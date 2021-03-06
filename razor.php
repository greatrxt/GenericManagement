<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'razorpay/autoload.php'; 
include 'razorpay/razorpay/razorpay/Razorpay.php';
use Razorpay\Api\Api;

$api_key = 'rzp_test_07TNBofP6ZZiKS';
$api_secret = '0hxG12Mho3ozkPwSQ7qiyGb3';


$api = new Api($api_key, $api_secret);

// Orders
$order  = $api->order->create(array('receipt' => '123', 'amount' => 100, 'currency' => 'INR')); // Creates order
$order  = $api->order->fetch($orderId); // Returns a particular order
$orders = $api->order->all($options); // Returns array of order objects
$payments = $api->order->fetch($orderId)->payments(); // Returns array of payment objects against an order

// Payments
$payments = $api->payment->all($options); // Returns array of payment objects
$payment  = $api->payment->fetch($id); // Returns a particular payment
$payment  = $api->payment->fetch($id)->capture(array('amount'=>$amount)); // Captures a payment

// To get the payment details
echo $payment->amount;
echo $payment->currency;
// And so on for other attributes

// Refunds
$refund = $api->refund->create(array('payment_id' => $id)); // Creates refund for a payment
$refund = $api->refund->create(array('payment_id' => $id, 'amount'=>$refundAmount)); // Creates partial refund for a payment
$refund = $api->refund->fetch($refundId); // Returns a particular refund

// Cards
$card = $api->card->fetch($cardId); // Returns a particular card

// Customers
$customer = $api->customer->create(array('name' => 'Razorpay User', 'email' => 'customer@razorpay.com')); // Creates customer
$customer = $api->customer->fetch($customerId); // Returns a particular customer
$customer = $api->customer->edit(array('name' => 'Razorpay User', 'email' => 'customer@razorpay.com')); // Edits customer

// Tokens
$token  = $api->customer->token()->fetch($tokenId); // Returns a particular token
$tokens = $api->customer->token()->all($options); // Returns array of token objects
$api->customer->token()->delete($tokenId); // Deletes a token


// Transfers
$transfer  = $api->payment->fetch($paymentId)->transfer(array('transfers' => [ ['account' => $accountId, 'amount' => 100, 'currency' => 'INR']])); // Create transfer
$transfers = $api->transfer->all(); // Fetch all transfers
$transfers = $api->payment->fetch($paymentId)->transfers(); // Fetch all transfers created on a payment
$transfer  = $api->transfer->fetch($transferId)->edit($options); // Edit a transfer
$reversal  = $api->transfer->fetch($transferId)->reverse(); // Reverse a transfer

// Payment Links
$links = $api->all();
$link  = $api->fetch('inv_00000000000001');
$link  = $api->invoice->create(array('type' => 'link', 'amount' => 500, 'description' => 'For XYZ purpose', 'customer' => array('email' => 'test@test.test')));
$link->cancel();
$link->notifyBy('sms');

// Invoices
$invoices = $api->all();
$invoice  = $api->fetch('inv_00000000000001');
$invoice  = $api->invoice->create($params); // Ref: razorpay.com/docs/invoices for request params example
$invoice  = $invoice->edit($params);
$invoice->issue();
$invoice->notifyBy('email');
$invoice->cancel();
$invoice->delete();

// Virtual Accounts
$virtualAccount  = $api->virtualAccount->create(array('receiver_types' => array('bank_account'), 'description' => 'First Virtual Account', 'notes' => array('receiver_key' => 'receiver_value')));
$virtualAccounts = $api->virtualAccount->all();
$virtualAccount  = $api->virtualAccount->fetch('va_4xbQrmEoA5WJ0G');
$virtualAccount  = $virtualAccount->close();
$payments        = $virtualAccount->payments();
$bankTransfer    = $api->payment->fetch('pay_8JpVEWsoNPKdQh')->bankTransfer();

// Subscriptions
$plan          = $api->plan->create(array('period' => 'weekly', 'interval' => 1, 'item' => array('name' => 'Test Weekly 1 plan', 'description' => 'Description for the weekly 1 plan', 'amount' => 600, 'currency' => 'INR')));
$plan          = $api->plan->fetch('plan_7wAosPWtrkhqZw');
$plans         = $api->plan->all();
$subscription  = $api->subscription->create(array('plan_id' => 'plan_7wAosPWtrkhqZw', 'customer_notify' => 1, 'total_count' => 6, 'start_at' => 1495995837, 'addons' => array(array('item' => array('name' => 'Delivery charges', 'amount' => 30000, 'currency' => 'INR')))));
$subscription  = $api->subscription->fetch('sub_82uBGfpFK47AlA');
$subscriptions = $api->subscription->all();
$subscription  = $api->subscription->fetch('sub_82uBGfpFK47AlA')->cancel();
$addon         = $api->subscription->fetch('sub_82uBGfpFK47AlA')->createAddon(array('item' => array('name' => 'Extra Chair', 'amount' => 30000, 'currency' => 'INR'), 'quantity' => 2));
$addon         = $api->addon->fetch('ao_8nDvQYYGQI5o4H');
$addon         = $api->addon->fetch('ao_8nDvQYYGQI5o4H')->delete();