<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

// phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.IncorrectWhitespaceBeforeDeclare
declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Demo;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\Doctrine\VendorSpecific\PostgreSQL\CidrType;
use Arokettu\IP\Doctrine\VendorSpecific\PostgreSQL\InetType;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

require __DIR__ . '/../../BaseModel.php';

#[Entity, Table(name: 'ip_test')]
class PostgresIP extends BaseModel
{
    #[Column(type: InetType::NAME, nullable: true)]
    public AnyIPAddress|null $inet;

    #[Column(type: CidrType::NAME, nullable: true)]
    public AnyIPBlock|null $cidr;
}
