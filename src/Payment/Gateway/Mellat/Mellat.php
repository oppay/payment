<?php

namespace Payment\Gateway\Mellat;

use Payment\Gateway\Gateway;
use Payment\Gateway\GatewayInterface;

class Mellat extends Gateway implements GatewayInterface
{
	protected $wsdlUrl = 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';


	protected $paymentUrl = 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat';


	protected $terminalId;


	protected $userName;


	protected $userPassword;

	
	protected $callbackUrl;


	protected $token;


	//protected $client;


	protected $requestData;


	protected $requestError;


	protected $responseData;


	protected $responseError;


	protected $errors = [
		"error" => "خطای درگاه پرداخت!",
		"0"   => "",
		"11"  => "",
		"12"  => "",
		"13"  => "",
		"14"  => "",
		"15"  => "",
		"16"  => "",
		"17"  => "کاربر از انجام تراکنش منصرف شده است",
		"18"  => "",
		"19"  => "",
		"111" => "",
		"112" => "",
		"113" => "",
		"114" => "",
		"21"  => "",
		"23"  => "",
		"24"  => "",
		"25"  => "",
		"31"  => "",
		"32"  => "",
		"33"  => "",
		"34"  => "",
		"35"  => "",
		"41"  => "",
		"42"  => "",
		"43"  => "",
		"44"  => "",
		"45"  => "",
		"46"  => "",
		"47"  => "",
		"48"  => "",
		"49"  => "",
		"412" => "",
		"413" => "",
		"414" => "",
		"415" => "",
		"416" => "",
		"417" => "",
		"418" => "",
		"419" => "",
		"421" => "",
		"51"  => "",
		"54"  => "",
		"55"  => "",
		"61"  => "",
	];



	public function __construct(array $params)
	{
		$this->terminalId   = $params['terminalId'];
		$this->userName     = $params['userName'];
		$this->userPassword = $params['userPassword'];
		$this->callbackUrl  = $params['callbackUrl'];
	}



	public function send($amount, $receiptId)
	{
		$params = [
			'terminalId'     => $this->terminalId + 0,
			'userName'       => $this->userName,
			'userPassword'   => $this->userPassword,
			'orderId'        => $receiptId + 0,
			'amount'         => $amount + 0,
			'localDate'      => date('Ymd'),
			'localTime'      => date('His'),
			'additionalData' => '',
			'callBackUrl'    => $this->callbackUrl,
			'payerId'        => 0,
		];

		$result = $this->client->__soapCall('bpPayRequest', [$params]);

		if ($result instanceof \SoapFault)
		{
			$this->requestError = 'error';

			return false;
		}

		$result = explode(',', $result->return);

		if ($result[0] !== '0')
		{
			$this->requestError = $result[0];

			return false;
		}

		$this->token = $result[1];

		$this->requestData = implode(',', $result);

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
		$this->redirectByForm($this->paymentUrl, ['RefId' => $this->token]);
	}



	public function captureResponse()
	{
		if (!(isset($_POST['ResCode']) && ($_POST['ResCode'] === "0")))
		{
			$this->responseError = $_POST['ResCode'];
		}
	}



	public function isResponseOk()
	{
		return $this->responseError === null;
	}



	public function getResponseData()
	{
		if (isset($_POST['SaleOrderId']))
		{
			$_POST['SaleOrderId'] = $_POST['SaleOrderId'] + 0;
		}

		if (isset($_POST['SaleReferenceId']))
		{
			$_POST['SaleReferenceId'] = $_POST['SaleReferenceId'] + 0;
		}

		return $_POST;
	}



	public function getResponseError()
	{
		if (isset($this->errors[$this->responseError]))
		{
			return ['code' => $this->responseError, 'message' => $this->errors[$this->responseError]];
		}

		return ['code' => null, 'message' => null];
	}



	public function verify($orderId, $saleReferenceId)
	{
		$params = [
			'terminalId'      => $this->terminalId + 0,
			'userName'        => $this->userName,
			'userPassword'    => $this->userPassword,
			'orderId'         => $orderId + 0,
			'saleOrderId'     => $orderId + 0,
			'saleReferenceId' => $saleReferenceId + 0,
		];

		$result = $this->client->__soapCall('bpVerifyRequest', [$params]);

		if ($result instanceof \SoapFault)
		{
			//$this->responseError = 'error';

			return $this->inquiry($orderId, $saleReferenceId);
		}

		$result = $result->return;

		if ($result === '43')
		{
			return $this->inquiry($orderId, $saleReferenceId);
		}

		if ($result !== '0')
		{
			$this->responseError = $result;

			return false;
		}

		return true;
	}



	public function inquiry($orderId, $saleReferenceId)
	{
		$params = [
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

		return true;
	}
}
