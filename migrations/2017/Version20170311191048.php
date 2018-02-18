<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170311191048 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE `pa_group` ADD `nextVs` int(4) unsigned NULL AFTER `ks`;");
    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE `pa_group` DROP `nextVs`;");
    }
}