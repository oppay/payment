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
	}



	public function send()
	{
		$this->gateway->sendRequest($this->amount, $this->orderId);
	}



	public function isReady()
	{
		# code...
	}



	public function getData()
	{
		# code...
	}



	public function redirect()
	{
		# code...
	}



	public function getError()
	{
		# code...
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
