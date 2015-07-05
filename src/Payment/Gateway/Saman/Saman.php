<?php

namespace Payment\Gateway\Saman;

use Payment\Gateway\Gateway;
use Payment\Gateway\GatewayInterface;

class Saman extends Gateway implements GatewayInterface
{
	protected $wsdlUrl = 'https://sep.shaparak.ir/Payments/InitPayment.asmx?wsdl';


	protected $wsdlVerifyUrl = 'https://sep.shaparak.ir/Payments/referencepayment.asmx?WSDL';


	protected $paymentUrl = 'https://sep.shaparak.ir/Payment.aspx';


	protected $terminalId;

	
	protected $callbackUrl;


	protected $token;


	protected $requestData;


	protected $requestError;


	protected $responseData;


	protected $responseError;


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
		"error"                                => "در اتصال با درگاه پرداخت خطایی رخ داده!",
		"OK"                                   => "",
		"Canceled By User"                     => "",
		"Invalid Amount"                       => "",
		"InvalidTransaction"                   => "",
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



	public function client($url = null)
	{
		$url = $url ?: $this->wsdlUrl;

		return new \SoapClient($url, ['exceptions' => false, 'encoding' => 'UTF-8']);
	}



	public function send($amount, $receiptId)
	{
		$params = [
			'TermID'          => (string)$this->terminalId,
			'ResNum'          => (string)$receiptId,
			'TotalAmount'     => $amount + 0,
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

		$result = $this->client()->__soapCall('RequestToken', $params);

		if ($result instanceof \SoapFault)
		{
			$this->requestError = 'error';

			return false;
		}

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



	public function captureResponse()
	{
		if (!(isset($_POST['State']) && ($_POST['State'] === "OK")))
		{
			$this->responseError = $_POST['State'];
		}
	}



	public function isResponseOk()
	{
		return $this->responseError === null;
	}



	public function getResponseData()
	{
		return $_POST;
	}



	public function getResponseError()
	{
		if (isset($this->states[$this->responseError]))
		{
			return ['code' => $this->responseError, 'message' => $this->states[$this->responseError]];
		}

		return ['code' => null, 'message' => null];
	}



	public function verify($orderId, $saleReferenceId)
	{
		$params = [
			'RefNum'     => (string)$saleReferenceId,
			'MerchantID' => (string)$this->terminalId,
		];

		$result = $this->client($this->wsdlVerifyUrl)->__soapCall('verifyTransaction', $params);

		if ($result instanceof \SoapFault)
		{
			$this->requestError = 'error';

			return false;
		}

		if (is_numeric($result) and $result <= 0)
		{
			$this->requestError = $result;

			return false;
		}

		if ($result != $this->amount)
		{
			$result = $this->client($this->wsdlVerifyUrl)->__soapCall('reverseTransaction', ['RefNum' => $RefNum, 'MID' => $this->username, 'Username' => $this->username, 'Password' => $this->password]);
			if ($result == 1)
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