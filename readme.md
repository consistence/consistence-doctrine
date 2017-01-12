Integration of Consistence library with Doctrine ORM
====================================================

This library provides integration of [Consistence](https://github.com/consistence/consistence) value objects for [Doctrine ORM](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/) so that you can use them in your entities.

For now, the only integration which is needed is for [Enums](https://github.com/consistence/consistence/blob/master/docs/Enum/enums.md), see the examples below.

Usage
-----

[Enums](https://github.com/consistence/consistence/blob/master/docs/Enum/enums.md) represent predefined set of values and of course, you will want to store these values in your database as well. Since [`Enums`](https://github.com/consistence/consistence/blob/master/src/Enum/Enum.php) are objects and you only want to store the represented value, there has to be some mapping.

Let's see this in the example where you want to store sex for your `User`s:

```php
<?php

namespace Consistence\Doctrine\Example\User;

class Sex extends \Consistence\Enum\Enum
{

	const FEMALE = 'female';
	const MALE = 'male';

}
```

Now you can use the `Sex` enum in your `User` entity. There are two important things to notice:

1) `type="string_enum"` in `ORM\Column` - this will be used for mapping the value to your database, that means if you have a string based enum (see values in `Sex`), use `string_enum`

You can specify any other parameters for `ORM\Column` as you would usually (nullability, length...).

There is also `integer_enum` and `float_enum` which can be used respectively for their types.

2) `@Enum(class=Sex::class)` - this will be used for reconstructing the `Sex`
 enum object when loading the value back from database

The `class` annotation parameter uses the same namespace resolution process as other Doctrine annotations, so it is practically the same as when you specify a `targetEntity` in associations mapping.

```php
<?php

namespace Consistence\Doctrine\Example\User;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class User extends \Consistence\ObjectPrototype
{

	// ...

	/**
	 * @Enum(class=Sex::class)
	 * @ORM\Column(type="string_enum", nullable=true)
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

Now everything is ready to be used, when you call `flush` only `female` will be saved:

```php
<?php

namespace Consistence\Doctrine\Example\User;

$user = new User(
	// ...
	Sex::get(Sex::FEMALE)
	// ...
);
/** @var $entityManager \Doctrine\ORM\EntityManager */
$entityManager->persist($user);

// when persisting User::$sex to database, `female` will be saved
$entityManager->flush();
```

And when you retrieve the entity back from database, you will receive the `Sex` enum object again:

```php
<?php

namespace Consistence\Doctrine\Example\User;

/** @var $entityManager \Doctrine\ORM\EntityManager */
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

1) Install package [`consistence/consistence-doctrine`](https://packagist.org/packages/consistence/consistence-doctrine) with [Composer](https://getcomposer.org/):

```bash
composer require consistence/consistence-doctrine
```

2) Register [Doctrine DBAL types](http://doctrine-orm.readthedocs.org/en/latest/cookbook/custom-mapping-types.html) and [annotations](http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html#registering-annotations):

```php
<?php

use Consistence\Doctrine\Enum\Type\FloatEnumType;
use Consistence\Doctrine\Enum\Type\IntegerEnumType;
use Consistence\Doctrine\Enum\Type\StringEnumType;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\DBAL\Types\Type as DoctrineType;

// path to your Composer autoload file
$loader = require __DIR__ . '/../vendor/autoload.php';

// register loading of custom annotations
// if you are already using Doctrine annotations you probably won't need this
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

// register Doctrine DBAL types
DoctrineType::addType(FloatEnumType::NAME, FloatEnumType::class); // float_enum
DoctrineType::addType(IntegerEnumType::NAME, IntegerEnumType::class); // integer_enum
DoctrineType::addType(StringEnumType::NAME, StringEnumType::class); // string_enum
```

This step contains static call which have global effect, so I recommend putting them inside a bootstrap file (usually where you register the Composer autoloader now), which is run or included when the application starts.

If you are already using [Doctrine annotations](http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html), the `AnnotationRegistry::registerLoader()` might already be called somewhere in your application, so check that before adding it.

3) [Register postLoad listener](http://doctrine-orm.readthedocs.io/en/latest/reference/events.html#listening-and-subscribing-to-lifecycle-events):

You need to register [`EnumPostLoadEntityListener`](/src/Enum/EnumPostLoadEntityListener.php), which needs `\Doctrine\Common\Annotations\Reader`. If you are using [annotations Doctrine mapping](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html), then you can use the same reader this way:

```php
<?php

use Consistence\Doctrine\Enum\EnumPostLoadEntityListener;

use Doctrine\ORM\Events;

/** @var $entityManager \Doctrine\ORM\EntityManager */
/** @var $annotationDriver \Doctrine\ORM\Mapping\Driver\AnnotationDriver */
$annotationDriver = $entityManager->getConfiguration()->getMetadataDriverImpl();
$annotationReader = $annotationDriver->getReader();

$entityManager->getEventManager()->addEventListener(
	Events::postLoad,
	new EnumPostLoadEntityListener($annotationReader)
);
```

If not, just create a new instance and pass it to the constructor.

That's all, you are good to go!
