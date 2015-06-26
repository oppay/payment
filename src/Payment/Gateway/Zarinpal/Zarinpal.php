<?php

namespace Qlake\Payment\Gateway;

use SoapClient;
use SoapFault;

class Zarinpal implements GatewayInterface
{
	protected $wsdlUrl = 'https://de.zarinpal.com/pg/services/WebGate/wsdl';


	protected $paymentUrl = 'https://www.zarinpal.com/pg/StartPay/';


	protected $terminalId;


	protected $amount;


	protected $receiptId;


	protected $client;


	protected $requestData;


	protected $requestError;



	public function __construct(array $config)
	{
		//$this->client = new SoapClient($config['requestUrl'], ['exceptions' => false]);
		$this->client = new SoapClient($this->wsdlUrl, ['exceptions' => false, 'encoding' => 'UTF-8']);
		$this->terminalId = $config['terminalId'];
		//$this->orderId = $payment->id;
		//$this->callbackUrl = '$callbackUrl';
	}



	public function sendRequest($amount, $receiptId)
	{
		$this->amount    = (int)$amount;
		$this->receiptId = $receiptId;

		$params = [
			'MerchantID'  => $this->terminalId,
			'Amount'      => $this->amount,
			'Description' => '',
			'Email'       => '',
			'Mobile'      => '',
			'CallbackURL' => $this->callbackUrl
		];

		$result = $this->client->__soapCall('PaymentRequest', $params);

		if ($result instanceof SoapFault)
		{
			$this->requestError = $result;

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
	{//Header($this->paymentUrl . $result->Authority);
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
