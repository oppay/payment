<?php

$gateway = Payment::create('Saman');

$receipt = $gateway->receipt(1000, -16, '', '44654654');

$receipt->send();

if ($receipt->isSuccessful())
{
	$receipt->getData();

	$receipt->verify();

	
}
else
{
	$receipt->getError();
}
