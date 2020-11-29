<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201127072310 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE patient_appointment DROP planned_time');
        $this->addSql('ALTER TABLE patient_testing DROP planned_date');
        $this->addSql('COMMENT ON COLUMN plan_testing.time_range_count IS \'Срок выполнения\'');
        $this->addSql('ALTER TABLE prescription_appointment ADD planned_date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.planned_date_time IS \'Назначенные дата и время проведения приема\'');
        $this->addSql('ALTER TABLE prescription_testing ADD planned_date DATE NOT NULL');
        $this->addSql('COMMENT ON COLUMN prescription_testing.planned_date IS \'Назначенная дата проведения обследования\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('COMMENT ON COLUMN plan_testing.time_range_count IS NULL');
        $this->addSql('ALTER TABLE patient_testing ADD planned_date DATE NOT NULL');
        $this->addSql('COMMENT ON COLUMN patient_testing.planned_date IS \'Планируемая дата проведения тестирования\'');
        $this->addSql('ALTER TABLE patient_appointment ADD planned_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN patient_appointment.planned_time IS \'Дата и время приема по плану\'');
        $this->addSql('ALTER TABLE prescription_testing DROP planned_date');
        $this->addSql('ALTER TABLE prescription_appointment DROP planned_date_time');
    }
}
