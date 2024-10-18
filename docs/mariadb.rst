MariaDB
#######

.. highlight:: php

``inet4``
=========

``\Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet4Type``

IPv4 address. Plain and simple.

``inet6``
=========

``\Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet6Type``

IPv6 address.
If you store IPv4 in MariaDB natively, it will be converted to a mapped IPv6 address.
This library does not support this.
To replicate this behavior, do this::

    <?php

    use Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet6Type;
    use Arokettu\IP\IPAddress;
    use Arokettu\IP\IPv6Address;

    class Model
    {
        #[Column(type: Inet6Type::NAME)]
        public IPv6Address $ip;
    }

    $ip = IPAddress::fromString('192.168.0.1');

    $model = new Model();
    $model = $ip->toMappedIPv6(); // will be stored as ::ffff:192.168.0.1
