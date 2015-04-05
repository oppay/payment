<?php

namespace Payment\Gateway\Saman;

use Payment\Gateway;
use Payment\GatewayInterface;

use SoapClient;
use SoapFault;

class Saman extends Gateway implements GatewayInterface
{
	protected $wsdlUrl = 'https://sep.shaparak.ir/Payments/InitPayment.asmx?wsdl';


	protected $paymentUrl = 'https://sep.shaparak.ir/Payment.aspx';


	protected $terminalId;


	protected $amount;


	protected $receiptId;


	protected $token;


	//protected $client;


	protected $requestData;


	protected $requestError;



	public function __construct(array $params)
	{
		$this->terminalId  = $params['terminalId'];
		$this->callbackUrl = $params['callbackUrl'];
		
		$this->amount      = (int)$params['amount'];
		$this->receiptId   = $params['receiptId'];
	}



	public function send()
	{
		$params = [
			'TermID'          => $this->terminalId,
			'ResNum'          => $this->receiptId,
			'TotalAmount'     => $this->amount,
			
			'SegAmount1'      => null,
			'SegAmount2'      => null,
			'SegAmount3'      => null,
			'SegAmount4'      => null,
			'SegAmount5'      => null,
			'SegAmount6'      => null,
			'AdditionalData1' => null,
			'AdditionalData1' => null,
			'wage'            => null,
		];

		$result = $this->client->__soapCall('RequestToken', $params);

		if (is_numeric($result) and $result <= 0)
		{
			$this->requestError = $result;

			return false;
		}

		$this->token = $result;

		$this->requestData = $result;

		return true;
	}



	public function isReady()
	{
		return $this->requestError === null;
	}



	public function getRequestData()
	{
		return $this->requestData;
	}



	public function getRequestError()
	{
		return $this->requestError;
	}



	public function getToken()
	{
		return $this->token;
	}



	public function redirect()
	{
		$this->redirectByForm($this->paymentUrl, ['Token' => $this->token, 'RedirectURL' => $this->callbackUrl]);
	}



	/*public function capture()
	{
		// redirect to RedirectURL
		// $_POST['State']
		// $_POST['RefNum']
		// $_POST['ResNum']
		// $_POST['MID']
		// $_POST['TraceNo']
	}



	public function isSuccessfulResponse()
	{
		return ($_POST['State'] === 'OK' && $_POST['RefNum'] === $token) ? true : false;
	}



	public function getResponseData()
	{
		//return $this->requestData;
	}



	public function getResponseError()
	{
		return $_POST['State'];
	}



	public function check($amount, $orderId, $token)
	{
		// redirect to RedirectURL
		// $_POST['State']
		// $_POST['RefNum']
		// $_POST['ResNum']
		// $_POST['MID']
		// $_POST['TraceNo']

		return ($_POST['State'] === 'OK' && $_POST['RefNum'] === $token) ? true : false;
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
	}*/
}
