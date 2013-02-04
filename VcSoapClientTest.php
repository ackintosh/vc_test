<?php
require("./require_test.php");
require("./VcSoapClient.php");

class VcSoapClientTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->order_id = '1000';
		$this->payment_total = '2000';
		$this->card_no = '1234567890123456';
		$this->card_pin = '123456';

		$this->mock = $this->getMockBuilder('SoapClient')
			->setMethods(array('withdraw'))
			->disableOriginalConstructor()->getMock();
	}

	public function testWithdraw()
	{
		$expect_query = array(
			'accessKey' => ACCESS_KEY,
			'dealInfo' 	=> array(
				'termId' 	=> TERM_ID,
				'receiptNo' => $this->order_id,
				'cardNo' 	=> $this->card_no,
				'pinCode' 	=> $this->card_pin,
				'reqId' 	=> $this->order_id,
				),
			'volume' 	=> $this->payment_total,
			);

		$this->mock->expects($this->once())
			->method('withdraw')
			->with(
				$this->equalTo(
					$expect_query
					)
				);

		$vsc = new VcSoapClient($this->mock);
		$vsc->withdraw($this->order_id, $this->payment_total, $this->card_no, $this->card_pin);
	}

	public function testGetResult()
	{
		$responce = new stdClass();
		$responce->return = 'expected value';


		$this->mock->expects($this->once())
			->method('withdraw')
			->will($this->returnValue($responce));
		$vsc = new VcSoapClient($this->mock);
		$vsc->withdraw($this->order_id, $this->payment_total, $this->card_no, $this->card_pin);

		$this->assertEquals('expected value', $vsc->getResult());
	}

	public function testIsSuccess_200()
	{
		$responce = new stdClass();
		$responce->return = new stdClass();
		$responce->return->resultCode = 200;

		$this->mock->expects($this->once())
			->method('withdraw')
			->will($this->returnValue($responce));

		$vsc = new VcSoapClient($this->mock);
		$vsc->withdraw($this->order_id, $this->payment_total, $this->card_no, $this->card_pin);

		$this->assertEquals(true, $vsc->isSuccess());
	}

	public function testIsSuccess_201()
	{
		$responce = new stdClass();
		$responce->return = new stdClass();
		$responce->return->resultCode = 201;

		$this->mock->expects($this->once())
			->method('withdraw')
			->will($this->returnValue($responce));

		$vsc = new VcSoapClient($this->mock);
		$vsc->withdraw($this->order_id, $this->payment_total, $this->card_no, $this->card_pin);

		$this->assertEquals(true, $vsc->isSuccess());
	}

	public function testGetErrorMessage()
	{
		$responce = new stdClass();
		$responce->return = new stdClass();
		$responce->return->errorMessage = 'expected error message';

		$this->mock->expects($this->once())
			->method('withdraw')
			->will($this->returnValue($responce));

		$vsc = new VcSoapClient($this->mock);
		$vsc->withdraw($this->order_id, $this->payment_total, $this->card_no, $this->card_pin);

		$this->assertEquals('expected error message', $vsc->getErrorMessage());
	}
}
