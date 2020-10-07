<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201007122910 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE blog_item DROP CONSTRAINT fk_ffcba98ee8839d43');
        $this->addSql('DROP SEQUENCE blog_item_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE blog_record_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE patient_file_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE patient_file (id INT NOT NULL, patient_id INT NOT NULL, file_name VARCHAR(255) NOT NULL, uploaded_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_50E7BD86B899279 ON patient_file (patient_id)');
        $this->addSql('COMMENT ON COLUMN patient_file.patient_id IS \'Ключ пациента\'');
        $this->addSql('ALTER TABLE patient_file ADD CONSTRAINT FK_50E7BD86B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE blog_record');
        $this->addSql('DROP TABLE blog_item');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('DROP SEQUENCE patient_file_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE blog_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE blog_record_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE blog_record (id INT NOT NULL, date_begin DATE NOT NULL, date_end DATE NOT NULL, version VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE blog_item (id INT NOT NULL, blog_record_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, duration INT DEFAULT NULL, completed BOOLEAN NOT NULL, project VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_ffcba98ee8839d43 ON blog_item (blog_record_id)');
        $this->addSql('ALTER TABLE blog_item ADD CONSTRAINT fk_ffcba98ee8839d43 FOREIGN KEY (blog_record_id) REFERENCES blog_record (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE patient_file');
    }
}
