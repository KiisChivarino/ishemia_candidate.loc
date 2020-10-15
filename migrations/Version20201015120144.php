<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201015120144 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE plan_appointment ADD time_range_id INT NOT NULL');
        $this->addSql('ALTER TABLE plan_appointment ADD time_range_count INT NOT NULL');
        $this->addSql('ALTER TABLE plan_appointment DROP date_begin');
        $this->addSql('ALTER TABLE plan_appointment DROP date_end');
        $this->addSql('COMMENT ON COLUMN plan_appointment.time_range_count IS \'Срок выполнения\'');
        $this->addSql('ALTER TABLE plan_appointment ADD CONSTRAINT FK_A81202F8E07937D FOREIGN KEY (time_range_id) REFERENCES time_range (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A81202F8E07937D ON plan_appointment (time_range_id)');
        $this->addSql('ALTER TABLE plan_testing ADD time_range_id INT NOT NULL');
        $this->addSql('ALTER TABLE plan_testing DROP time_range');
        $this->addSql('ALTER TABLE plan_testing ADD CONSTRAINT FK_564C120E8E07937D FOREIGN KEY (time_range_id) REFERENCES time_range (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_564C120E8E07937D ON plan_testing (time_range_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('ALTER TABLE plan_appointment DROP CONSTRAINT FK_A81202F8E07937D');
        $this->addSql('DROP INDEX IDX_A81202F8E07937D');
        $this->addSql('ALTER TABLE plan_appointment ADD date_begin DATE NOT NULL');
        $this->addSql('ALTER TABLE plan_appointment ADD date_end DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE plan_appointment DROP time_range_id');
        $this->addSql('ALTER TABLE plan_appointment DROP time_range_count');
        $this->addSql('COMMENT ON COLUMN plan_appointment.date_begin IS \'Дата начала приема\'');
        $this->addSql('COMMENT ON COLUMN plan_appointment.date_end IS \'Дата завершения приема\'');
        $this->addSql('ALTER TABLE plan_testing DROP CONSTRAINT FK_564C120E8E07937D');
        $this->addSql('DROP INDEX IDX_564C120E8E07937D');
        $this->addSql('ALTER TABLE plan_testing ADD time_range VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE plan_testing DROP time_range_id');
    }
}
