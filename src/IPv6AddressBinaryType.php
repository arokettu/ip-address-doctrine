<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv6Address;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class IPv6AddressBinaryType extends AbstractType
{
    public const NAME = 'arokettu_ipv6_bin';

    protected const CLASS_TITLE = 'IPv6Address';
    protected const BASE_CLASSES = [
        IPv4Address::class,
    ];
    protected const LENGTH = Values::IPV6_BYTES;
    protected const BINARY = true;

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv6Address) {
            return $address->getBytes();
        }

        $this->throwInvalidArgumentException($address);
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv6Address::fromBytes($address);
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv6Address::fromString($address);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = self::LENGTH;
        $column['fixed'] = true;
        return $platform->getBinaryTypeDeclarationSQL($column);
    }

    public function getBindingType(): ParameterType
    {
        return ParameterType::BINARY;
    }
}
