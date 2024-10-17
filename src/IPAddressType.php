<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPAddress;
use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv6Address;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class IPAddressType extends AbstractType
{
    public const NAME = 'arokettu_ip';

    protected const CLASS_TITLE = 'IPAddress';
    protected const BASE_CLASSES = [
        IPv4Address::class,
        IPv6Address::class,
    ];
    protected const LENGTH = Values::IPV6_LENGTH;

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv4Address || $address instanceof IPv6Address) {
            return $address->toString();
        }

        $this->throwInvalidArgumentException($address);
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPAddress::fromString($address);
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPAddress::fromString($address);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = self::LENGTH;
        $column['fixed'] = false;
        return $platform->getStringTypeDeclarationSQL($column);
    }
}
