<?php

namespace Qlake\Payment\Gateway;

use SoapClient;

class Parsian implements GatewayInterface
{
	protected $wsdlUrl = 'https://pec.shaparak.ir/pecpaymentgateway/eshopservice.asmx?wsdl';


	protected $paymentUrl = 'https://sep.shaparak.ir/Payment.aspx';


	protected $terminalId;


	protected $amount;


	protected $receiptId;


	protected $client;


	protected $requestData;


	protected $requestError;



	public function __construct(array $config)
	{
		//$this->client = new SoapClient($config['requestUrl']);
		$this->client = new SoapClient($this->wsdlUrl);
		$this->terminalId = $config['terminalId'];
		//$this->orderId = $payment->id;
		//$this->callbackUrl = '$callbackUrl';
	}



	public function sendRequest($amount, $receiptId)
	{
		$this->amount    = (int)$amount;
		$this->receiptId = $receiptId;

		$params = [
			'pin'         => 'SLpL4380HE5fKH4tB8d4',
			'amount'      => $this->amount,
			'orderId'     => $this->receiptId,
			'callbackUrl' => $this->callbackUrl,
			'authority'   => $authority,
			'status'      => $status,
		];

		$result = $this->client->__soapCall('PinPaymentRequest', $params);

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
		return $this->requestError;
	}



	public function isReady()
	{
		return $this->requestError === null;
	}



	public function redirect()
	{
		$html = <<<html
		<!doctype html>
		<html>
		<head>
			<title></title>
		</head>
		<body>
			<form action="{$this->paymentUrl}" method="post">
				<input type="hidden" value="{$this->token}" name="Token" />
				<input type="hidden" value="{$this->callbackUrl}" name="RedirectURL" />
			</form>
			<script type="text/javascript">
				document.getElementsByTagName('form')[0].submit();
			</script>
		</body>
		</html>
html;

		echo $html;
	}



	public function handle()
	{
		// redirect to RedirectURL
		// $_POST['State']
		// $_POST['RefNum']
		// $_POST['ResNum']
		// $_POST['MID']
		// $_POST['TraceNo']
		$state = $_POST['state'];
		if ($state !== 'OK')
		{
			echo "Error: $state";
			exit;
		}
		if ($_POST['RefNum'] /*is not uniqu*/)
		{
			echo 'error';
			exit;
		}
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
