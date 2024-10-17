<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPv6Block;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class IPv6BlockType extends AbstractType
{
    public const NAME = 'arokettu_ipv6_cidr';

    protected const CLASS_TITLE = 'IPv6Block';
    protected const BASE_CLASSES = [
        IPv6Block::class,
    ];
    protected const LENGTH = Values::IPV6_CIDR_LENGTH;

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv6Block) {
            return $address->toString();
        }

        $this->throwInvalidArgumentException($address);
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv6Block::fromString($address, strict: true); // do not read garbage from the database
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv6Block::fromString($address); // lax
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = self::LENGTH;
        $column['fixed'] = false;
        return $platform->getStringTypeDeclarationSQL($column);
    }
}
