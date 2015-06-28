<?php

namespace Payment;

use Payment\Gateway\GatewayInterface;
use Payment\Gateway\Saman\Saman;
use Payment\Gateway\Mellat\Mellat;

class Payment
{
	protected static $gateway;



	public static function create($name, array $params = [])
	{
		switch ($name)
		{
			case 'Saman':
				static::$gateway = new Saman($params);
				break;

			case 'Mellat':
				static::$gateway = new Mellat($params);
				break;

			default:
				throw new \InvalidArgumentException();
				break;
		}

		return new static;
	}



	public function purchase($amount, $orderId)
	{
		$purchase = new Purchase($amount, $orderId, static::$gateway);

		return $purchase;
	}



	public function receipt()
	{
		return new Receipt(static::$gateway);
	}
}
