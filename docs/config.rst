Configuration
#############

.. highlight:: php

Generally, follow the Doctrine instructions.

Plain Doctrine
==============

.. note:: Doctrine doc: https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/cookbook/custom-mapping-types.html

::

    <?php

    use Arokettu\IP\Doctrine\IPv6AddressBinaryType;
    use Arokettu\IP\Doctrine\IPv6BlockBinaryType;
    use Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet6Type;
    use Doctrine\DBAL\DriverManager;
    use Doctrine\DBAL\Types\Type;

    // register types you need
    Type::addType(IPv6AddressBinaryType::NAME, IPv6AddressBinaryType::class);
    Type::addType(IPv6BlockBinaryType::NAME, IPv6BlockBinaryType::class);

    // vendor specific types also need to be registered in the platform
    Type::addType(Inet6Type::NAME, Inet6Type::class);

    $db = DriverManager::getConnection(/* ... */); // when initializing DBAL
    $db->getDatabasePlatform()->registerDoctrineTypeMapping(
        Inet6Type::NATIVE_TYPE,
        Inet6Type::NAME,
    );

Symfony
=======

.. note:: Symfony doc: https://symfony.com/doc/current/doctrine/dbal.html#registering-custom-mapping-types

.. code-block:: yaml

    # config/packages/doctrine.yaml
    doctrine:
      dbal:
        types:
          !php/const Arokettu\IP\Doctrine\IPv6AddressBinaryType::NAME:
            Arokettu\IP\Doctrine\IPv6AddressBinaryType
          !php/const Arokettu\IP\Doctrine\IPv6BlockBinaryType::NAME:
            Arokettu\IP\Doctrine\IPv6BlockBinaryType
          !php/const Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet6Type::NAME:
            Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet6Type
        mapping_types:
          inet6: !php/const Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet6Type::NAME

Applying Types to Models
========================

::

    <?php

    use Arokettu\IP\Doctrine\IPBlockType;
    use Arokettu\IP\Doctrine\IPv6AddressType;
    use Arokettu\IP\IPv4Block;
    use Arokettu\IP\IPv6Address;
    use Arokettu\IP\IPv6Block;
    use Doctrine\ORM\Mapping\Column;
    use Doctrine\ORM\Mapping\Entity;

    #[Entity]
    class Model
    {
        #[Column(type: IPv6AddressType::NAME)]
        public IPv6Address $ip;

        #[Column(type: IPBlockType::NAME)]
        public IPv4Block|IPv6Block $block;
    }
