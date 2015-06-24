<?php

include('src/Payment/Payment.php');
include('src/Payment/Gateway.php');
include('src/Payment/GatewayInterface.php');
include('src/Payment/Gateway/Saman/Saman.php');
include('src/Payment/Purchase.php');

$gateway = Payment\Payment::create('Saman');

$receipt = $gateway->receipt(1000, -16, '', '44654654');

if ($receipt->send())
{
	$receipt->getData();

	$receipt->verify();


}
else
{
	$receipt->getError();
}
