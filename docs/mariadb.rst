MariaDB
#######

.. highlight:: php

These types are specific to MariaDB.

``inet4``
=========

``\Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet4Type``

.. note:: MariaDB doc: https://mariadb.com/kb/en/inet4/

IPv4 address. Plain and simple.

``inet6``
=========

``\Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet6Type``

.. note:: MariaDB doc: https://mariadb.com/kb/en/inet6/

IPv6 address.
If you store IPv4 in MariaDB natively, it will be converted to a mapped IPv6 address.
This library does not support this behavior.
To replicate this behavior, do this::

    <?php

    use Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet6Type;
    use Arokettu\IP\IPAddress;
    use Arokettu\IP\IPv6Address;

    class Model
    {
        #[Column(type: Inet6Type::NAME)]
        public IPv6Address $ip; // only v6 is supported
    }

    $ip = IPAddress::fromString('192.168.0.1'); // but we have v4

    $model = new Model();
    $model->ip = $ip->toMappedIPv6(); // will be stored as ::ffff:192.168.0.1
