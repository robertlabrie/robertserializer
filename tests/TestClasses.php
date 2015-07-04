<?php

namespace RobertSerializerTestClasses;

class dummyA
{
	public $a;
	public $n;
	public $s;
	public $i;
	private $b;
	public $o;
	public function makeBusy()
	{
		$this->a = Array('a'=>1,'b'=>2);
		$this->n = Array(1,2,3);
		$this->s = "I am a String";
		$this->i = 1;
		$this->b = true;
		$this->o = new \RobertSerializerTestClasses\dummyB();
	}
}

class dummyB
{
	public $p = "I'm a public property.";
	private $i = "I'm a private property.";
}