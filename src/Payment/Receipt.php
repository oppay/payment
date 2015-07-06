<?php

namespace Payment;

use Payment\Gateway\Gateway;
use Payment\Gateway\GatewayInterface;

class Receipt
{
	protected $gateway;



	public function __construct(GatewayInterface $gateway)
	{
		$this->gateway = $gateway;
		
		$this->gateway->captureResponse();
	}



	public function isOk()
	{
		return $this->gateway->isResponseOk();
	}



	public function getData()
	{
		return $this->gateway->getResponseData();
	}



	public function getError()
	{
		return $this->gateway->getResponseError();
	}



	public function getErrorCode()
	{
		return $this->gateway->getResponseError()['code'];
	}



	public function getErrorMessage()
	{
		return $this->gateway->getResponseError()['message'];
	}



	public function verify()
	{
		return $this->gateway->verify($_POST['SaleOrderId'] + 0, $_POST['SaleReferenceId'] + 0);
	}
}
