<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201204064640 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE medical_history ADD clinical_diagnosis TEXT NOT NULL DEFAULT \'Клинический диагноз отсутствует!\'');
        $this->addSql('ALTER TABLE medical_history ALTER main_disease_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN medical_history.clinical_diagnosis IS \'Клинический диагноз\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('ALTER TABLE medical_history DROP clinical_diagnosis');
        $this->addSql('ALTER TABLE medical_history ALTER main_disease_id SET NOT NULL');
    }
}
