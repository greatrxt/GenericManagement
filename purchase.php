<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'razorpay/autoload.php'; 
include 'razorpay/razorpay/razorpay/Razorpay.php';
use Razorpay\Api\Api;

//echo print_r($_POST);

$api_key = 'rzp_test_07TNBofP6ZZiKS';
$api_secret = '0hxG12Mho3ozkPwSQ7qiyGb3';


$api = new Api($api_key, $api_secret);

$payment = $api->payment->fetch($_POST['razorpay_payment_id']);

echo print_r($payment);
$payment->capture(array('amount' => $payment->amount));