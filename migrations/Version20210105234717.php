<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210105234717 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX IDX_DFEC3F39FAD093291FA7465B6656B2C2EF666937 ON rate (rte_date, rte_source, rte_secondary, rte_main)');
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_DFEC3F39FAD093291FA7465B6656B2C2EF666937 ON rate');
    }
}
