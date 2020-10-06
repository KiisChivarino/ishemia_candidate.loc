<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201006113442 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE gender_id_seq CASCADE');
        $this->addSql('COMMENT ON COLUMN analysis_rate.gender_id IS \'Ключ пола\'');
        $this->addSql('COMMENT ON COLUMN gender.id IS \'Ключ пола\'');
        $this->addSql('COMMENT ON COLUMN gender.name IS \'Название пола\'');
        $this->addSql('COMMENT ON COLUMN gender.title IS \'Заголовок пола\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('CREATE SEQUENCE gender_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('COMMENT ON COLUMN gender.id IS NULL');
        $this->addSql('COMMENT ON COLUMN gender.name IS NULL');
        $this->addSql('COMMENT ON COLUMN gender.title IS NULL');
        $this->addSql('COMMENT ON COLUMN analysis_rate.gender_id IS NULL');
    }
}
