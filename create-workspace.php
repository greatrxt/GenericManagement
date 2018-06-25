<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
        
    if(!session_start()){
        echo 'Failed to start session';
        exit;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(empty($_SESSION["workspace"]) 
                && empty($_SESSION["username"]) 
                && empty($_SESSION["password"]) 
                && empty($_SESSION["contact"]) 
                && empty($_SESSION["otp"])){

            require 'db/company.php';
            require 'db/communication_utils.php';

            if(empty($_POST["workspace_name"])){
                echo "Workspace name cannot be empty";
                exit;
            } 

            if(empty($_POST["username"]) || empty($_POST["password"])){
                echo "Username or password cannot be empty";
                exit;
            } 

            $company = new company();
            if(strcmp($company->workspace_available($_POST["workspace_name"]), 'available')){
                echo $_POST["workspace_name"]." workspace already exists";
                exit;
            }

            $_SESSION["workspace"] = $_POST["workspace_name"];
            $_SESSION["username"] = $_POST["username"];
            $_SESSION["password"] = $_POST["password"];
            $_SESSION["contact"] = $_POST["contact"];
            $_SESSION["otp"] = rand(1111, 9998);
            $message = 'Your Quant OTP is : '.$_SESSION["otp"];
            
            $comm = new communication_utils();
            $comm->send_text_message($message, $_SESSION["contact"]);
            
            //print_r($_SESSION);
            echo 'success';
        } else if(!empty($_SESSION["otp"] 
                && !empty($_POST["otp"]))) {
            require ('cpanel/cpanel.php');
            if(trim($_SESSION["otp"]) == $_POST["otp"]){
                $cpanel = new cpanel();
                echo $cpanel->create_workspace($_SESSION["workspace"], $_SESSION["username"], $_SESSION["password"], $_SESSION["contact"]);
            } else {
                echo "You have entered an invalid OTP";
            }
        } 
        
        exit;
    } else if(empty($_SESSION["workspace"]) 
                || empty($_SESSION["username"]) 
                || empty($_SESSION["password"]) 
                || empty($_SESSION["contact"]) 
                || empty($_SESSION["otp"])){
        header("Location: https://quanterp.com");
        die();
    }
    
//    if(isset($_SESSION["otp"]))
  //      echo '<!--'.$_SESSION["otp"].'-->';
    
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
                        <p class="fs-20 opacity-70">
                            
                        </p>

                    </div>
                </div>

            </div>
        </header>
        <!-- END Header -->
        <!-- Main container -->
        <main class="main-content">
            <section class="section">
                <div class="container">
                    <header class="section-header">
                        <span class ="lead" id="serverMessage" style = "color: black">Please enter the OTP sent to you on your registered mobile number.</span>
                    </header>
                        <div class="form-group center-block" style ="margin-left:30%;">
                        <input maxlength = "4" onkeypress='return event.charCode >= 48 && event.charCode <= 57' id ="otp_text"
                                class="col-7 form-control text-center" style="color: black;letter-spacing: 1em; padding: 10px;" type="text" placeholder="Enter OTP">
                        <br> 
                        </div>
                        <p class="text-center">   
                            <button onclick="createWorkspaceUrl()" id = "buttonCreateUrl" class="btn btn-xl btn-primary w-270" type="submit">Continue</button><br>
                            <small>3, 2, 1, Go !</small>
                        </p>
                
            </section>
            <script>
                function createWorkspaceUrl(){
                    $('#serverMessage').css('color', 'black');
                    //$('#serverMessage').html("Please enter One Time Password sent to your mobile phone.");
                    $('#buttonCreateUrl').prop('disabled', true);

                    var otp = $("#otp_text").val().trim();
                    if(otp.length!=4){
                        $('#serverMessage').css('color', 'red'); 
                        $('#serverMessage').html('Please enter a valid OTP');
                        $('#buttonCreateUrl').prop('disabled', false);
                        return;
                    }
                    $.post("create-workspace.php", {otp: otp}, function(result){
                        if(result == 'success'){
                            setTimeout(new function(){
                                window.location = 'https://<?php echo $_SESSION["workspace"]; ?>.quanterp.com';
                            }, 10000);
                        } else {
                           $('#serverMessage').css('color', 'red'); 
                           $('#serverMessage').html(result);
                           $('#buttonCreateUrl').prop('disabled', false);
                        }
                    });
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

    
