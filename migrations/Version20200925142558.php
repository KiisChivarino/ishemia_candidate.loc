<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200925142558 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE plan_appointment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE plan_appointment (id INT NOT NULL, date_begin DATE NOT NULL, date_end DATE DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN plan_appointment.date_begin IS \'Дата начала приема\'');
        $this->addSql('COMMENT ON COLUMN plan_appointment.date_end IS \'Дата завершения приема\'');
        $this->addSql('COMMENT ON COLUMN plan_appointment.enabled IS \'Ограничение использования\'');
        $this->addSql('ALTER TABLE complaint ALTER enabled SET DEFAULT \'true\'');
        $this->addSql('COMMENT ON COLUMN complaint.enabled IS \'Ограничение использования\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE plan_appointment_id_seq CASCADE');
        $this->addSql('DROP TABLE plan_appointment');
        $this->addSql('ALTER TABLE complaint ALTER enabled DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN complaint.enabled IS NULL');
    }
}
