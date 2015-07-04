<?php
require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/TestClasses.php";
class RobertSerializerTest extends PHPUnit_Framework_TestCase
{
	public $rs;
	public function setUp()
	{
		$this->rs = new \RobertSerializer\RobertSerializer();
	}
	public function testHashArraySimple()
	{
		$in = Array('a'=>1,'b'=>2);
		$out = $this->rs->deserialize($this->rs->serialize($in));
		$this->assertEquals($in,$out);
	}
	public function testObjectSimple()
	{
		$in = new \RobertSerializerTestClasses\DummyB();
		$ser = $this->rs->serialize($in);
		$expected = Array('__TYPE__' => 'RobertSerializerTestClasses\dummyB','p'=> "I'm a public property.",'i'=>"I'm a private property.");
		$this->assertEquals($ser,$expected);
		
		$out = $this->rs->deserialize($ser);
		$this->assertEquals($in,$out);
	}
	public function testObjectBusy()
	{
		$in = new \RobertSerializerTestClasses\DummyA();
		$in->makeBusy();
		
		$ser = $this->rs->serialize($in);
		$expected = Array(
			'__TYPE__'=>'RobertSerializerTestClasses\dummyA',
			'a'=>Array(
				'a'=>1,
				'b'=>2
			),
			'n'=>Array(1,2,3),
			's'=>'I am a String',
			'i'=>1,
			'b'=>true,
			'o'=>Array(
				'__TYPE__'=>'RobertSerializerTestClasses\dummyB',
				'p' => "I'm a public property.",
				'i' => "I'm a private property.",
			),
		);
		$this->assertEquals($ser,$expected);
		
		$out = $this->rs->deserialize($ser);
		$this->assertEquals($in,$out);
	}
	public function testBusyArray()
	{
		$in = Array(new \RobertSerializerTestClasses\DummyB(),new \RobertSerializerTestClasses\DummyB());
		$busy = new \RobertSerializerTestClasses\DummyA();
		$busy->makeBusy();
		array_push($in,$busy);
		$this->assertEquals($in,$this->rs->deserialize($this->rs->serialize($in)));
	}
}