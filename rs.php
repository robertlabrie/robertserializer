<?php
require_once('dummy.php');

$rs = new \RobertSerializer();
$dummyA = new \foo\dummyA();

$ser = $rs->serialize($dummyA);
print_r($ser);

echo "\n-----------------------------------\n";
$obj = $rs->deserialize($ser);
print_r($obj);

echo "test equality: " . ($obj == $dummyA) . "\n";
class RobertSerializer
{
	public $typeKey = '__TYPE__';
	public function deserialize($o)
	{
		if (gettype($o) != 'array') { throw new \Exception('Can only deserialize array'); }
		if (array_key_exists($this->typeKey,$o))
		{ //object
			$rc = new \ReflectionClass($o[$this->typeKey]);
			$out = $rc->newInstanceWithoutConstructor();
			foreach ($o as $key => $value)
			{
				
				if (is_array($value))
				{
					if ($rc->hasProperty($key))
					{
						$rc->getProperty($key)->setValue($out,$this->deserialize($value));
					}
					//echo "$key\t" . $rc->hasProperty($key) . "\n";
				}
				else
				{
					if ($rc->hasProperty($key))
					{
						$p = $rc->getProperty($key);
						$p->setAccessible(true);
						$p->setValue($out,$value);
					}
				}
				//echo "$key\t" . is_array($value) . "\n";
			}
			if ($rc->hasMethod('__wakeup')) { $out->__wakeup(); }
		}
		else
		{ //array
			$out = Array();
			foreach ($o as $key => $value)
			{
				if (is_array($value)) { $out[$key] = $this->deserialize($value); }
				else { $out[$key] = $value; }
			}
		}
		return $out;
	}
	public function serialize($o)
	{
		$type = gettype($o);
		if  (! in_array($type,Array('object','array'))) { throw new \Exception('Can only serialize an object or array'); }
		$out = Array();
		if ($type == 'object')
		{
			$out[$this->typeKey] = get_class($o);
			$rc = new \ReflectionClass($o);
			if ($rc->hasMethod('__wakeup')) { $o->__sleep(); }
			foreach ($rc->getProperties() as $p)
			{
				$p->setAccessible(true);
				$v = $p->getValue($o);
				if (in_array(gettype($v),Array('object','array')))
				{
					$out[$p->name] = $this->serialize($v);
				}
				else
				{
					$out[$p->name] = $v;
				}
			}
		}
		if ($type == 'array')
		{
			foreach ($o as $key => $value)
			{
				if (in_array(gettype($value),Array('object','array')))
				{
					$out[$key] = $this->serialize($value);
				}
				else
				{
					$out[$key] = $value;
				}
			}
		}
		return $out;
	}
}