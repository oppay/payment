<?php

include('src/Payment/Payment.php');
include('src/Payment/Gateway.php');
include('src/Payment/GatewayInterface.php');
include('src/Payment/Gateway/Saman/Saman.php');
include('src/Payment/Gateway/Mellat/Mellat.php');
include('src/Payment/Purchase.php');


$db = new PDO('mysql:host=127.0.0.1;port=3306;dbname=payment', 'root', '', array( PDO::ATTR_PERSISTENT => false));

$order = $stmt = $db->prepare("INSERT INTO purchase SET gateway = 'Saman', amount = 100");
$stmt->execute();
$orderId = $db->lastInsertId(); 

//$gateway = Payment\Payment::create('Saman');

$purchase = new Payment\Purchase(100, $orderId);

$purchase->send();

if ($purchase->isReady())
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
	$order = $stmt = $db->prepare("UPDATE purchase SET requestCode = '$error' WHERE id = $orderId");
	$stmt->execute();
	//

	var_dump($error);
}
