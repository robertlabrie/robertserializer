# robertserializer

## Why

RobertSerializer was born out of the desire to easily store objects in MongoDB. The existing PHP serializer is fine
for storing in session, but is useless for a document DB. When I asked around, the consensus seemed to be using something
called an "ODM (Object Document Model)" which is essentially a property map between the Mongo document and my PHP class. I
didn't feel like maintaining one of those! So, RobertSerializer is what I came up with.

## How it works

I leverage PHPs' reflection class to recursively crawl the input object properties and feed the data back out as an array.
Mongo likes arrays, so that's why it's an array, but you could easily called `json_encode()` on it and store it in RavenDB
if that's your thing. 

### What's the catch?
I need to know your objects type when I deserialize later, so I add an extra element to the array `__TYPE__` 
(you can change it). For this reason, it's a good idea to use namespaces. Also, probably if you change your class
namespace, you won't be able to deserialize an object. If anyone uses this and that's a problem, we'll let you define a
map (boo!) so that you can survive a refactoring.

### Why a special field?
BSON seemed ugly

## How to use it
`composer require RobertSerializer`

```php
#do the necessary composer bits
$rs = new \RobertSerializer\RobertSerializer();
$in = new \Example\Foo();
$ser = $rs->serialize($in);
//$ser is an array that you can pass around

$out = $rs->deserialize($ser);
//$out is an instance of Foo(), exactly how you remember it!


```
