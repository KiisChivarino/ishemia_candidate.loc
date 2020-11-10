<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201110143505 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE notification ALTER text DROP NOT NULL');
        $this->addSql('ALTER TABLE patient ALTER city_id DROP NOT NULL');
        $this->addSql('ALTER TABLE patient ALTER date_birth SET NOT NULL');
        $this->addSql('ALTER TABLE patient_testing ALTER medical_record_id SET NOT NULL');
        $this->addSql('ALTER TABLE patient_testing_result ALTER analysis_rate_id SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE notification ALTER text SET NOT NULL');
        $this->addSql('ALTER TABLE patient_testing ALTER medical_record_id DROP NOT NULL');
        $this->addSql('ALTER TABLE patient_testing_result ALTER analysis_rate_id DROP NOT NULL');
        $this->addSql('ALTER TABLE patient ALTER city_id SET NOT NULL');
        $this->addSql('ALTER TABLE patient ALTER date_birth DROP NOT NULL');
    }
}
