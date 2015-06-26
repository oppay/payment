<?php

include('src/Payment/Payment.php');
include('src/Payment/Gateway.php');
include('src/Payment/GatewayInterface.php');
include('src/Payment/Gateway/Saman/Saman.php');
include('src/Payment/Purchase.php');

$gateway = Payment\Payment::create('Saman');

$receipt = $gateway->receipt(1000, -16, '', '44654654');

if ($receipt->isOk())
{
	$receipt->getData();

	//search RefNum in db
	// if is unique

	if ($receipt->verify())
	{
		//ok
	}
	else
	{
		$receipt->getError();
		$receipt->reverse();
	}


}
else
{
	$receipt->getError();
}
