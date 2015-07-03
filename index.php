<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
rtr
<?php

include('src/Payment/Payment.php');
include('src/Payment/Gateway/Gateway.php');
include('src/Payment/Gateway/GatewayInterface.php');
include('src/Payment/Gateway/Saman/Saman.php');
include('src/Payment/Gateway/Mellat/Mellat.php');
include('src/Payment/Purchase.php');


$db = new PDO('mysql:host=127.0.0.1;port=3306;dbname=payment', 'root', '', array( PDO::ATTR_PERSISTENT => false));

$gatewayName = 'Mellat';

$order = $stmt = $db->prepare("INSERT INTO purchase SET gateway = '$gatewayName', amount = 1000");
$stmt->execute();
$orderId = $db->lastInsertId(); 


$gateway = Payment\Payment::create('Saman', [
	'terminalId'  => 21056352,
	'callbackUrl' => 'http://2.182.224.75/Payment/back.php',
]);


$gateway = Payment\Payment::create('Mellat', [
	'terminalId'  => 802802,
	'userName' => 'rahahost',
	"userPassword"   => 'ra94ha',
	'callbackUrl' => 'http://2.182.224.75/Payment/back.php',
]);
/**/


$purchase = $gateway->purchase(1000, $orderId);

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
