<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\SerializationFailed;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Stringable;
use TypeError;
use UnexpectedValueException;

use function Arokettu\IsResource\try_get_resource_type;

abstract class AbstractType extends Type
{
    public const NAME = '';
    protected const CLASS_TITLE = '';
    protected const BASE_CLASSES = [];
    protected const LENGTH = 0;
    protected const LENGTH_VARIABLE = false;

    abstract protected function addressToDbString(AnyIPAddress|AnyIPBlock $address): string;
    abstract protected function dbStringToAddress(string $address): AnyIPAddress|AnyIPBlock;
    abstract protected function externalStringToAddress(string $address): AnyIPAddress|AnyIPBlock;

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): AnyIPAddress|AnyIPBlock|null
    {
        if ($value === null) {
            return $value;
        }

        foreach (self::BASE_CLASSES as $class) {
            if (\is_a($value, $class)) {
                return $value;
            }
        }

        if (try_get_resource_type($value) === 'stream') {
            // Read $LENGTH bytes. If the steam is longer than $LENGTH bytes, crash, no need to read it whole
            $value = stream_get_contents($value, static::LENGTH + 1);
        }

        try {
            return $this->dbStringToAddress((string)$value);
        } catch (TypeError | UnexpectedValueException | InvalidArgumentException) {
            throw ValueNotConvertible::new(
                $value,
                static::NAME,
                sprintf('Not a valid %s representation', static::CLASS_TITLE)
            );
        }
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string|null
    {
        if ($value === null) {
            return null;
        }

        try {
            if ($value instanceof AnyIPAddress || $value instanceof AnyIPBlock) {
                return $this->addressToDbString($value);
            }

            if (\is_string($value) || $value instanceof Stringable) {
                $value = $this->externalStringToAddress((string)$value);
                return $this->addressToDbString($value);
            }
        } catch (TypeError | UnexpectedValueException $e) {
            throw SerializationFailed::new(
                $value,
                static::NAME,
                sprintf('Not a valid %s representation', static::CLASS_TITLE),
                $e
            );
        }

        throw InvalidType::new($value, static::NAME, ['null', 'string', ...static::BASE_CLASSES]);
    }
}
