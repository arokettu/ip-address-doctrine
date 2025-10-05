<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

// phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.IncorrectWhitespaceBeforeDeclare
declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Demo;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

require __DIR__ . '/../../BaseModel.php';

#[Entity, Table(name: 'ip_test')]
final class SqliteIP extends BaseModel
{
}
