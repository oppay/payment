<?php

namespace Payment\Gateway\Parsian;

use Payment\Gateway\Gateway;
use Payment\Gateway\GatewayInterface;

class Parsian extends Gateway implements GatewayInterface
{
	protected $wsdlUrl = 'https://pec.shaparak.ir/pecpaymentgateway/eshopservice.asmx?wsdl';


	protected $paymentUrl = 'https://pec.shaparak.ir/pecpaymentgateway';


	protected $terminalId;


	protected $errors = [
		'error' => 'در اتصال به درگاه پرداخت خطایی رخ داده!',
		'0'  => 'تراكنش با موفقيت انجام شد',
		'1'  => 'وضعيت بلا تكليف',
		'20' => 'پين فروشنده درست نميباشد',
		'22' => 'پين يا IP فروشنده درست نميباشد',
		'30' => 'تراكنش قبلاً انجام شده (در شرايطي كه OrderId تكراري باشد پيش مي آيد)',
		'34' => 'شماره تراکنش فروشنده درست نميباشد'
	];



	public function __construct(array $params)
	{
		$this->terminalId   = $params['terminalId'];
		$this->callbackUrl  = $params['callbackUrl'];
	}



	public function send($amount, $receiptId)
	{
		$params = [
			'pin'         => (string)$this->terminalId,
			'amount'      => $amount + 0,
			'orderId'     => $receiptId + 0,
			'callbackUrl' => $this->callbackUrl,
			'authority'   => 0,
			'status'      => 0,
		];

		$result = $this->client()->__soapCall('PinPaymentRequest', [$params]);

		if ($result instanceof \SoapFault)
		{
			$this->requestError = 'error';

			return false;
		}

		if ($result->authority !== -1 && $result->status !== 0)
		{
			$this->requestError = $result->status;

			return false;
		}

		$this->token = $result->authority;

		$this->requestData = (array)$result;

		return true;
	}



	public function redirect()
	{
		$this->redirectByGetMethod($this->paymentUrl, ['au' => $this->token]);
	}



	public function captureResponse()
	{
		if (!(isset($_GET['rs']) && ($_GET['rs'] == "0")))
		{
			$this->responseError = $_GET['rs'];
		}
	}



	public function getResponseData()
	{
		if (isset($_GET['rs']))
		{
			$_GET['rs'] = $_GET['rs'] + 0;
		}

		if (isset($_GET['au']))
		{
			$_GET['au'] = $_GET['au'] + 0;
		}

		return $_GET;
	}



	public function verify($orderId, $saleReferenceId)
	{
		$params = [
			'pin'       => (string)$this->terminalId,
			'authority' => $_GET['au'],
			'status'    => 0,
		];

		$result = $this->client()->__soapCall('PinPaymentEnquiry', [$params]);

		if ($result instanceof \SoapFault)
		{
			$this->requestError = 'error';

			return false;
		}

		if ($result->status !== 0)
		{
			$this->requestError = $result->status;

			return false;
		}

		return true;
	}
}
