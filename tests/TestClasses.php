<?php

namespace TestClasses;

class dummyA
{
	public $a = Array('a'=>1,'b'=>2);
	public $n = Array(1,2,3);
	public $s = "I am a String";
	public $i = 1;
	private $b = true;
	public $o;
	public function __construct()
	{
		$this->o = new \RobertSerializer();
	}
}