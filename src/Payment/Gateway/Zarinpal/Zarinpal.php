<?php

namespace Payment\Gateway\Zarinpal;

use Payment\Gateway\Gateway;
use Payment\Gateway\GatewayInterface;

class Zarinpal extends Gateway implements GatewayInterface
{
	protected $wsdlUrl = 'https://ir.zarinpal.com/pg/services/WebGate/wsdl';


	protected $paymentUrl = 'https://www.zarinpal.com/pg/StartPay';


	protected $terminalId;


	protected $errors =
	[
		"error" => "در اتصال با درگاه پرداخت خطایی رخ داده!",
		"NOK" => "پرداخت ناموفق بود",
		"-1"  => "اعلاعت اضسال ضسٌ والع است",
		"-2"  => "",
		"-3"  => "",
		"-4"  => "",
		"-11" => "",
		"-21" => "",
		"-22" => "",
		"-33" => "",
		"-54" => "",
		"100" => "",
		"101" => ""
	];



	public function __construct(array $params)
	{
		$this->terminalId  = $params['terminalId'];
		$this->callbackUrl = $params['callbackUrl'];
	}



	public function send($amount, $receiptId)
	{
		$params = [
			'MerchantID'  => (string)$this->terminalId,
			'Amount'      => $amount / 10,
			'Description' => '-',
			'Email'       => '',
			'Mobile'      => '',
			'CallbackURL' => $this->callbackUrl
		];

		$result = $this->client()->__soapCall('PaymentRequest', [$params]);

		if ($result instanceof \SoapFault)
		{
			$this->requestError = 'error';

			return false;
		}

		if ($result->Status != 100)
		{
			$this->requestError = $result->Status;

			return false;
		}

		$this->token = $result->Authority;

		$this->requestData = $result->Status;

		return true;
	}



	public function redirect()
	{
		$this->redirectByGetMethod($this->paymentUrl .'/'. $this->token);
	}



	public function captureResponse()
	{
		if (!(isset($_GET['Status']) && ($_GET['Status'] === "OK")))
		{
			$this->responseError = $_GET['Status'];
		}
	}



	public function verify($amount, $orderId, $saleReferenceId)
	{return;
		$params = [
			'MerchantID' => (string)$this->terminalId,
			'Authority'  => (string)$saleReferenceId,
			'Amount'     => $amount,
		];

		$result = $this->client($this->wsdlVerifyUrl)->__soapCall('PaymentVerification', $params);

		if ($result instanceof \SoapFault)
		{
			$this->requestError = 'error';

			return false;
		}

		if ($result->Status != 100)
		{
			$this->requestError = $result->Status;

			return false;
		}

		return true;
	}
}
