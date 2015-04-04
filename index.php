<?php

include('src/Payment/Payment.php');
include('src/Payment/Gateway.php');
include('src/Payment/GatewayInterface.php');
include('src/Payment/Gateway/Saman/Saman.php');
include('src/Payment/Purchase.php');


$db = new PDO('mysql:host=127.0.0.1;port=3306;dbname=payment', 'root', '', array( PDO::ATTR_PERSISTENT => false));

$order = $stmt = $db->prepare("INSERT INTO purchase SET gateway = 'Saman', amount = 1000");
$stmt->execute();
$orderId = $db->lastInsertId(); 

//$gateway = Payment\Payment::create('Saman');

$purchase = new Payment\Purchase(1000, $orderId);

$purchase->send();

if ($purchase->isReady())
{
	$token = $purchase->getToken();

	$order = $stmt = $db->prepare("UPDATE purchase SET token = '$token' WHERE id = $orderId");
	$stmt->execute();

	$purchase->redirect();
}
else
{
	$error = $purchase->getError();

	var_dump($error);
}
