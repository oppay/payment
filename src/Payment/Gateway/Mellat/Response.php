<?php

namespace Qlake\Payment\Mellat;

use Qlake\Payment\Gateway;
use Qlake\Payment\GatewayInterface;

use SoapClient;
use SoapFault;

class Response
{
	public function __construct(Mellat $gateway, $result)
	{
		$this->gateway = $gateway;

		$this->requestResult = $result;
	}



	public function isSuccessful()
	{
		$result = $this->requestResult;

		if ($result instanceof SoapFault)
		{
			$this->requestError = $result;

			return false;
		}

		$result = explode(',', $result->return);

		if ($result[0] !== '0')
		{
			$this->requestError = $result[0];

			return false;
		}

		$this->token = $result[1];

		$this->requestData = $result[1];

		return true;
	}



	public function getRequestData()
	{
		return $this->requestData;
	}



	public function getRequestError()
	{
		return $this->requestError;
	}



	public function redirect()
	{
		$this->redirectByForm($this->paymentUrl, ['RefId' => $this->token]);
	}



	public function verify()
	{
		$params = [
			'RefNum' => '',
			'MID' => '',
		];

		$res = $this->client->__soapCall('VerifyTransaction', $params);

		if ($res < 0)
		{
			echo "Error: $res";
			exit;
		}
		if ($res != $this->amount)
		{
			$res = $this->client->__soapCall('reverseTransaction', ['RefNum' => $RefNum, 'MID' => $this->username, 'Username' => $this->username, 'Password' => $this->password]);
			if ($res == 1)
			{
				echo 'Transaction reversed';
				exit;
			}
			else
			{
				echo 'error';
				exit;
			}
		}
		return true;
	}




}
