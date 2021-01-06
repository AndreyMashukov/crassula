<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210105230543 extends AbstractMigration
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
        $this->addSql('CREATE TABLE rate (rte_id INT AUTO_INCREMENT NOT NULL, rte_source VARCHAR(255) NOT NULL, rte_name VARCHAR(255) NOT NULL, rte_main VARCHAR(3) NOT NULL, rte_secondary VARCHAR(3) NOT NULL, rte_rate DOUBLE PRECISION NOT NULL, rte_eid VARCHAR(50) NOT NULL, rte_date DATE NOT NULL, PRIMARY KEY(rte_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE rate');
    }
}
