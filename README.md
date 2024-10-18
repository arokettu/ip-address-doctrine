# IP Address for Doctrine

[![Packagist]][Packagist Link]
[![PHP]][Packagist Link]
[![License]][License Link]
[![Gitlab CI]][Gitlab CI Link]
[![Codecov]][Codecov Link]

[Packagist]: https://img.shields.io/packagist/v/arokettu/ip-address-doctrine.svg?style=flat-square
[PHP]: https://img.shields.io/packagist/php-v/arokettu/ip-address-doctrine.svg?style=flat-square
[License]: https://img.shields.io/packagist/l/arokettu/ip-address-doctrine.svg?style=flat-square
[Gitlab CI]: https://img.shields.io/gitlab/pipeline/sandfox/ip-address-doctrine/master.svg?style=flat-square
[Codecov]: https://img.shields.io/codecov/c/gl/sandfox/ip-address-doctrine?style=flat-square

[Packagist Link]: https://packagist.org/packages/arokettu/ip-address-doctrine
[License Link]: LICENSE.md
[Gitlab CI Link]: https://gitlab.com/sandfox/ip-address-doctrine/-/pipelines
[Codecov Link]: https://codecov.io/gl/sandfox/ip-address-doctrine/

Doctrine support for [arokettu/ip-address] with support for native types in MariaDB and PostgreSQL.

## Installation

```bash
composer require arokettu/ip-address-doctrine
```

## Usage

Available types:

* Any version IP Address in text and binary form
* IPv4 Address in text and binary form
* IPv6 Address in text and binary form
* Any version IP Block in text and binary form
* IPv4 Block in text and binary form
* IPv6 Block in text and binary form
* Native PostgreSQL types: `inet`, `cidr`
* Native MariaDB types: `inet4`, `inet6`

Example:

```php
<?php

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\Doctrine\IPAddressType;
use Arokettu\IP\Doctrine\VendorSpecific\PostgreSQL\InetType;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Column;

// first register types you need
Type::addType(IPAddressType::NAME, IPAddressType::class);

// native type should also be registered in the platform
Type::addType(InetType::NAME, InetType::class);

$db = DriverManager::getConnection(/* ... */); // when initializing DBAL
$db->getDatabasePlatform()->registerDoctrineTypeMapping(InetType::NATIVE_TYPE, InetType::NAME);

// apply to the object:

class Model
{
    #[Column(type: IPAddressType::NAME)]
    public AnyIPAddress $ip;

    #[Column(type: InetType::NAME)]
    public AnyIPAddress $native_ip;
}
```

## Documentation

Read full documentation here: <https://sandfox.dev/php/ip-address-doctrine.html>

Also on Read the Docs: <https://arokettu-ip-address-doctrine.readthedocs.io/>

## Support

Please file issues on our main repo at GitLab: <https://gitlab.com/sandfox/ip-address-doctrine/-/issues>

Feel free to ask any questions in our room on Gitter: <https://gitter.im/arokettu/community>

## License

The library is available as open source under the terms of the [MIT License][License Link].

[arokettu/ip-address]: https://sandfox.dev/php/ip-address.html
