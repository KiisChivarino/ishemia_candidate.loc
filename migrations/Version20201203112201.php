<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201203112201 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE patient_appointment ADD plan_appointment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_appointment ADD is_by_plan BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('COMMENT ON COLUMN patient_appointment.is_by_plan IS \'Флаг: прием по плану\'');
        $this->addSql('ALTER TABLE patient_appointment ADD CONSTRAINT FK_CE3BC70B5A6B53F3 FOREIGN KEY (plan_appointment_id) REFERENCES plan_appointment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_CE3BC70B5A6B53F3 ON patient_appointment (plan_appointment_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE patient_appointment DROP CONSTRAINT FK_CE3BC70B5A6B53F3');
        $this->addSql('DROP INDEX IDX_CE3BC70B5A6B53F3');
        $this->addSql('ALTER TABLE patient_appointment DROP plan_appointment_id');
        $this->addSql('ALTER TABLE patient_appointment DROP is_by_plan');
    }
}
