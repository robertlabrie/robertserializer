<?php
namespace RobertSerializer;

class RobertSerializer
{
	public $typeKey = '__TYPE__';
	public $debug = false;
	public function deserialize($o)
	{
		if (gettype($o) != 'array') { throw new \Exception('Can only deserialize array'); }
		if (array_key_exists($this->typeKey,$o))
		{ //object
			$rc = new \ReflectionClass($o[$this->typeKey]);
			$out = $rc->newInstanceWithoutConstructor();
			foreach ($o as $key => $value)
			{
				if ($rc->hasProperty($key))
				{
					$p = $rc->getProperty($key);
					$p->setAccessible(true);
					if (is_array($value))
					{
						$p->setValue($out,$this->deserialize($value));
					}
					else
					{
						$p->setValue($out,$value);
					}
				}
			}
			if ($rc->hasMethod('__wakeup')) { $out->__wakeup(); }
		}
		else
		{ //array
			$out = Array();
			foreach ($o as $key => $value)
			{
				if ($this->debug) { echo "deserializing $key\n"; }
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