<?php

namespace Payment\Gateway\Jahanpay;

use Payment\Gateway\Gateway;
use Payment\Gateway\GatewayInterface;

class Jahanpay extends Gateway implements GatewayInterface
{
	protected $wsdlUrl = 'http://www.jahanpay.com/webservice?wsdl';


	protected $paymentUrl = 'http://www.jahanpay.com/pay_invoice';


	protected $terminalId = 'gt34117g539';


	protected $errors = [
		'error' => 'در اتصال به درگاه پرداخت خطایی رخ داده!',
		'-6'  => 'خطاي اتصال به بانک',
		'-9'  => 'خطاي سیستمی',
		'-20' => 'api نادرست است',
		'-21' => 'آي پی براي این درگاه نامعتبر است',
		'-22' => 'مبلغ خیلی کم است، حداقل مبلغ ارسالی به درگاه 100 ت می باشد',
		'-23' => 'مبلغ زیاد است',
		'-24' => 'مبلغ نادرست است',
		'-26' => 'درگاه غیر فعال شده است',
		'-27' => 'آي پی شما مسدود است',
		'-29' => 'آدرس کال بک خالی است',
		'-30' => 'چنین تراکنشی موجود نیست',
		'-31' => 'تراکنش انجام نشده است',
		'-32' => 'تراکنش انجام شده اما مبلغ مطابقت ندارد',
	];



	public function __construct(array $params)
	{
		$this->terminalId   = $params['terminalId'];
		$this->callbackUrl  = $params['callbackUrl'];
	}



	public function send($amount, $receiptId)
	{
		$result = $this->client()->requestpayment
		(
			$this->terminalId,
			$amount / 10,
			$this->callbackUrl,
			$receiptId, 
			''
		);

		if ($result instanceof \SoapFault)
		{
			$this->requestError = 'error';

			return false;
		}
		
		if (is_numeric($result) && $result < 0)
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
		$this->redirectByGetMethod($this->paymentUrl .'/'. $this->token);
	}



	public function captureResponse()
	{
		if (!(isset($_GET['au']) && is_numeric($_GET['au'])))
		{
			$this->responseError = 'error';
		}
	}



	public function getResponseData()
	{
		if (isset($_GET['au']))
		{
			$_GET['au'] = $_GET['au'] + 0;
		}

		if (isset($_GET['order_id']))
		{
			$_GET['order_id'] = $_GET['order_id'] + 0;
		}

		return $_GET;
	}



	public function verify($orderId, $saleReferenceId)
	{
		$result = $this->client->verification($this->terminalId, $amount + 0, $_GET["au"]);

		if ($result instanceof \SoapFault)
		{
			$this->requestError = 'error';

			return false;
		}

		if (is_numeric($result) && $result < 0)
		{
			$this->requestError = $result;

			return false;
		}

		if (is_numeric($result) && $result == 1)
		{
			return true;
		}

		return false;
	}
}
