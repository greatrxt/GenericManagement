<?php

if(empty($_POST["company_name"])){
    echo "Company name cannot be empty";
    exit;
} 

if (!$link = mysqli_connect('localhost', 'quanterp_dev1', 'o&HcDXUpg8?752yj*7')) {
    echo 'Could not connect to mysql';
    exit;
}

define("CPANEL_USERNAME", "quanterp");
if (mysqli_select_db($link, CPANEL_USERNAME.'_'.$_POST["company_name"])) {
    echo 'exists';
    exit;
} 

if (!$link_super = mysqli_connect('localhost', 'quanterp_super1', '^0e0g>KOZ^kK#4s_wc')) {
    echo 'Could not connect to mysql';
    exit;
}

if (mysqli_select_db($link_super, CPANEL_USERNAME.'_'.$_POST["company_name"])) {
    echo 'exists';
    exit;
} 

echo 'available';


?>