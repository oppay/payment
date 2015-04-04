<?php

namespace Payment;

interface GatewayInterface
{
	public function __construct(array $config);



	//public function purchase(array $params);



	public function send();



	public function redirect();



	//public function handle();



	public function verify();
}
