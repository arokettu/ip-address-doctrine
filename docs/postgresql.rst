PostgreSQL
##########

.. note:: PostgreSQL doc: https://www.postgresql.org/docs/current/datatype-net-types.html

``inet``
========

``\Arokettu\IP\Doctrine\VendorSpecific\PostgreSQL\InetType``

Accepts any ``IPv4Address`` and ``IPv6Address``.
``INET`` is designed to store plain IP addresses but for some reason also supports an optional CIDR prefix.
This library does not support this and will fail if it encounters such values in the database.

``cidr``
========

``\Arokettu\IP\Doctrine\VendorSpecific\PostgreSQL\CidrType``

Accepts any ``IPv4Block`` and ``IPv6Block``.
