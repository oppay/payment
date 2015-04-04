<?php

namespace Payment;

class Gateway
{
	protected function redirectByForm($url, array $params)
	{
		$elements = '';

		foreach ($params as $name => $value)
		{
			$elements .= "<input type=\"hidden\" value=\"{$value}\" name=\"{$name}\" />";
		}

		include __DIR__ . '/form.html';
	}



	public function getAmount()
	{
		return $this->amount;
	}



	public function getReceiptId()
	{
		return $this->receiptId;
	}



	public function getToken()
	{
		return $this->token;
	}



	public function getTransactionId()
	{
		return $this->transactionId;
	}



	public function __get($name)
	{
		if ($name === 'client')
		{
			$this->client = new \SoapClient($this->wsdlUrl, ['exceptions' => false, 'encoding' => 'UTF-8']);
		}

		return $this->client;
	}
}
