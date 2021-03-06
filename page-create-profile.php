<?php
    
    if(!session_start()){
        echo 'Failed to start session';
        exit;
    }
    
    session_unset();
    session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
                        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-105838220-2"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'UA-105838220-2');
        </script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
        <title>Quant</title>

        <!-- Styles -->
        <link href="assets/css/core.min.css" rel="stylesheet">
        <link href="assets/css/thesaas.min.css" rel="stylesheet">
        <link href="assets/css/style.css" rel="stylesheet">

        <!-- Favicons -->
        <link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
        <link rel="icon" href="assets/img/logo_icon_light.png">
    </head>
    
    <body>

        <!-- Topbar -->
        <nav class="topbar topbar-sticky">
            <div class="container">

                <div class="topbar-left">
                    <a class="topbar-brand" href="index.html">
                        <img class="logo-default" src="assets/img/logo_icon_light.png" alt="logo">
                        <img class="logo-inverse" src="assets/img/logo_icon_light.png" alt="logo">
                    </a>
                </div>


                <div class="topbar-right">
                    <a class="btn btn-sm btn-danger mr-4" href="page-login.html">Login</a>
                    <!--<a class="btn btn-sm btn-outline btn-danger hidden-sm-down" href="page-register.html">Sign up</a>-->

                    <button class="drawer-toggler ml-12">&#9776;</button>
                </div>

            </div>
        </nav>
        <!-- END Topbar -->

        <!-- Header -->
        <header class="header header" style="background-image: url(assets/img/bg-pattern_1.png);background-size: contain;background-repeat: repeat;">
            <div class="container text-center">

                <div class="row">
                    <div class="col-12 col-lg-8 offset-lg-2">

                        <h1 id = "workspace"></h1>
                        <p class="fs-20 opacity-70"></p>

                    </div>
                </div>

            </div>
        </header>
        <!-- END Header -->
        <!-- Main container -->
        <main class="main-content">
            <!--
            |??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
            | Request form
            |??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
            !-->
            <section class="section">
                <div class="container">
                    <header class="section-header">
                        <span class ="lead" id="serverMessage" style = "color: black;font-weight: 600;">Please enter a valid username. <br>You'll be using this username/password to log into the system.</span>
                    </header>
                        <div class="form-group center-block" style ="margin-left:30%;">
                        <input onkeypress='return event.charCode >= 48 && event.charCode <= 57 || ((event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode == 8)'
                                class="col-7 form-control" maxlength="20" style="color: black;" type="text" id="username" placeholder="Enter Username">
                        <br>
                        <input type ="password"
                            onkeypress='return event.charCode >= 48 && event.charCode <= 57 || ((event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode == 8)'
                               class="col-7 form-control" maxlength="20" style="color: black;" id="password" placeholder="Enter Password">
                        <br>
                        <input type ="password"
                            onkeypress='return event.charCode >= 48 && event.charCode <= 57 || ((event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode == 8)'
                                class="col-7 form-control" maxlength="20" style="color: black;" id="confirmPassword" placeholder="Confirm Password">
                        <br>
                        <input type ="text" id ="contact" maxlength="10"
                            onkeypress='return event.charCode >= 48 && event.charCode <= 57'
                                class="col-7 form-control"  style="color: black;" placeholder="Enter Your Contact Number">
                        </div> 
                        <br> 
                        <p class="text-center">   
                            <button onclick="createProfile()" id = "buttonCreateUrl" class="btn btn-xl btn-primary w-270" type="submit">Continue</button><br>
                            <small>Send OTP to contact number</small>
                        </p>
                </div>
            </section>
            <script>
                function createProfile(){
                    $('#serverMessage').css('color', 'black');
                    $('#serverMessage').html("Please enter a valid username. <br>You'll be using this username/password to log into the system.");
                    $('#buttonCreateUrl').prop('disabled', true);
                    var name = getParameterByName('workspace');
                    var username = $("#username").val().trim();
                    var password = $("#password").val().trim();
                    var confirmPassword = $("#confirmPassword").val().trim();
                    
                    var contact = $("#contact").val().trim();
                    
                    if(password!=confirmPassword){
                        $('#serverMessage').css('color', 'red'); 
                        $('#serverMessage').html('Passwords do not match');
                        $('#buttonCreateUrl').prop('disabled', false);
                        return;
                    }
                    
                    if(contact.length!=10){
                        $('#serverMessage').css('color', 'red'); 
                        $('#serverMessage').html('Please enter a valid 10 digit mobile number');
                        $('#buttonCreateUrl').prop('disabled', false);
                        return;
                    }
                    
                    $.post("create-workspace.php", {username: username, password:password, workspace_name:name, contact:contact}, function(result){
                        if(result == 'success'){
                            window.location = 'create-workspace.php';
                            return;
                        } else {
                           $('#serverMessage').css('color', 'red'); 
                           $('#serverMessage').html(result);
                           $('#buttonCreateUrl').prop('disabled', false);
                        }
                    });
                }
                
                function checkIfCompanyExists(){
                    var name = getParameterByName('workspace');                    
                    if(name && name.trim() != ''){
                        $.post("/db/companyexists.php", {company_name: name}, function(result){
                            if(result != 'available'){
                                window.location = 'page-confirm-url.html';
                            } else {
                                $('#workspace').html('Enter details for workspace<br>' + name + '.quanterp.com');
                            }
                        });
                    } else {
                        window.location = 'page-confirm-url.html';
                    }
                }
                           
                checkIfCompanyExists();
                
                function getParameterByName(name, url) {
                    if (!url) url = window.location.href;
                    name = name.replace(/[\[\]]/g, "\\$&");
                    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                        results = regex.exec(url);
                    if (!results) return null;
                    if (!results[2]) return '';
                    return decodeURIComponent(results[2].replace(/\+/g, " "));
                }
            </script>
        </main>
        <!-- END Main container -->




        <!-- Footer -->
        <footer class="site-footer">
            <div class="container">
                <div class="row gap-y align-items-center">
                    <div class="col-12 col-lg-3">
                        <p class="text-center text-lg-left">
                            <a href="index.html"><img src="assets/img/logo_icon_light.png" alt="logo"></a>
                        </p>
                    </div>

                    <div class="col-12 col-lg-6">
                        <ul class="nav nav-primary nav-hero">
                            <li class="nav-item">
                                <a class="nav-link" href="index.html#top">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.html#features">Features</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.html#pricing">Pricing</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="page-support.html">Support</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="page-contact.html">Contact</a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-12 col-lg-3">
                        <div class="social text-center text-lg-right">
                            <a class="social-facebook" href="#"><i class="fa fa-facebook"></i></a>
                            <a class="social-twitter" href="#"><i class="fa fa-twitter"></i></a>
                            <a class="social-instagram" href="#"><i class="fa fa-instagram"></i></a>
                            <a class="social-whatsapp" href="#"><i class="fa fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- END Footer -->



        <!-- Drawer -->
        <div class="drawer">
            <div class="drawer-content">
                <ul class="nav nav-primary nav-hero flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="page-demo.html">Schedule a Free Demo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.html#faq-section">Frequently Asked Questions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Privacy Policy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="page-support.html">Support</a>
                    </li> 
                </ul>

                <br>

                <ul class="nav nav-primary flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html#top">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.html#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.html#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="page-support.html">Support</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="page-contact.html">Contact</a>
                    </li>
                </ul>

                <hr>

                <div class="social social-boxed social-rounded text-center">
                    <a class="social-facebook" href="#"><i class="fa fa-facebook"></i></a>
                    <a class="social-twitter" href="#"><i class="fa fa-twitter"></i></a>
                    <a class="social-instagram" href="#"><i class="fa fa-instagram"></i></a>
                    <a class="social-whatsapp" href="#"><i class="fa fa-whatsapp"></i></a>
                </div>

                <br>

                <div class="row">
                    <div class="col-12">
                        <a class="btn btn-sm btn-block btn-danger" href="https://demo.quanterp.com">Login</a>
                    </div>
                </div>

            </div>

            <button class="drawer-close"></button>
            <div class="drawer-backdrop"></div>
        </div>
        <!-- END Drawer -->



        <!-- Scripts -->
        <script src="assets/js/core.min.js"></script>
        <script src="assets/js/thesaas.min.js"></script>
        <script src="assets/js/script.js"></script>

    </body>
</html>
