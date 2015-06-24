<?php

include('src/Payment/PaymentFactory.php');
include('src/Payment/Gateway.php');
include('src/Payment/GatewayInterface.php');
include('src/Payment/Gateway/Saman/Saman.php');
include('src/Payment/Gateway/Mellat/Mellat.php');
include('src/Payment/Purchase.php');


$db = new PDO('mysql:host=127.0.0.1;port=3306;dbname=payment', 'root', '', array( PDO::ATTR_PERSISTENT => false));

$gatewayName = 'Saman';

$order = $stmt = $db->prepare("INSERT INTO purchase SET gateway = '$gatewayName', amount = 100");
$stmt->execute();
$orderId = $db->lastInsertId(); 


$gateway = Payment\PaymentFactory::create('Saman', [
	'terminalId'  => 21056352,
	'callbackUrl' => 'http://2.182.224.73/Payment/back.php',
]);

/*
$gateway = Payment\PaymentFactory::create($gatewayName, [
	'terminalId'  => 802802,
	'userName' => 'rahahost',
	"userPassword"   => 'ra94ha',
	'callbackUrl' => 'http://2.182.224.73/Payment/back.php',
]);*/



$purchase = $gateway->purchase(100, $orderId);

if ($purchase->send())
{
	$token = $purchase->getToken();

	//
	$order = $stmt = $db->prepare("UPDATE purchase SET token = '$token', requestCode = '$token' WHERE id = $orderId");
	$stmt->execute();
	//

	$purchase->redirect();
}
else
{
	$error = $purchase->getError();

	//
	$order = $stmt = $db->prepare("UPDATE purchase SET requestCode = '$error[code]' WHERE id = $orderId");
	$stmt->execute();
	//

	print_r($error);
}
