<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210126082319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE reception_method_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE medicine_id_seq CASCADE');
        $this->addSql('DROP TABLE reception_method');
        $this->addSql('DROP TABLE medicine');
        $this->addSql('ALTER TABLE prescription_medicine ADD patient_medicine_id INT NOT NULL');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.patient_medicine_id IS \'Ключ лекарства по назначению\'');
        $this->addSql('ALTER TABLE prescription_medicine ADD CONSTRAINT FK_EFFCDA9A1F7E2B45 FOREIGN KEY (patient_medicine_id) REFERENCES patient_medicine (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EFFCDA9A1F7E2B45 ON prescription_medicine (patient_medicine_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE reception_method_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE medicine_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE reception_method (id INT NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE reception_method IS \'Способ приема препарата\'');
        $this->addSql('COMMENT ON COLUMN reception_method.id IS \'Ключ способа приема препарата\'');
        $this->addSql('COMMENT ON COLUMN reception_method.name IS \'Название способа приема\'');
        $this->addSql('COMMENT ON COLUMN reception_method.enabled IS \'Ограничение использования\'');
        $this->addSql('CREATE TABLE medicine (id INT NOT NULL, name VARCHAR(50) NOT NULL, description TEXT NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN medicine.id IS \'Ключ препарата\'');
        $this->addSql('COMMENT ON COLUMN medicine.name IS \'Название препарата\'');
        $this->addSql('COMMENT ON COLUMN medicine.description IS \'Описание использования\'');
        $this->addSql('COMMENT ON COLUMN medicine.enabled IS \'Ограничение использования\'');
        $this->addSql('ALTER TABLE prescription_medicine DROP CONSTRAINT FK_EFFCDA9A1F7E2B45');
        $this->addSql('DROP INDEX UNIQ_EFFCDA9A1F7E2B45');
        $this->addSql('ALTER TABLE prescription_medicine DROP patient_medicine_id');
    }
}
