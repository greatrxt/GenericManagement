<?php
?>

<form action="/purchase.php" method="POST">
<!-- Note that the amount is in paise = 50 INR -->
<script
    src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="rzp_test_07TNBofP6ZZiKS"
    data-amount="5000"
    data-buttontext="Pay 50"
    data-name="1Qubit Technologies"
    data-description="Recharge your account"
    data-image="assets/img/logo_light.png"
    data-prefill.name="Harshil Mathur"
    data-prefill.email="support@razorpay.com"
    data-order_id="order_APuCkVRHWxanXL"
    data-theme.color="#528ff0"
></script>
<input type="hidden" value="Hidden Element" name="hidden">
</form>
<form action="/purchase.php" method="POST">
<!-- Note that the amount is in paise = 50 INR -->
<script
    src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="rzp_test_07TNBofP6ZZiKS"
    data-amount="500000"
    data-buttontext="Pay 5000"
    data-name="1Qubit Technologies"
    data-description="Recharge your account"
    data-image="assets/img/logo_light.png"
    data-prefill.name="Harshil Mathur"
    data-prefill.email="support@razorpay.com"
    data-theme.color="#528ff0"
></script>
<input type="hidden" value="Hidden Element" name="hidden">
</form>
<style>
    .razorpay-payment-button {
        
    }
</style>