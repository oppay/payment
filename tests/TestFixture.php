<?php


require __DIR__.'/../vendor/autoload.php';

class TestFixture extends \PHPUnit_Framework_TestCase
{
	protected $timezone;



	protected function setUp()
	{
		//save current timezone
		$this->timezone = date_default_timezone_get();
		
		date_default_timezone_set('Asia/Tehran');
	}



	protected function tearDown()
	{
		date_default_timezone_set($this->timezone);
	}
}