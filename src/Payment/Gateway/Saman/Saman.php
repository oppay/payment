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
		"-13" => "مبلغ برگشتی برای برگشت جزئی بیش از مبلغ برگشت نخورده ی رسید دیجیتالی ایست",
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
		"Canceled By User"                     => "تراکنش توسط خریدار کنسل شده است",
		"Invalid Amount"                       => "مبلغ سند برگشتی از مبلغ تراکنش اصلی بیشتر است",
		"InvalidTransaction"                   => "درخواست برگشت یک تراکنش رسیده است، درحالی که تراکنش اصلی پیدا نمی شود",
		"Invalid Card Number"                  => "شماره کارت اشتباه است",
		"No Such Issuer"                       => "چنین صادرکننده کارتی وجود ندارد",
		"Expired Card Pick Up"                 => "از تاریخ انقضای کارت گذشته است و کارت دیگر معتبر نیست",
		"Allowable PIN Tries Exceeded Pick Up" => "رمز کارت (pin) سه مرتبه اشتباه وارد شده است در نتیجه کارت غیرفعال خواهد شد",
		"Incorrect PIN"                        => "خریدار رمز کارت (pin) را اشتباه وارد کرده است",
		"Exceeds Withdrawal Amount Limit"      => "مبلغ بیش از سقف برداشت می باشد",
		"Transaction Cannot Be Completed"      => "تراکنش authorize شده است (شماره pin و pan درست هستند) ولی امکان سند خوردن وجود ندارد",
		"Response Received Too Late"           => "تراکنش در شبکه بانکی timeout خورده است",
		"Suspected Fraud Pick Up"              => "خریدار یا فیلد CVV2 یا فیلد ExpDate را اشتباه وارد کرده است (یا اصلا وارد نرکدهاست)",
		"No Sufficient Funds"                  => "موجودی حساب خریدار کافی نیست",
		"Issuer Down Slm"                      => "سیستم بانک صادرکننده کارت خریدار در وضعیت عملیاتی نیست",
	];



	public function __construct(array $params)
	{
		$this->terminalId  = $params['terminalId'];
		$this->callbackUrl = $params['callbackUrl'];
	}



	public function send($amount, $receiptId, $description = '')
	{
		$params = [
			'TermID'          => (string)$this->terminalId,
			'ResNum'          => (string)$receiptId,
			'TotalAmount'     => $amount + 0,
			'AdditionalData1' => (string)$description,
		];

		$result = $this->client()->__soapCall('RequestToken', $params);

		if ($result instanceof \SoapFault)
		{
			$this->requestError = 'error';

			return false;
		}

		if (is_numeric($result) && $result <= 0)
		{
			$this->requestError = $result;

			return false;
		}

		$this->token = $result;

		$this->requestData = $result;

		return true;
	}



	public function redirect()
	{
		$this->redirectByPostMethod($this->paymentUrl, [], ['Token' => $this->token, 'RedirectURL' => $this->callbackUrl]);
	}



	public function captureResponse()
	{
		if (!(isset($_POST['State']) && ($_POST['State'] === "OK")))
		{
			$this->responseError = $_POST['State'];
		}
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

		if (is_numeric($result) && $result <= 0)
		{
			$this->requestError = $result;

			return false;
		}

		return true;
	}
}
