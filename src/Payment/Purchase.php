<?php

namespace Payment;

class Purchase
{
	protected $amount;


	protected $orderId;


	protected $gateway;



	public function __construct($amount, $orderId)
	{
		$this->amount  = (int)$amount;
		$this->orderId = $orderId;

		$this->gateway = new Gateway\Saman\Saman([
			'terminalId' => 21056352,
			'callbackUrl' => 'http://2.182.224.73/Payment/back.php',
			'amount' => $this->amount,
			'receiptId' => $this->orderId,
		]);

		/*$this->gateway = new Gateway\Mellat\Mellat([
			'terminalId' => 802802,
			'userName' => 'rahahost',
			'userPassword' => 'ra94ha',
			'callbackUrl' => 'http://2.182.224.73/Payment/back.php',
			'amount' => $this->amount,
			'receiptId' => $this->orderId,
		]);*/
	}



	public function send()
	{
		$this->gateway->send();
	}



	public function isReady()
	{
		return $this->gateway->isReady();
	}



	public function getData()
	{
		return $this->gateway->getRequestData();
	}



	public function getToken()
	{
		return $this->gateway->getToken();
	}



	public function redirect()
	{
		return $this->gateway->redirect();
	}



	public function getError()
	{
		return $this->gateway->getRequestError();
	}



	/*public function send()
	{
		# code...
	}



	public function send()
	{
		# code...
	}



	public function send()
	{
		# code...
	}



	public function send()
	{
		# code...
	}*/
}
