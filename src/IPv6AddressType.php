<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPv6Address;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use InvalidArgumentException;

final class IPv6AddressType extends AbstractType
{
    public const NAME = 'arokettu_ipv6';
    protected const CLASS_TITLE = 'IPv6Address';
    protected const BASE_CLASSES = [
        IPv6Address::class,
    ];
    protected const LENGTH = Values::IPV6_LENGTH;

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv6Address) {
            return $address->toString();
        }

        throw new InvalidArgumentException();
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv6Address::fromString($address);
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv6Address::fromString($address);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = self::LENGTH;
        return $platform->getStringTypeDeclarationSQL($column);
    }
}
