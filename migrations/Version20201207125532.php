<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201207125532 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE plan_appointment ADD starting_point_id INT NOT NULL DEFAULT 1');
        $this->addSql('ALTER TABLE plan_appointment ADD CONSTRAINT FK_A81202FF39C0FE7 FOREIGN KEY (starting_point_id) REFERENCES starting_point (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A81202FF39C0FE7 ON plan_appointment (starting_point_id)');
        $this->addSql('ALTER TABLE plan_testing ALTER starting_point_id DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('ALTER TABLE plan_appointment DROP CONSTRAINT FK_A81202FF39C0FE7');
        $this->addSql('DROP INDEX IDX_A81202FF39C0FE7');
        $this->addSql('ALTER TABLE plan_appointment DROP starting_point_id');
        $this->addSql('ALTER TABLE plan_testing ALTER starting_point_id SET DEFAULT 1');
    }
}
