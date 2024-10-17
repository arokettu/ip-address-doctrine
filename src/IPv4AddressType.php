<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPv4Address;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class IPv4AddressType extends AbstractType
{
    public const NAME = 'arokettu_ipv4';

    protected const CLASS_TITLE = 'IPv4Address';
    protected const BASE_CLASSES = [
        IPv4Address::class,
    ];
    protected const LENGTH = Values::IPV4_LENGTH;

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv4Address) {
            return $address->toString();
        }

        $this->throwInvalidArgumentException($address);
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv4Address::fromString($address);
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv4Address::fromString($address);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = self::LENGTH;
        $column['fixed'] = false;
        return $platform->getStringTypeDeclarationSQL($column);
    }
}
