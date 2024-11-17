Database Agnostic Types
#######################

These types can be used with any database supported by Doctrine.

Groups
======

Types can be divided in several groups:

* By IP version

  * IPv4 only
  * IPv6 only
  * Both IPv4 and IPv6
* By storage

  * Text
  * Binary
* By values

  * Single IP addresses
  * CIDR blocks

All Types
=========

``Arokettu\IP\Doctrine\*Type``

All combinations of the params above exist.

+-------------+---------------------+---------------------------+-------------------+-------------------------+
|             | IP Address                                      | CIDR Block                                  |
+-------------+---------------------+---------------------------+-------------------+-------------------------+
|             | Text                | Binary                    | Text              | Binary                  |
+=============+=====================+===========================+===================+=========================+
| IPv4        | ``IPv4AddressType`` | ``IPv4AddressBinaryType`` | ``IPv4BlockType`` | ``IPv4BlockBinaryType`` |
+-------------+---------------------+---------------------------+-------------------+-------------------------+
| IPv6        | ``IPv6AddressType`` | ``IPv6AddressBinaryType`` | ``IPv6BlockType`` | ``IPv6BlockBinaryType`` |
+-------------+---------------------+---------------------------+-------------------+-------------------------+
| IPv4 + IPv6 | ``IPAddressType``   | ``IPAddressBinaryType``   | ``IPBlockType``   | ``IPBlockBinaryType``   |
+-------------+---------------------+---------------------------+-------------------+-------------------------+

IPv6 and Varchar
================

Please note that there is more than one way to express a human-readable IPv6.
For example, mapped IPv4 ``192.168.0.123`` (``0x00000000000000000000ffffc0a8007b``) can be written
as ``::ffff:192.168.0.123``, as ``::ffff:c0a8:7b``, and as ``0000:0000:0000:0000:0000:ffff:c0a8:007b``.
Therefore if you need to search for duplicate IPs,
especially if this library is not the only code that writes to your database,
binary form should be preferred.
