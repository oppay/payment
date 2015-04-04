<?php

namespace Payment;

use Payment\GatewayInterface;
use Payment\Gateway\Saman\Saman;
use Payment\Gateway\Mellat\Mellat;

class Payment
{
	public static function create($name, array $params = [])
	{
		switch ($name)
		{
			case 'Saman':
				$gateway = new Saman($params);
				break;

			case 'Mellat':
				$gateway = new Mellat($params);
				break;
			
			default:
				throw new \InvalidArgumentException();
				break;
		}

		return $gateway;
	}
}
