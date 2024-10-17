<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPBlock;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class IPBlockType extends AbstractType
{
    public const NAME = 'arokettu_ip_cidr';

    protected const CLASS_TITLE = 'IPBlock';
    protected const BASE_CLASSES = [
        IPv4Block::class,
        IPv6Block::class,
    ];
    protected const LENGTH = Values::IPV6_CIDR_LENGTH;

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv4Block || $address instanceof IPv6Block) {
            return $address->toString();
        }

        $this->throwInvalidArgumentException($address);
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPBlock::fromString($address, strict: true); // do not read garbage from the database
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPBlock::fromString($address); // lax
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = self::LENGTH;
        return $platform->getStringTypeDeclarationSQL($column);
    }
}
