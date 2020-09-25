<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200925103926 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE risk_factor DROP CONSTRAINT fk_2df3e264dbcdf493');
        $this->addSql('DROP SEQUENCE risk_factor_id_seq CASCADE');
        $this->addSql('DROP TABLE risk_factor');
        $this->addSql('DROP TABLE risk_factor_type');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('CREATE SEQUENCE risk_factor_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE risk_factor (id INT NOT NULL, risk_factor_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, scores INT NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_2df3e264dbcdf493 ON risk_factor (risk_factor_type_id)');
        $this->addSql('COMMENT ON COLUMN risk_factor.id IS \'Ключ фактора риска\'');
        $this->addSql('COMMENT ON COLUMN risk_factor.risk_factor_type_id IS \'Ключ типа фактора риска\'');
        $this->addSql('COMMENT ON COLUMN risk_factor.name IS \'Название\'');
        $this->addSql('COMMENT ON COLUMN risk_factor.scores IS \'Количество баллов\'');
        $this->addSql('COMMENT ON COLUMN risk_factor.enabled IS \'Ограничение использования\'');
        $this->addSql('CREATE TABLE risk_factor_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN risk_factor_type.id IS \'Ключ типа фактора риска\'');
        $this->addSql('COMMENT ON COLUMN risk_factor_type.name IS \'Название\'');
        $this->addSql('COMMENT ON COLUMN risk_factor_type.enabled IS \'Ограничение использования\'');
        $this->addSql('ALTER TABLE risk_factor ADD CONSTRAINT fk_2df3e264dbcdf493 FOREIGN KEY (risk_factor_type_id) REFERENCES risk_factor_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
