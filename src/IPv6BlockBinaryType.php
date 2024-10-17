<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPv6Block;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class IPv6BlockBinaryType extends AbstractType
{
    public const NAME = 'arokettu_ipv6_cidr_bin';

    protected const CLASS_TITLE = 'IPv6Block';
    protected const BASE_CLASSES = [
        IPv6Block::class,
    ];
    protected const LENGTH = Values::IPV6_CIDR_BYTES;

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv6Block) {
            return $address->getBytes() . \chr($address->getPrefix());
        }

        $this->throwInvalidArgumentException($address);
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        $bytes = substr($address, 0, -1);
        $prefix = substr($address, -1);

        return IPv6Block::fromBytes($bytes, \ord($prefix));
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv6Block::fromString($address); // lax
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
