<?php

namespace Payment\Gateway\Saman;

use Payment\Gateway\Gateway;
use Payment\Gateway\GatewayInterface;

class Saman extends Gateway implements GatewayInterface
{
	protected $wsdlUrl = 'https://sep.shaparak.ir/Payments/InitPayment.asmx?wsdl';


	protected $paymentUrl = 'https://sep.shaparak.ir/Payment.aspx';


	protected $terminalId;

	
	protected $callbackUrl;


	protected $token;


	//protected $client;


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
		"OK"                                   => "",
		"Canceled By User"                     => "",
		"Invalid Amount"                       => "",
		"InvalidTransaction"                  => "",
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
			'RefNum' => (string)$saleReferenceId,
			'MID'    => (string)$this->terminalId,
		];

		$res = $this->client->__soapCall('verifyTransaction', $params);print_r($res);

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



	public function inquiry($orderId, $saleReferenceId)
	{
		/*$params = [
			'terminalId'      => $this->terminalId + 0,
			'userName'        => $this->userName,
			'userPassword'    => $this->userPassword,
			'orderId'         => $orderId + 0,
			'saleOrderId'     => $orderId + 0,
			'saleReferenceId' => $saleReferenceId + 0,
		];

		$result = $this->client->__soapCall('bpInquiryRequest', [$params]);

		if ($result instanceof \SoapFault)
		{
			$this->responseError = 'error';

			return false;
		}

		$result = $result->return;

		if ($result !== '0')
		{
			$this->responseError = $result;

			return false;
		}

		return true;*/
	}
}
