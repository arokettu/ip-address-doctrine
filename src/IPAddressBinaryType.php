<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPAddress;
use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv6Address;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use InvalidArgumentException;

final class IPAddressBinaryType extends AbstractType
{
    public const NAME = 'arokettu_ip_bin';

    protected const CLASS_TITLE = 'IPAddress';
    protected const BASE_CLASSES = [
        IPv4Address::class,
        IPv6Address::class,
    ];
    protected const LENGTH = Values::IPV6_BYTES;
    protected const BINARY = true;

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv4Address || $address instanceof IPv6Address) {
            return $address->getBytes();
        }

        $this->throwInvalidArgumentException($address);
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPAddress::fromBytes($address);
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPAddress::fromString($address);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = self::LENGTH;
        $column['fixed'] = false;
        return $platform->getBinaryTypeDeclarationSQL($column);
    }

    public function getBindingType(): ParameterType
    {
        return ParameterType::BINARY;
    }
}
