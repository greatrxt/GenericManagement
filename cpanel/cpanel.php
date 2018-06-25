<?php
//https://www.codepunker.com/blog/using-php-to-create-new-subdomains-databases-and-email-accounts-on-a-cpanel-driven-server

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require ('vendor/autoload.php');
require ('xmlapi.php');

class cpanel{
    public function create_workspace($workspace_name, $username, $password, $contact){
        if(empty($workspace_name)){
            return "Workspace name cannot be empty";
            exit;
        } 

        if(empty($username) || empty($password)){
            return "Username or password cannot be empty";
            exit;
        } 

        $database_postfix = $workspace_name;
        define("CPANEL_API_1", 1);
        define("CPANEL_API_2", 2);
        define("UAPI", 3);

        define("CPANEL_USERNAME", "quanterp");
        define("CPANEL_PASSWORD", "rVhth7nuBruo");


        if (!$link = mysqli_connect('localhost', 'quanterp_dev1', 'o&HcDXUpg8?752yj*7')) {
            return 'Could not connect to mysql';
            exit;
        }

        if (mysqli_select_db($link, CPANEL_USERNAME.'_'.$database_postfix)) {
            return 'exists';
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

        $backup = $cpanel->execute_action(
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

        $status = 'failed';
        if (mysqli_select_db($link, CPANEL_USERNAME.'_'.$database_postfix)) {
            $con = mysqli_connect("localhost","quanterp_dev1","o&HcDXUpg8?752yj*7",CPANEL_USERNAME.'_'.$database_postfix);
            $multi_sql = file_get_contents('https://'.$database_postfix.'.quanterp.com/Queries.txt');

            $query1 = "UPDATE owner_company SET oc_name = '".$database_postfix."', oc_subscription_start_date = CURDATE(), oc_subscription_end_date = DATE_ADD(curdate(), INTERVAL 30 DAY), oc_subscribed_modules = 'sales,purchase,inventory,orders,logistics,analytics,manufacturing,accounts,reports'; ";
            $query2 = "UPDATE employee SET employee_username = '".$username."', employee_password = '".password_hash($password, PASSWORD_DEFAULT, ['cost' => 12])."' WHERE employee_id = 1; ";
            $multi_sql = $multi_sql.$query1.$query2;
            if(mysqli_multi_query($con, $multi_sql)){
                $status = 'success';
            }
        }
        
        //enter details
        
        $this->enter_registration_details($workspace_name, $username, $contact, $status);
        return $status;

    }


    /**
     * 
     * @param type $workspace_name
     * @param type $username
     * @param type $contact
     * @param type $status
     */
    public function enter_registration_details($workspace_name, $username, $contact, $status){
        $con = mysqli_connect("localhost","quanterp_admin","-rbSc>OmFsH2fVdb&p", 'quanterp_admin');
        
        if(mysqli_query($con, "CREATE TABLE IF NOT EXISTS `registration` "
                        . "( `registration_id` INT NOT NULL AUTO_INCREMENT, "
                        . "`registration_workspace_name` TEXT,"
                        . "`registration_username` TEXT,"
                        . "`registration_contact` VARCHAR(255),"
                        . "`registration_status` VARCHAR(255),"
                        ."PRIMARY KEY (`registration_id`)"
                        . ") ENGINE = InnoDB COLLATE utf8_general_ci;")){
            
            $query = 'INSERT INTO registration ( registration_workspace_name, registration_username, registration_contact, registration_status ) '
                    . 'VALUES ("'.$workspace_name.'", "'.$username.'", "'.$contact.'", "'.$status.'"); ';
            
            mysqli_query($con, $query);
        }
        
        require ('db/communication_utils.php');
        $comm = new communication_utils();
        
        $message_body = 'Workspace https://'.$workspace_name. '.quanterp.com created using contact number - '.$contact;
        $comm->send_text_message($message_body, '9920154235');
        $comm->send_text_message($message_body, '9833507607');
        $comm->send_mail('1qubitindia@gmail.com', '', '', 'Workspace Created', $message_body);
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