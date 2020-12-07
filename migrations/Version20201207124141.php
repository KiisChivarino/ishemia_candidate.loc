<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201207124141 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE patient ALTER heart_attack_date SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN patient.heart_attack_date IS \'Дата возникновения инфаркта\'');
        $this->addSql('ALTER TABLE plan_testing ADD starting_point_id INT NOT NULL DEFAULT 1');
        $this->addSql('ALTER TABLE plan_testing ADD CONSTRAINT FK_564C120EF39C0FE7 FOREIGN KEY (starting_point_id) REFERENCES starting_point (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_564C120EF39C0FE7 ON plan_testing (starting_point_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE plan_testing DROP CONSTRAINT FK_564C120EF39C0FE7');
        $this->addSql('DROP INDEX IDX_564C120EF39C0FE7');
        $this->addSql('ALTER TABLE plan_testing DROP starting_point_id');
        $this->addSql('ALTER TABLE patient ALTER heart_attack_date DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN patient.heart_attack_date IS NULL');
    }
}
