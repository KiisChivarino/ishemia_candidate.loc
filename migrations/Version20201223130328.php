<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201223130328 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return '';
    }

    /**
     * @param Schema $schema
     * @throws DBALException
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE prescription_medicine DROP CONSTRAINT fk_effcda9a2527130b');
        $this->addSql('DROP SEQUENCE reception_method_id_seq CASCADE');
        $this->addSql('DROP TABLE reception_method');
        $this->addSql('ALTER TABLE prescription_medicine DROP CONSTRAINT fk_effcda9a2f7d140a');
        $this->addSql('DROP INDEX idx_effcda9a2f7d140a');
        $this->addSql('DROP INDEX idx_effcda9a2527130b');
        $this->addSql('ALTER TABLE prescription_medicine DROP medicine_id');
        $this->addSql('ALTER TABLE prescription_medicine DROP reception_method_id');
        $this->addSql('ALTER TABLE prescription_medicine DROP instruction');
    }

    /**
     * @param Schema $schema
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('CREATE SEQUENCE reception_method_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE reception_method (id INT NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE reception_method IS \'Способ приема препарата\'');
        $this->addSql('COMMENT ON COLUMN reception_method.id IS \'Ключ способа приема препарата\'');
        $this->addSql('COMMENT ON COLUMN reception_method.name IS \'Название способа приема\'');
        $this->addSql('COMMENT ON COLUMN reception_method.enabled IS \'Ограничение использования\'');
        $this->addSql('ALTER TABLE prescription_medicine ADD medicine_id INT NOT NULL');
        $this->addSql('ALTER TABLE prescription_medicine ADD reception_method_id INT NOT NULL');
        $this->addSql('ALTER TABLE prescription_medicine ADD instruction TEXT NOT NULL');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.medicine_id IS \'Ключ препарата\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.reception_method_id IS \'Ключ способа приема препарата\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.instruction IS \'Инструкция по применению\'');
        $this->addSql('ALTER TABLE prescription_medicine ADD CONSTRAINT fk_effcda9a2f7d140a FOREIGN KEY (medicine_id) REFERENCES medicine (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prescription_medicine ADD CONSTRAINT fk_effcda9a2527130b FOREIGN KEY (reception_method_id) REFERENCES reception_method (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_effcda9a2f7d140a ON prescription_medicine (medicine_id)');
        $this->addSql('CREATE INDEX idx_effcda9a2527130b ON prescription_medicine (reception_method_id)');
    }
}
