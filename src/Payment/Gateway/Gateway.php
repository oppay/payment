<?php

namespace Payment\Gateway;

class Gateway
{
	protected $wsdlUrl;


	protected $paymentUrl;


	protected $callbackUrl;


	protected $token;


	protected $requestData;


	protected $requestError;


	protected $responseData;


	protected $responseError;


	protected $errors = [];



	protected function client($url = null)
	{
		$url = $url ?: $this->wsdlUrl;

		return new \SoapClient($url, ['exceptions' => false, 'encoding' => 'UTF-8']);
	}



	protected function redirectByForm($url, array $params)
	{
		$elements = '';

		foreach ($params as $name => $value)
		{
			$elements .= "<input type=\"hidden\" value=\"{$value}\" name=\"{$name}\" />";
		}

		include __DIR__ . '/../form.html';
	}



	public function getToken()
	{
		return $this->token;
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
		if (isset($this->errors[$this->responseError]))
		{
			return ['code' => $this->responseError, 'message' => $this->errors[$this->responseError]];
		}

		return ['code' => null, 'message' => null];
	}
}
