<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of communication_utils
 *
 * @author Rakshit
 */
class communication_utils {
    /**
     * Send SMS
     * @param type $message
     * @param type $numbers
     * @return string
     */
    public function send_text_message($message, $numbers){
        // Authorisation details.
            $username = "1qubitindia@gmail.com";
            $hash = "abe602feef5764a02c1d09029ff24e7da9a4212d455fb443e092b67b9be01d1a";

            // Config variables. Consult http://api.textlocal.in/docs for more info.
            $test = "0";

            // Data for text message. This is the text message data.
            $sender = "iQUANT"; // This is who the message appears to be from.
            //$numbers = '91'.trim($_SESSION["contact"]); // A single number or a comma-seperated list of numbers

            //$message = 'Your Quant OTP is : '.$_SESSION["otp"];
            $message = urlencode($message);
            $data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$numbers."&test=".$test;        

            $ch = curl_init('http://api.textlocal.in/send/?');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch); // This is the result from the API
            curl_close($ch);

            if(json_decode($result, true)['status'] != 'success'){
                return "Error sending OTP";
                exit;
            }
    }
    
    /**
     * 
     * @param type $to
     * @param type $cc
     * @param type $bcc
     * @param type $subject
     * @param type $mail_body
     */
    public function send_mail($to, $cc, $bcc, $subject, $mail_body){
        $headers = "From: admin@quanterp.com\r\n";
        $headers .= "Cc: ".$cc."\r\n";
        $headers .= "Bcc: ".$bcc."\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html;charset=UTF-8\r\n";
        
        require ('Mail_signature.php');
        $mail_signature = new Mail_signature();
        $signature = $mail_signature->initialize();
        

        $signed_headers = $signature->get_signed_headers($to, $subject, $mail_body, $headers);
        mail($to, $subject, $mail_body, $signed_headers.$headers);
    }
}
