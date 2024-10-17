<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPv4Address;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use InvalidArgumentException;

final class IPv4AddressBinaryType extends AbstractType
{
    public const NAME = 'arokettu_ipv4_bin';

    protected const CLASS_TITLE = 'IPAddress';
    protected const BASE_CLASSES = [
        IPv4Address::class,
    ];
    protected const LENGTH = Values::IPV4_BYTES;

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv4Address) {
            return $address->getBytes();
        }

        throw new InvalidArgumentException();
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv4Address::fromBytes($address);
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv4Address::fromBytes($address);
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