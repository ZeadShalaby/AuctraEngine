<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
</head>
<body>

<h3>Redirecting to payment...</h3>

<x-moamalat-pay 
    :amount="$payment->amount"
    :reference="$payment->merchant_ref"
/>


</body>
</html>