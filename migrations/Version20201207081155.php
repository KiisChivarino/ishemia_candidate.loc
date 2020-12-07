<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201207081155 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE starting_point_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE starting_point (id INT NOT NULL, name VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE starting_point IS \'Точка отсчета\'');
        $this->addSql('COMMENT ON COLUMN starting_point.name IS \'Имя свойства для точки отсчета добавления обследований по плану\'');
        $this->addSql('COMMENT ON COLUMN starting_point.title IS \'Заголовок точки отсчета добавления обследований по плану\'');
        $this->addSql('ALTER TABLE template_parameter DROP serial_number');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('DROP SEQUENCE starting_point_id_seq CASCADE');
        $this->addSql('DROP TABLE starting_point');
        $this->addSql('ALTER TABLE template_parameter ADD serial_number INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN template_parameter.serial_number IS \'Порядковый номер\'');
    }
}
