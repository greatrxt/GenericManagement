<?php

    require 'company.php';
    
    $company = new company();
    echo $company->workspace_available($_POST["company_name"]);

?>