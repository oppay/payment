<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<pre>
<?php

include('src/Payment/Payment.php');
include('src/Payment/Receipt.php');
include('src/Payment/Gateway/Gateway.php');
include('src/Payment/Gateway/GatewayInterface.php');
include('src/Payment/Gateway/Saman/Saman.php');
include('src/Payment/Gateway/Mellat/Mellat.php');
include('src/Payment/Purchase.php');


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

$receipt = $gateway->receipt();
//$receipt = $gateway->capture();

if ($receipt->isOk())
{
	var_dump($receipt->getData());

	//shomare kharid bayad yekta bashe && bablagh bayad barabar bashe

	if ($receipt->verify())
	{
		//ok
	}
	else
	{
		print_r($receipt->getError());
		//$receipt->reverse();
	}


}
else
{
	print_r($receipt->getError());
}
