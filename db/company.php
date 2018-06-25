<?php
/**
 * Description of company
 *
 * @author Rakshit
 */
class company {
    public function workspace_available($company_name){
        if(empty($company_name)){
            return "Company name cannot be empty";
        } 

        if (!$link = mysqli_connect('localhost', 'quanterp_dev1', 'o&HcDXUpg8?752yj*7')) {
            return 'Could not connect to mysql';
        }

        define("CPANEL_USERNAME", "quanterp");
        if (mysqli_select_db($link, CPANEL_USERNAME.'_'.$company_name)) {
            return 'exists';
        } 

        if (!$link_super = mysqli_connect('localhost', 'quanterp_super1', '^0e0g>KOZ^kK#4s_wc')) {
            return 'Could not connect to mysql';
        }

        if ($company_name == 'admin' || //admin already taken by this app
                mysqli_select_db($link_super, CPANEL_USERNAME.'_'.$company_name)) {
            return 'exists';
        } 

        return 'available';

    }
}
