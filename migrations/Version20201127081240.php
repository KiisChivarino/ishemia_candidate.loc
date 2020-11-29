<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201127081240 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE patient_appointment ADD is_first BOOLEAN NOT NULL');
        $this->addSql('COMMENT ON COLUMN patient_appointment.is_first IS \'Флаг первого приема при заведении истории болезни\'');
        $this->addSql('ALTER TABLE patient_testing ADD is_first BOOLEAN NOT NULL');
        $this->addSql('COMMENT ON COLUMN patient_testing.is_first IS \'Флаг первого обследования по плану при заведении истории болезни\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('ALTER TABLE patient_testing DROP is_first');
        $this->addSql('ALTER TABLE patient_appointment DROP is_first');
    }
}
