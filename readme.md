Integration of Consistence library with Doctrine ORM
====================================================

This library provides integration of [Consistence](https://github.com/consistence/consistence) value objects for [Doctrine ORM](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/) so that you can use them in your entities.

For now, the only integration which is needed is for [Enums](https://github.com/consistence/consistence/blob/master/docs/Enum/enums.md), see the examples below.

Usage
-----

[Enums](https://github.com/consistence/consistence/blob/master/docs/Enum/enums.md) represent predefined set of values and of course, you will want to store these values in your database as well. Since [`Enums`](https://github.com/consistence/consistence/blob/master/src/Enum/Enum.php) are objects and you only want to store the represented value, there has to be some mapping.

You can see it in this example where you want to store sex for your `User`s:

```php
<?php

namespace Consistence\Doctrine\Example\User;

class Sex extends \Consistence\Enum\Enum
{

	public const FEMALE = 'female';
	public const MALE = 'male';

}
```

To use the `Sex` enum in your `User` entity notice the `type="enum<Consistence\Doctrine\Example\User\Sex>"` in `ORM\Column` - this will be used for mapping the value to and from your database. You can specify any other parameters for `ORM\Column` as you would usually (nullability, length...).

```php
<?php

namespace Consistence\Doctrine\Example\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class User extends \Consistence\ObjectPrototype
{

	// ...

	/**
	 * @ORM\Column(type="enum<Consistence\Doctrine\Example\User\Sex>", nullable=true)
	 * @var \Consistence\Doctrine\Example\User\Sex|null
	 */
	private $sex;

	// ...

	public function __construct(
		// ...
		Sex $sex = null
		// ...
	)
	{
		// ...
		$this->sex = $sex;
		// ...
	}

	// ...

}
```

Before this will work with database, we need to register [Doctrine DBAL type](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#custom-mapping-types) for each enum.

> If you are using Symfony, you can use [`consistence/consistence-doctrine-symfony`](https://github.com/consistence/consistence-doctrine-symfony), where you can just configure which types should be registered.

> If you do this in code, note that this step contains static calls which have global effect. I recommend putting them inside a bootstrap file (usually where you register the Composer autoloader now), which is run or included when the application starts.

```php
<?php

use Consistence\Doctrine\Enum\Type\StringEnumType;
use Consistence\Doctrine\Example\User\Sex;
use Doctrine\DBAL\Types\Type as DoctrineType;

foreach ([
	StringEnumType::create(StringEnumType::class),
] as $type) {
	DoctrineType::getTypeRegistry()->register($type->getName(), $type);
}
```

We used `StringEnumType` because the `Sex` enum uses strings for its values. There are also `IntegerEnumType`, `FloatEnumType` and `BooleanEnumType` - depending on what scalar values are used in the enum.

If you use the same enum in multiple places register it only once.

Now everything is ready to be used, when you call `flush`, only `female` will be saved:

```php
<?php

namespace Consistence\Doctrine\Example\User;

$user = new User(
	// ...
	Sex::get(Sex::FEMALE)
	// ...
);
/** @var \Doctrine\ORM\EntityManager $entityManager */
$entityManager->persist($user);

// when persisting User::$sex to database, `female` will be saved
$entityManager->flush();
```

And when you retrieve the entity back from database, you will receive the `Sex` enum object again:

```php
<?php

namespace Consistence\Doctrine\Example\User;

/** @var \Doctrine\ORM\EntityManager $entityManager */
$user = $entityManager->find(User::class, 1);
var_dump($user->getSex());

/*

class Consistence\Doctrine\Example\User\Sex#5740 (1) {
  private $value =>
  string(6) "female"
}

*/
```

This means that the objects API is symmetrical (you get the same type as you set) and you can start benefiting from [Enums](https://github.com/consistence/consistence/blob/master/docs/Enum/enums.md) advantages such as being sure, that what you get is already a valid value and having the possibility to define methods on top of the represented values.

Installation
------------

> If you are using Symfony, you can use [`consistence/consistence-doctrine-symfony`](https://github.com/consistence/consistence-doctrine-symfony), which will take care of the integration.

1) Install package [`consistence/consistence-doctrine`](https://packagist.org/packages/consistence/consistence-doctrine) with [Composer](https://getcomposer.org/):

```bash
composer require consistence/consistence-doctrine
```

2) Register needed DBAL types (see `Usage` section above).

That's all, you are good to go!
