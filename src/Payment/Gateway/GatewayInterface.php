<?php

namespace Payment\Gateway;

interface GatewayInterface
{
	public function send($amount, $receiptId);

	public function redirect();

	public function getToken();

	public function getRequestData();

	public function getRequestError();

	public function captureResponse();

	public function isResponseOk();

	public function getResponseData();

	public function getResponseError();

	public function verify($orderId, $saleReferenceId);
}
