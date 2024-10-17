<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPv4Block;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use InvalidArgumentException;

final class IPv4BlockType extends AbstractType
{
    public const NAME = 'arokettu_ipv4_cidr';

    protected const CLASS_TITLE = 'IPv4Block';
    protected const BASE_CLASSES = [
        IPv4Block::class,
    ];
    protected const LENGTH = Values::IPV4_CIDR_LENGTH;

    protected function addressToDbString(AnyIPBlock|AnyIPAddress $address): string
    {
        if ($address instanceof IPv4Block) {
            return $address->toString();
        }

        throw new InvalidArgumentException();
    }

    protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv4Block::fromString($address, strict: true); // do not read garbage from the database
    }

    protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock
    {
        return IPv4Block::fromString($address); // lax
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = self::LENGTH;
        return $platform->getStringTypeDeclarationSQL($column);
    }
}
