<?php

namespace Payment\Gateway\Mellat;

use Payment\Gateway\Gateway;
use Payment\Gateway\GatewayInterface;

class Mellat extends Gateway implements GatewayInterface
{
	protected $wsdlUrl = 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';


	protected $paymentUrl = 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat';


	protected $userName;


	protected $userPassword;


	protected $terminalId;


	protected $errors = [
		'error' => 'در اتصال به درگاه پرداخت خطایی رخ داده!',
		'0'   => 'تراكنش با موفقيت انجام شد',
		'11'  => 'شماره كارت نامعتبر است',
		'12'  => 'موجودي كافي نيست',
		'13'  => 'رمز نادرست است',
		'14'  => 'تعداد دفعات وارد كردن رمز بيش از حد مجاز است',
		'15'  => 'كارت نامعتبر است',
		'16'  => 'دفعات برداشت وجه بيش از حد مجاز است',
		'17'  => 'كاربر از انجام تراكنش منصرف شده است',
		'18'  => 'تاريخ انقضاي كارت گذشته است',
		'19'  => 'مبلغ برداشت وجه بيش از حد مجاز است',
		'111' => 'صادر كننده كارت نامعتبر است',
		'112' => 'خطاي سوييچ صادر كننده كارت',
		'113' => 'پاسخي از صادر كننده كارت دريافت نشد',
		'114' => 'دارنده كارت مجاز به انجام اين تراكنش نيست',
		'21'  => 'پذيرنده نامعتبر است',
		'23'  => 'خطاي امنيتي رخ داده است',
		'24'  => 'اطلاعات كاربري پذيرنده نامعتبر است',
		'25'  => 'مبلغ نامعتبر است',
		'31'  => 'پاسخ نامعتبر است',
		'32'  => 'فرمت اطلاعات وارد شده صحيح نمي باشد',
		'33'  => 'حساب نامعتبر است',
		'34'  => 'خطاي سيستمي',
		'35'  => 'تاريخ نامعتبر است',
		'41'  => 'شماره درخواست تكراري است',
		'42'  => 'يافت نشد Sale تراكنش',
		'43'  => 'قبلا درخواست Verify داده شده است',
		'44'  => 'درخواست Verfiy يافت نشد',
		'45'  => 'تراكنش Settle شده است',
		'46'  => 'تراكنش Settle نشده است',
		'47'  => 'تراكنش Settle يافت نشد',
		'48'  => 'تراكنش Reverse شده است',
		'49'  => 'تراكنش Refund يافت نشد',
		'412' => 'شناسه قبض نادرست است',
		'413' => 'شناسه پرداخت نادرست است',
		'414' => 'سازمان صادر كننده قبض نامعتبر است',
		'415' => 'زمان جلسه كاري به پايان رسيده است',
		'416' => 'خطا در ثبت اطلاعات',
		'417' => 'شناسه پرداخت كننده نامعتبر است',
		'418' => 'اشكال در تعريف اطلاعات مشتري',
		'419' => 'تعداد دفعات ورود اطلاعات از حد مجاز گذشته است',
		'421' => 'IP نامعتبر است',
		'51'  => 'تراكنش تكراري است',
		'54'  => 'تراكنش مرجع موجود نيست',
		'55'  => 'تراكنش نامعتبر است',
		'61'  => 'خطا در واريز',
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

		$result = $this->client()->__soapCall('bpPayRequest', [$params]);

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

		$result = $this->client()->__soapCall('bpVerifyRequest', [$params]);

		if ($result instanceof \SoapFault)
		{
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

		$result = $this->client()->__soapCall('bpInquiryRequest', [$params]);

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
