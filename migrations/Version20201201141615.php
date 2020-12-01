<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201201141615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE patient_testing ADD plan_testing_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_testing ADD is_by_plan BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('COMMENT ON COLUMN patient_testing.plan_testing_id IS \'Ключ анализа по плану\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.is_by_plan IS \'Флаг: обследование по плану\'');
        $this->addSql('ALTER TABLE patient_testing ADD CONSTRAINT FK_B5900FED6B419504 FOREIGN KEY (plan_testing_id) REFERENCES plan_testing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B5900FED6B419504 ON patient_testing (plan_testing_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('ALTER TABLE patient_testing DROP CONSTRAINT FK_B5900FED6B419504');
        $this->addSql('DROP INDEX IDX_B5900FED6B419504');
        $this->addSql('ALTER TABLE patient_testing DROP plan_testing_id');
        $this->addSql('ALTER TABLE patient_testing DROP is_by_plan');
    }
}
