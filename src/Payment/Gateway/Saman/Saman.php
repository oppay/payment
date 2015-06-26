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


	protected $errors =
	[
		"-1"  => "خطا در پردازش اطلاعات ارسالی",
		"-3"  => "ورودی ها حاوی کاراکترهای غیر مجاز می باشند",
		"-4"  => "کلمه عبور یا کد فروشنده اشتباه است",
		"-6"  => "سند قبلا برگشت کامل یافته است",
		"-7"  => "رسید دیجیتال تهی است",
		"-8"  => "طول ورودی ها بیشتر از حد مجاز است",
		"-9"  => "وجود کاراکترهای غیر مجاز در مبلغ برگشتی",
		"-10" => "رسید دیجیتال حاوی کاراکترهای غیرمجاز است",
		"-11" => "طول ورودی ها کمتر از حد مجاز است",
		"-12" => "مبلغ برگشتی منفی است",
		"-13" => "",
		"-14" => "چنین تراکنشی تعریف نشده",
		"-15" => "مبلغ برگشتی به صورت اعشاری داده شده",
		"-16" => "خطای داخلی سیستم",
		"-17" => "برگشت زدن جزئی تراکنش مجاز نیست",
		"-18" => "آدرس IP فروشنده نامعتبر است",
	];



	protected $states =
	[
		"OK"                                   => "",
		"Canceled By User"                     => "خطا در پردازش اطلاعات ارسالی",
		"Invalid Amount"                       => "",
		"Invalid Transaction"                  => "",
		"Invalid Card Number"                  => "",
		"No Such Issuer"                       => "",
		"Expired Card Pick Up"                 => "",
		"Allowable PIN Tries Exceeded Pick Up" => "",
		"Incorrect PIN"                        => "",
		"Exceeds Withdrawal Amount Limit"      => "",
		"Transaction Cannot Be Completed"      => "",
		"Response Received Too Late"           => "",
		"Suspected Fraud Pick Up"              => "",
		"No Sufficient Funds"                  => "",
		"Issuer Down Slm"                      => "",
	];



	public function __construct(array $params)
	{
		$this->terminalId  = $params['terminalId'];
		$this->callbackUrl = $params['callbackUrl'];
	}



	public function send($amount, $receiptId)
	{
		$params = [
			'TermID'          => $this->terminalId,
			'ResNum'          => $receiptId,
			'TotalAmount'     => $amount,
			/*
			'SegAmount1'      => null,
			'SegAmount2'      => null,
			'SegAmount3'      => null,
			'SegAmount4'      => null,
			'SegAmount5'      => null,
			'SegAmount6'      => null,
			'AdditionalData1' => null,
			'AdditionalData1' => null,
			'wage'            => null,
			*/
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



	public function getRequestData()
	{
		return $this->requestData;
	}



	public function getRequestError()
	{
		if (isset($this->errors[$this->requestError]))
		{
			return ['code' => $this->requestError, 'message' => $this->errors[$this->requestError]];
		}

		return ['code' => null, 'message' => null];
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
