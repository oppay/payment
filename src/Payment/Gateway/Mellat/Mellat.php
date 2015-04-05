<?php

namespace Payment\Gateway\Mellat;

use Payment\Gateway;
use Payment\GatewayInterface;

use SoapClient;
use SoapFault;

class Mellat extends Gateway implements GatewayInterface
{
	protected $wsdlUrl = 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';


	protected $paymentUrl = 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat';


	protected $terminalId;


	protected $amount;


	protected $receiptId;


	protected $token;


	//protected $client;


	protected $requestData;


	protected $requestError;



	public function __construct(array $params)
	{
		$this->terminalId   = $params['terminalId'];
		$this->userName     = $params['userName'];
		$this->userPassword = $params['userPassword'];
		$this->callbackUrl  = $params['callbackUrl'];

		$this->amount      = (int)$params['amount'];
		$this->receiptId   = $params['receiptId'];
	}



	public function purchase(array $params)
	{
		$this->amount    = (int)$params['amount'];
		$this->receiptId = $params['receiptId'];

		return $this;
	}



	public function send()
	{
		$params = [
			'terminalId'     => $this->terminalId,
			'userName'       => $this->userName,
			'userPassword'   => $this->userPassword,
			'orderId'        => $this->receiptId,
			'amount'         => $this->amount,
			'localDate'      => date('Ymd'),
			'localTime'      => date('His'),
			'additionalData' => '',//$this->additionalData,
			'callBackUrl'    => $this->callbackUrl,
			'payerId'        => 0,//$this->payerId,
		];

		$result = $this->client->__soapCall('bpPayRequest', [$params]);

		if ($result instanceof SoapFault)
		{
			$this->requestError = $result;

			return false;
		}

		$result = explode(',', $result->return);

		if ($result[0] !== '0')
		{
			$this->requestError = $result[0];

			return false;
		}

		$this->token = $result[1];

		$this->requestData = $result[1];

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
		$this->redirectByForm($this->paymentUrl, ['RefId' => $this->token]);
	}



	public function handle()
	{
/*
		[RefId] => A434BF0F8C1BA9BB 
		[ResCode] => 0 
		[SaleOrderId] => -11 
		[SaleReferenceId] => 106935951768 
		[CardHolderInfo] => 8A67E131795C8228B4AB27D6D6BC8F2ACFE579F37CED9131798BAF0F435BF0F1 
		[CardHolderPan] => 610433****9374 ) 
*/
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
