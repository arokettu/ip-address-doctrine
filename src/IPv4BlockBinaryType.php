<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPv4Block;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class IPv4BlockBinaryType extends AbstractType
{
    public const NAME = 'arokettu_ipv4_cidr_bin';

    protected const CLASS_TITLE = 'IPv4Block';
    protected const BASE_CLASSES = [
        IPv4Block::class,
    ];
    protected const LENGTH = Values::IPV4_CIDR_BYTES;
    protected const BINARY = true;

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv4Block) {
            return $address->getBytes() . \chr($address->getPrefix());
        }

        $this->throwInvalidArgumentException($address);
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        $bytes = substr($address, 0, -1);
        $prefix = substr($address, -1);

        return IPv4Block::fromBytes($bytes, \ord($prefix));
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv4Block::fromString($address); // lax
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
