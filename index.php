<?php

include('src/Payment/Payment.php');
include('src/Payment/Gateway.php');
include('src/Payment/GatewayInterface.php');
include('src/Payment/Gateway/Saman/Saman.php');




$gateway = Payment\Payment::create('Saman');

$purchse = $gateway->purchse(1000, -16);

$purchse->send();

if ($purchse->isReady())
{
	$purchse->getData();

	$purchse->redirect();
}
else
{
	$purchse->getError();
}
