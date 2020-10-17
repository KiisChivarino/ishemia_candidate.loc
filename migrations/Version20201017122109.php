<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201017122109 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE date_interval (id INT NOT NULL, name VARCHAR(30) NOT NULL, title VARCHAR(30) NOT NULL, format VARCHAR(1) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE date_interval IS \'Интервал даты\'');
        $this->addSql('COMMENT ON COLUMN date_interval.id IS \'Ключ интервала\'');
        $this->addSql('COMMENT ON COLUMN date_interval.name IS \'Имя интервала\'');
        $this->addSql('COMMENT ON COLUMN date_interval.title IS \'Заголовок интервала\'');
        $this->addSql('COMMENT ON COLUMN date_interval.format IS \'Формат интервала\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('DROP TABLE date_interval');
    }
}
