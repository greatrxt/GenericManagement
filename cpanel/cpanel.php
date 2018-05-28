<?php
//https://www.codepunker.com/blog/using-php-to-create-new-subdomains-databases-and-email-accounts-on-a-cpanel-driven-server

error_reporting(E_ALL);
ini_set('display_errors', 1);

require ('vendor/autoload.php');
require ('xmlapi.php');


if(empty($_POST["workspace_name"])){
    echo "Workspace name cannot be empty";
    exit;
} 

if(empty($_POST["username"]) || empty($_POST["password"])){
    echo "Username or password cannot be empty";
    exit;
} 

$database_postfix = $_POST["workspace_name"];
define("CPANEL_API_1", 1);
define("CPANEL_API_2", 2);
define("UAPI", 3);

define("CPANEL_USERNAME", "quanterp");
define("CPANEL_PASSWORD", "rVhth7nuBruo");


if (!$link = mysqli_connect('localhost', 'quanterp_dev1', 'o&HcDXUpg8?752yj*7')) {
    echo 'Could not connect to mysql';
    exit;
}

if (mysqli_select_db($link, CPANEL_USERNAME.'_'.$database_postfix)) {
    echo 'exists';
    exit;
}


$cpanel = new \Gufy\CpanelPhp\Cpanel([
        'host'        =>  'https://quanterp.com:2083',
        'username'    => CPANEL_USERNAME,
        'auth_type'   =>  'password', // there is also an option to use "hash"
        'password'    =>  CPANEL_PASSWORD, // if you use hash, get the value from WHM's Remote Access Key if not use the root password here
    ]);

$data = $cpanel->execute_action(UAPI, 'Mysql', 'create_database', CPANEL_USERNAME, ['name'=>CPANEL_USERNAME.'_'.$database_postfix]);//grant everything

$grant = $cpanel->execute_action(
        UAPI, 
        'Mysql', 
        'set_privileges_on_database', 
        CPANEL_USERNAME, 
        [
            'user'=>'quanterp_dev1', 
            'database'=>CPANEL_USERNAME.'_'.$database_postfix, 
            'privileges'=>'ALL PRIVILEGES'
        ]
    );

$backcup = $cpanel->execute_action(
        UAPI, 
        'Mysql', 
        'set_privileges_on_database', 
        CPANEL_USERNAME, 
        [
            'user'=>'quanterp_drpmyst', 
            'database'=>CPANEL_USERNAME.'_'.$database_postfix, 
            'privileges'=>'ALL PRIVILEGES'
        ]
    );
 
if (mysqli_select_db($link, CPANEL_USERNAME.'_'.$database_postfix)) {
    $con = mysqli_connect("localhost","quanterp_dev1","o&HcDXUpg8?752yj*7",CPANEL_USERNAME.'_'.$database_postfix);
    $multi_sql = file_get_contents('https://'.$database_postfix.'.quanterp.com/Queries.txt');
    
    $query1 = "UPDATE owner_company SET oc_name = '".$database_postfix."', oc_subscription_start_date = CURDATE(), oc_subscription_end_date = DATE_ADD(curdate(), INTERVAL 1 YEAR), oc_subscribed_modules = 'sales,purchase,inventory,orders,logistics,analytics,manufacturing,accounts,reports'; ";
    $query2 = "UPDATE employee SET employee_username = '".$_POST["username"]."', employee_password = '".password_hash($_POST["password"], PASSWORD_DEFAULT, ['cost' => 12])."' WHERE employee_id = 1; ";
    $multi_sql = $multi_sql.$query1.$query2;
    if(mysqli_multi_query($con, $multi_sql)){
        echo 'success';
    } else {
        echo 'failed';
    }
}

/*
    $accounts = $cpanel->listaccts();
    print_r($accounts);*/
    
    /*$cpanelusr = 'quanterp';
    $cpanelpass = 'rVhth7nuBruo';
    $xmlapi2 = new xmlapi('quanterp.com');
    $xmlapi2->set_port(2083);
    $xmlapi2->password_auth($cpanelusr,$cpanelpass);
    $xmlapi2->set_debug(0); //output actions in the error log 1 for true and 0 false 

    //the actual $databasename and $databaseuser will contain the cpanel prefix for a particular account. Ex: prefix_dbname and prefix_dbuser
    $databasename = 'test2';
    $databaseuser = 'dev1'; //be careful this can only have a maximum of 7 characters
    $databasepass = 'passwordforthenewuser';

    $createdb = $xmlapi2->api1_query($cpanelusr, "Mysql", "adddb", array($cpanelusr."_".$databasename)); //creates the database
    //$usr = $xmlapi2->api1_query($cpanelusr, "Mysql", "adduser", array($databaseuser, $databasepass)); //creates the user
    $backupusr = $xmlapi2->api1_query($cpanelusr, "Mysql", "adduserdb", array($cpanelusr."_".$databasename, 'quanterp_drpmyst', 'all')); //gives all privileges to the newly created user on the new db
    $addusr = $xmlapi2->api1_query($cpanelusr, "Mysql", "adduserdb", array($cpanelusr."_".$databasename, $cpanelusr."_".$databaseuser, 'all')); //gives all privileges to the newly created user on the new db
*/
    
    
?>