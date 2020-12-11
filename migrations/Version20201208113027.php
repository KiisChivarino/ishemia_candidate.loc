<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201208113027 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE role (id INT NOT NULL, name VARCHAR(50) NOT NULL, tech_name VARCHAR(50) DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN role.id IS \'Ключ роли\'');
        $this->addSql('COMMENT ON COLUMN role.name IS \'Название роли\'');
        $this->addSql('COMMENT ON COLUMN role.tech_name IS \'Техническое название\'');
        $this->addSql('COMMENT ON COLUMN role.description IS \'Описание роли\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE oksm (id INT NOT NULL, a2 VARCHAR(2) NOT NULL, a3 VARCHAR(3) NOT NULL, n3 INT NOT NULL, caption VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE oksm IS \'ОКСМ\'');
        $this->addSql('COMMENT ON COLUMN oksm.id IS \'Ключ ОКСМ\'');
        $this->addSql('COMMENT ON COLUMN oksm.a2 IS \'Двузначный код страны\'');
        $this->addSql('COMMENT ON COLUMN oksm.a3 IS \'Трехзначный код страны\'');
        $this->addSql('COMMENT ON COLUMN oksm.n3 IS \'Числовой код страны\'');
        $this->addSql('COMMENT ON COLUMN oksm.caption IS \'Название страны\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE analysis_group (id INT NOT NULL, name VARCHAR(50) NOT NULL, full_name VARCHAR(255) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE analysis_group IS \'Группа анализов\'');
        $this->addSql('COMMENT ON COLUMN analysis_group.id IS \'Ключ группы анализов\'');
        $this->addSql('COMMENT ON COLUMN analysis_group.name IS \'Название группы анализов (аббревиатура)\'');
        $this->addSql('COMMENT ON COLUMN analysis_group.full_name IS \'Полное название группы анализов (расшифровка аббревиатуры)\'');
        $this->addSql('COMMENT ON COLUMN analysis_group.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE medical_record (id INT NOT NULL, medical_history_id INT NOT NULL, record_date DATE NOT NULL, comment TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_f06a283e3544ad9e ON medical_record (medical_history_id)');
        $this->addSql('COMMENT ON TABLE medical_record IS \'Запись в историю болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_record.id IS \'Ключ записи в историю болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_record.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_record.record_date IS \'Дата создания записи\'');
        $this->addSql('COMMENT ON COLUMN medical_record.comment IS \'Комментарий\'');
        $this->addSql('COMMENT ON COLUMN medical_record.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE notification_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, template TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE notification_type IS \'Тип уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification_type.id IS \'Ключ типа уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification_type.name IS \'Наименование типа уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification_type.template IS \'Шаблон типа уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification_type.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE notification (id INT NOT NULL, notification_type_id INT NOT NULL, medical_record_id INT NOT NULL, staff_id INT NOT NULL, medical_history_id INT NOT NULL, notification_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, text TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_bf5476cad0520624 ON notification (notification_type_id)');
        $this->addSql('CREATE INDEX idx_bf5476cad4d57cd ON notification (staff_id)');
        $this->addSql('CREATE INDEX idx_bf5476ca3544ad9e ON notification (medical_history_id)');
        $this->addSql('CREATE INDEX idx_bf5476cab88e2bb6 ON notification (medical_record_id)');
        $this->addSql('COMMENT ON TABLE notification IS \'Уведомление\'');
        $this->addSql('COMMENT ON COLUMN notification.id IS \'Ключ уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification.notification_type_id IS \'Ключ типа уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification.medical_record_id IS \'Ключ записи в историю болезни\'');
        $this->addSql('COMMENT ON COLUMN notification.staff_id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN notification.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN notification.notification_time IS \'Дата и время создания уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification.text IS \'Текст уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE analysis_rate (id INT NOT NULL, analysis_id INT NOT NULL, measure_id INT NOT NULL, gender_id INT DEFAULT NULL, rate_min DOUBLE PRECISION NOT NULL, rate_max DOUBLE PRECISION NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX analysis_rate_unique ON analysis_rate (analysis_id, measure_id, gender_id)');
        $this->addSql('CREATE INDEX idx_ee5f7ad2708a0e0 ON analysis_rate (gender_id)');
        $this->addSql('CREATE INDEX idx_ee5f7ad25da37d00 ON analysis_rate (measure_id)');
        $this->addSql('CREATE INDEX idx_ee5f7ad27941003f ON analysis_rate (analysis_id)');
        $this->addSql('COMMENT ON TABLE analysis_rate IS \'Референтные значения анализа\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.id IS \'Ключ нормальных значений\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.analysis_id IS \'Ключ анализа\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.measure_id IS \'Ключ единицы измерения\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.gender_id IS \'Ключ пола\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.rate_min IS \'Минимальное нормальное значение\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.rate_max IS \'Максимальное нормальное значение\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE measure (id INT NOT NULL, name_ru VARCHAR(10) NOT NULL, name_en VARCHAR(10) DEFAULT NULL, title VARCHAR(100) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN measure.id IS \'Ключ единицы измерения\'');
        $this->addSql('COMMENT ON COLUMN measure.name_ru IS \'Русское название единицы измерения\'');
        $this->addSql('COMMENT ON COLUMN measure.name_en IS \'Английское название единицы измерения\'');
        $this->addSql('COMMENT ON COLUMN measure.title IS \'Описание единицы измерения\'');
        $this->addSql('COMMENT ON COLUMN measure.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE gender (id INT NOT NULL, name VARCHAR(1) NOT NULL, title VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN gender.id IS \'Ключ пола\'');
        $this->addSql('COMMENT ON COLUMN gender.name IS \'Название пола\'');
        $this->addSql('COMMENT ON COLUMN gender.title IS \'Заголовок пола\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE patient_appointment_complaint (patient_appointment_id INT NOT NULL, complaint_id INT NOT NULL, PRIMARY KEY(patient_appointment_id, complaint_id))');
        $this->addSql('CREATE INDEX idx_7278ad23edae188e ON patient_appointment_complaint (complaint_id)');
        $this->addSql('CREATE INDEX idx_7278ad235fa482b2 ON patient_appointment_complaint (patient_appointment_id)');
        $this->addSql('COMMENT ON COLUMN patient_appointment_complaint.patient_appointment_id IS \'Ключ приема пациента\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE complaint (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE complaint IS \'Жалоба\'');
        $this->addSql('COMMENT ON COLUMN complaint.name IS \'Название жалобы\'');
        $this->addSql('COMMENT ON COLUMN complaint.description IS \'Описание жалобы\'');
        $this->addSql('COMMENT ON COLUMN complaint.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE medicine (id INT NOT NULL, name VARCHAR(50) NOT NULL, description TEXT NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN medicine.id IS \'Ключ препарата\'');
        $this->addSql('COMMENT ON COLUMN medicine.name IS \'Название препарата\'');
        $this->addSql('COMMENT ON COLUMN medicine.description IS \'Описание использования\'');
        $this->addSql('COMMENT ON COLUMN medicine.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE region (id INT NOT NULL, country_id INT NOT NULL, name VARCHAR(255) NOT NULL, region_number VARCHAR(8) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, oktmo_region_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_f62f176f92f3e70 ON region (country_id)');
        $this->addSql('COMMENT ON COLUMN region.id IS \'Ключ региона\'');
        $this->addSql('COMMENT ON COLUMN region.country_id IS \'Ключ страны\'');
        $this->addSql('COMMENT ON COLUMN region.name IS \'Название региона\'');
        $this->addSql('COMMENT ON COLUMN region.region_number IS \'Номер региона\'');
        $this->addSql('COMMENT ON COLUMN region.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE "position" (id INT NOT NULL, name VARCHAR(50) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE "position" IS \'Должность\'');
        $this->addSql('COMMENT ON COLUMN "position".id IS \'Ключ должности\'');
        $this->addSql('COMMENT ON COLUMN "position".name IS \'Название должности\'');
        $this->addSql('COMMENT ON COLUMN "position".enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE hospital (id INT NOT NULL, region_id INT NOT NULL, city_id INT DEFAULT NULL, lpu_id INT DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(50) NOT NULL, description TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, code VARCHAR(6) NOT NULL, email VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_4282c85b8bac62af ON hospital (city_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_4282c85bf2c7c2c1 ON hospital (lpu_id)');
        $this->addSql('CREATE INDEX idx_4282c85b98260155 ON hospital (region_id)');
        $this->addSql('COMMENT ON TABLE hospital IS \'Больница\'');
        $this->addSql('COMMENT ON COLUMN hospital.id IS \'Ключ больницы\'');
        $this->addSql('COMMENT ON COLUMN hospital.region_id IS \'Ключ региона\'');
        $this->addSql('COMMENT ON COLUMN hospital.city_id IS \'Ключ города\'');
        $this->addSql('COMMENT ON COLUMN hospital.lpu_id IS \'Ключ лечебно-профилактического учреждения\'');
        $this->addSql('COMMENT ON COLUMN hospital.address IS \'Адрес больницы\'');
        $this->addSql('COMMENT ON COLUMN hospital.name IS \'Название больницы\'');
        $this->addSql('COMMENT ON COLUMN hospital.phone IS \'Телефон для отправки смс\'');
        $this->addSql('COMMENT ON COLUMN hospital.description IS \'Описание или комментарий для больницы\'');
        $this->addSql('COMMENT ON COLUMN hospital.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN hospital.code IS \'Код больницы\'');
        $this->addSql('COMMENT ON COLUMN hospital.email IS \'Email больницы\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE city (id INT NOT NULL, region_id INT NOT NULL, district_id INT DEFAULT NULL, oktmo_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_2d5b023498260155 ON city (region_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_2d5b0234ee0c723d ON city (oktmo_id)');
        $this->addSql('CREATE INDEX idx_2d5b0234b08fa272 ON city (district_id)');
        $this->addSql('COMMENT ON COLUMN city.id IS \'Ключ города\'');
        $this->addSql('COMMENT ON COLUMN city.region_id IS \'Ключ региона\'');
        $this->addSql('COMMENT ON COLUMN city.district_id IS \'Ключ района\'');
        $this->addSql('COMMENT ON COLUMN city.oktmo_id IS \'Ключ ОКТМО\'');
        $this->addSql('COMMENT ON COLUMN city.name IS \'Название города\'');
        $this->addSql('COMMENT ON COLUMN city.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE lpu (id INT NOT NULL, oktmo_region_id INT DEFAULT NULL, region_name VARCHAR(100) NOT NULL, years VARCHAR(255) NOT NULL, code VARCHAR(6) NOT NULL, full_name VARCHAR(255) DEFAULT NULL, caption VARCHAR(255) NOT NULL, okopf VARCHAR(5) NOT NULL, post_code VARCHAR(6) DEFAULT NULL, address VARCHAR(255) NOT NULL, director_last_name VARCHAR(50) NOT NULL, director_first_name VARCHAR(50) NOT NULL, director_patronymic_name VARCHAR(50) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, fax VARCHAR(50) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, license VARCHAR(50) DEFAULT NULL, license_date DATE DEFAULT NULL, license_date_end DATE DEFAULT NULL, medical_care_types VARCHAR(255) DEFAULT NULL, include_date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE lpu IS \'ЛПУ\'');
        $this->addSql('COMMENT ON COLUMN lpu.id IS \'Ключ лечебно-профилактического учреждения\'');
        $this->addSql('COMMENT ON COLUMN lpu.oktmo_region_id IS \'Код ОКТМО региона\'');
        $this->addSql('COMMENT ON COLUMN lpu.region_name IS \'Название региона\'');
        $this->addSql('COMMENT ON COLUMN lpu.code IS \'Код ЛПУ\'');
        $this->addSql('COMMENT ON COLUMN lpu.full_name IS \'Полное наименование ЛПУ\'');
        $this->addSql('COMMENT ON COLUMN lpu.caption IS \'Краткое наименование ЛПУ\'');
        $this->addSql('COMMENT ON COLUMN lpu.okopf IS \'Код ОКОПФ\'');
        $this->addSql('COMMENT ON COLUMN lpu.post_code IS \'Почтовый индекс\'');
        $this->addSql('COMMENT ON COLUMN lpu.address IS \'Адрес\'');
        $this->addSql('COMMENT ON COLUMN lpu.director_last_name IS \'Фамилия руководителя\'');
        $this->addSql('COMMENT ON COLUMN lpu.director_first_name IS \'Имя руководителя\'');
        $this->addSql('COMMENT ON COLUMN lpu.director_patronymic_name IS \'Отчество руководителя\'');
        $this->addSql('COMMENT ON COLUMN lpu.phone IS \'Телефон\'');
        $this->addSql('COMMENT ON COLUMN lpu.fax IS \'Факс\'');
        $this->addSql('COMMENT ON COLUMN lpu.email IS \'Email\'');
        $this->addSql('COMMENT ON COLUMN lpu.license IS \'Номер лицензии\'');
        $this->addSql('COMMENT ON COLUMN lpu.license_date IS \'Дата лицензии\'');
        $this->addSql('COMMENT ON COLUMN lpu.license_date_end IS \'Дата завершения срока лицензии\'');
        $this->addSql('COMMENT ON COLUMN lpu.medical_care_types IS \'Виды медицинской помощи\'');
        $this->addSql('COMMENT ON COLUMN lpu.include_date IS \'Дата включения в реестр\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE country (id INT NOT NULL, name VARCHAR(30) NOT NULL, shortcode VARCHAR(4) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE country IS \'Страна\'');
        $this->addSql('COMMENT ON COLUMN country.id IS \'Ключ страны\'');
        $this->addSql('COMMENT ON COLUMN country.name IS \'Название страны\'');
        $this->addSql('COMMENT ON COLUMN country.shortcode IS \'Код страны в формате ISO\'');
        $this->addSql('COMMENT ON COLUMN country.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE oktmo (id INT NOT NULL, kod VARCHAR(11) NOT NULL, kod2 VARCHAR(11) NOT NULL, sub_kod1 INT NOT NULL, sub_kod2 INT DEFAULT NULL, sub_kod3 INT DEFAULT NULL, sub_kod4 INT DEFAULT NULL, p1 INT DEFAULT NULL, p2 INT DEFAULT NULL, kch INT DEFAULT NULL, name VARCHAR(300) NOT NULL, name2 VARCHAR(300) DEFAULT NULL, notes VARCHAR(255) DEFAULT NULL, federal_district_id INT NOT NULL, federal_district_name VARCHAR(255) NOT NULL, region_id INT NOT NULL, region_name VARCHAR(255) NOT NULL, settlement_type_id INT DEFAULT NULL, settlement_type_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE oktmo IS \'ОКТМО\'');
        $this->addSql('COMMENT ON COLUMN oktmo.id IS \'Ключ ОКТМО\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE date_interval (id INT NOT NULL, name VARCHAR(30) NOT NULL, title VARCHAR(30) NOT NULL, format VARCHAR(1) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE date_interval IS \'Интервал даты\'');
        $this->addSql('COMMENT ON COLUMN date_interval.id IS \'Ключ интервала\'');
        $this->addSql('COMMENT ON COLUMN date_interval.name IS \'Имя интервала\'');
        $this->addSql('COMMENT ON COLUMN date_interval.title IS \'Заголовок интервала\'');
        $this->addSql('COMMENT ON COLUMN date_interval.format IS \'Формат интервала\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE patient (id INT NOT NULL, auth_user_id INT NOT NULL, hospital_id INT NOT NULL, city_id INT DEFAULT NULL, district_id INT DEFAULT NULL, address VARCHAR(255) NOT NULL, sms_informing BOOLEAN DEFAULT \'true\' NOT NULL, email_informing BOOLEAN DEFAULT \'true\' NOT NULL, snils VARCHAR(14) DEFAULT NULL, insurance_number VARCHAR(50) DEFAULT NULL, passport VARCHAR(20) DEFAULT NULL, weight INT DEFAULT NULL, height INT DEFAULT NULL, date_birth DATE NOT NULL, passport_issue_date DATE DEFAULT NULL, passport_issuing_authority VARCHAR(255) DEFAULT NULL, passport_issuing_authority_code VARCHAR(7) DEFAULT NULL, heart_attack_date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_1adad7eb8bac62af ON patient (city_id)');
        $this->addSql('CREATE INDEX idx_1adad7eb63dbb69 ON patient (hospital_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_1adad7ebe94af366 ON patient (auth_user_id)');
        $this->addSql('CREATE INDEX idx_1adad7ebb08fa272 ON patient (district_id)');
        $this->addSql('COMMENT ON TABLE patient IS \'Пациент\'');
        $this->addSql('COMMENT ON COLUMN patient.id IS \'Ключ пациента\'');
        $this->addSql('COMMENT ON COLUMN patient.auth_user_id IS \'Ключ пользователя\'');
        $this->addSql('COMMENT ON COLUMN patient.hospital_id IS \'Ключ больницы\'');
        $this->addSql('COMMENT ON COLUMN patient.city_id IS \'Ключ города\'');
        $this->addSql('COMMENT ON COLUMN patient.district_id IS \'Ключ района\'');
        $this->addSql('COMMENT ON COLUMN patient.address IS \'Адрес пациента\'');
        $this->addSql('COMMENT ON COLUMN patient.sms_informing IS \'Флаг оповещения через смс\'');
        $this->addSql('COMMENT ON COLUMN patient.email_informing IS \'Флаг оповещения через email\'');
        $this->addSql('COMMENT ON COLUMN patient.snils IS \'СНИЛС пациента\'');
        $this->addSql('COMMENT ON COLUMN patient.insurance_number IS \'Номер страховки\'');
        $this->addSql('COMMENT ON COLUMN patient.passport IS \'Паспортный данные\'');
        $this->addSql('COMMENT ON COLUMN patient.weight IS \'Вес\'');
        $this->addSql('COMMENT ON COLUMN patient.height IS \'Рост\'');
        $this->addSql('COMMENT ON COLUMN patient.date_birth IS \'Дата рождения\'');
        $this->addSql('COMMENT ON COLUMN patient.passport_issue_date IS \'Дата выдачи паспорта\'');
        $this->addSql('COMMENT ON COLUMN patient.passport_issuing_authority IS \'Орган, выдавший паспорт\'');
        $this->addSql('COMMENT ON COLUMN patient.passport_issuing_authority_code IS \'Код органа, выдавшего паспорт\'');
        $this->addSql('COMMENT ON COLUMN patient.heart_attack_date IS \'Дата возникновения инфаркта\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE time_range (id INT NOT NULL, date_interval_id INT NOT NULL, title VARCHAR(30) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, multiplier INT DEFAULT 1 NOT NULL, is_regular BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_d6f5bb356f2301f2 ON time_range (date_interval_id)');
        $this->addSql('COMMENT ON TABLE time_range IS \'Временной диапазон\'');
        $this->addSql('COMMENT ON COLUMN time_range.date_interval_id IS \'Ключ интервала\'');
        $this->addSql('COMMENT ON COLUMN time_range.title IS \'Заголовок временного диапазона\'');
        $this->addSql('COMMENT ON COLUMN time_range.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN time_range.multiplier IS \'Множитель\'');
        $this->addSql('COMMENT ON COLUMN time_range.is_regular IS \'Флаг регулярный период\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE plan_appointment (id INT NOT NULL, time_range_id INT NOT NULL, starting_point_id INT NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, time_range_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_a81202f8e07937d ON plan_appointment (time_range_id)');
        $this->addSql('CREATE INDEX idx_a81202ff39c0fe7 ON plan_appointment (starting_point_id)');
        $this->addSql('COMMENT ON TABLE plan_appointment IS \'План приемов\'');
        $this->addSql('COMMENT ON COLUMN plan_appointment.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN plan_appointment.time_range_count IS \'Срок выполнения\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE patient_appointment (id INT NOT NULL, medical_record_id INT DEFAULT NULL, medical_history_id INT NOT NULL, staff_id INT DEFAULT NULL, appointment_type_id INT DEFAULT NULL, objective_status_id INT DEFAULT NULL, plan_appointment_id INT DEFAULT NULL, recommendation TEXT DEFAULT NULL, appointment_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, is_confirmed BOOLEAN DEFAULT \'false\' NOT NULL, complaints_comment TEXT DEFAULT NULL, therapy TEXT DEFAULT NULL, is_first BOOLEAN NOT NULL, is_by_plan BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_ce3bc70b5a6b53f3 ON patient_appointment (plan_appointment_id)');
        $this->addSql('CREATE INDEX idx_ce3bc70bb88e2bb6 ON patient_appointment (medical_record_id)');
        $this->addSql('CREATE INDEX idx_ce3bc70bd4d57cd ON patient_appointment (staff_id)');
        $this->addSql('CREATE INDEX idx_ce3bc70b3544ad9e ON patient_appointment (medical_history_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_ce3bc70b1426aa42 ON patient_appointment (objective_status_id)');
        $this->addSql('CREATE INDEX idx_ce3bc70b546fbebb ON patient_appointment (appointment_type_id)');
        $this->addSql('COMMENT ON TABLE patient_appointment IS \'Прием пациента\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.id IS \'Ключ приема пациента\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.medical_record_id IS \'Ключ записи в историю болезни\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.staff_id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.appointment_type_id IS \'Ключ вида приема\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.objective_status_id IS \'Ключ текста шаблона\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.recommendation IS \'Рекомендации врача\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.appointment_time IS \'Фактические дата и время приема\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.is_confirmed IS \'Подтверждение пользователем\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.complaints_comment IS \'Комментарий врача по жалобам\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.therapy IS \'Терапия\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.is_first IS \'Флаг первого приема при заведении истории болезни\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.is_by_plan IS \'Флаг: прием по плану\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE concomitant_diseases (medical_history_id INT NOT NULL, diagnosis_id INT NOT NULL, PRIMARY KEY(medical_history_id, diagnosis_id))');
        $this->addSql('CREATE INDEX idx_59f3a8e03cbe4d00 ON concomitant_diseases (diagnosis_id)');
        $this->addSql('CREATE INDEX idx_59f3a8e03544ad9e ON concomitant_diseases (medical_history_id)');
        $this->addSql('COMMENT ON COLUMN concomitant_diseases.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN concomitant_diseases.diagnosis_id IS \'Ключ диагноза\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE prescription_testing (id INT NOT NULL, prescription_id INT NOT NULL, patient_testing_id INT NOT NULL, staff_id INT NOT NULL, inclusion_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, confirmed_by_staff BOOLEAN DEFAULT \'false\' NOT NULL, planned_date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_9f5f767893db413d ON prescription_testing (prescription_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_9f5f7678b0ec09fd ON prescription_testing (patient_testing_id)');
        $this->addSql('CREATE INDEX idx_9f5f7678d4d57cd ON prescription_testing (staff_id)');
        $this->addSql('COMMENT ON TABLE prescription_testing IS \'Назначение обследования\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.id IS \'Ключ назначения обследования\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.prescription_id IS \'Ключ назначения\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.patient_testing_id IS \'Ключ сдачи анализов\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.staff_id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.inclusion_time IS \'Дата и время включения в назначение\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.confirmed_by_staff IS \'Флаг подтверждения врачом назначения на прием\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.planned_date IS \'Назначенная дата проведения обследования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE template_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN template_type.id IS \'Ключ типа шаблона\'');
        $this->addSql('COMMENT ON COLUMN template_type.name IS \'Название шаблона\'');
        $this->addSql('COMMENT ON COLUMN template_type.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE patient_testing (id INT NOT NULL, analysis_group_id INT NOT NULL, medical_history_id INT NOT NULL, medical_record_id INT DEFAULT NULL, plan_testing_id INT DEFAULT NULL, analysis_date DATE DEFAULT NULL, processed BOOLEAN DEFAULT \'false\' NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, is_first BOOLEAN NOT NULL, is_by_plan BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_b5900fed3544ad9e ON patient_testing (medical_history_id)');
        $this->addSql('CREATE INDEX idx_b5900fed6b419504 ON patient_testing (plan_testing_id)');
        $this->addSql('CREATE INDEX idx_b5900fedb88e2bb6 ON patient_testing (medical_record_id)');
        $this->addSql('CREATE INDEX idx_b5900fed174dad14 ON patient_testing (analysis_group_id)');
        $this->addSql('COMMENT ON TABLE patient_testing IS \'Сдача анализов (обследование) пациента\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.id IS \'Ключ сдачи анализов\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.analysis_group_id IS \'Ключ группы анализов\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.medical_record_id IS \'Ключ записи в историю болезни\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.plan_testing_id IS \'Ключ анализа по плану\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.analysis_date IS \'Дата проведенного тестирования\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.processed IS \'Статус принятия в работу врачом\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.is_first IS \'Флаг первого обследования по плану при заведении истории болезни\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.is_by_plan IS \'Флаг: обследование по плану\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE template (id INT NOT NULL, template_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_97601f8396f4f7aa ON template (template_type_id)');
        $this->addSql('COMMENT ON COLUMN template.id IS \'Ключ шаблона\'');
        $this->addSql('COMMENT ON COLUMN template.template_type_id IS \'Ключ типа шаблона\'');
        $this->addSql('COMMENT ON COLUMN template.name IS \'Название шаблона\'');
        $this->addSql('COMMENT ON COLUMN template.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE patient_discharge_epicrisis (id INT NOT NULL, medical_history_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_5e69ad6e3544ad9e ON patient_discharge_epicrisis (medical_history_id)');
        $this->addSql('COMMENT ON COLUMN patient_discharge_epicrisis.id IS \'Ключ выписного эпикриза\'');
        $this->addSql('COMMENT ON COLUMN patient_discharge_epicrisis.medical_history_id IS \'Ключ истории болезни\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE text_by_template (id INT NOT NULL, template_id INT DEFAULT NULL, template_type_id INT NOT NULL, text TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_268cfe5996f4f7aa ON text_by_template (template_type_id)');
        $this->addSql('CREATE INDEX idx_268cfe595da0fb8 ON text_by_template (template_id)');
        $this->addSql('COMMENT ON COLUMN text_by_template.id IS \'Ключ текста шаблона\'');
        $this->addSql('COMMENT ON COLUMN text_by_template.template_id IS \'Ключ шаблона\'');
        $this->addSql('COMMENT ON COLUMN text_by_template.template_type_id IS \'Ключ типа шаблона\'');
        $this->addSql('COMMENT ON COLUMN text_by_template.text IS \'Оригинальный текст по шаблону\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE template_many_to_many_template_parameter_text (id INT NOT NULL, template_id INT NOT NULL, template_parameter_text_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_f63eade52ed430a ON template_many_to_many_template_parameter_text (template_parameter_text_id)');
        $this->addSql('CREATE INDEX idx_f63eade5da0fb8 ON template_many_to_many_template_parameter_text (template_id)');
        $this->addSql('COMMENT ON COLUMN template_many_to_many_template_parameter_text.template_id IS \'Ключ шаблона\'');
        $this->addSql('COMMENT ON COLUMN template_many_to_many_template_parameter_text.template_parameter_text_id IS \'Ключ параметра шаблона\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE discharge_epicrisis_file (id INT NOT NULL, patient_discharge_epicrisis_id INT NOT NULL, file_name VARCHAR(255) NOT NULL, uploaded_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, extension VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_26d6c758bcf381c ON discharge_epicrisis_file (patient_discharge_epicrisis_id)');
        $this->addSql('COMMENT ON COLUMN discharge_epicrisis_file.id IS \'Ключ файла выписного эпикриза\'');
        $this->addSql('COMMENT ON COLUMN discharge_epicrisis_file.patient_discharge_epicrisis_id IS \'Ключ выписного эпикриза\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE background_diseases (medical_history_id INT NOT NULL, diagnosis_id INT NOT NULL, PRIMARY KEY(medical_history_id, diagnosis_id))');
        $this->addSql('CREATE INDEX idx_2c1d0b803544ad9e ON background_diseases (medical_history_id)');
        $this->addSql('CREATE INDEX idx_2c1d0b803cbe4d00 ON background_diseases (diagnosis_id)');
        $this->addSql('COMMENT ON COLUMN background_diseases.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN background_diseases.diagnosis_id IS \'Ключ диагноза\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE log_action (id INT NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE template_parameter_text (id INT NOT NULL, template_parameter_id INT NOT NULL, text TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_31945ad836828783 ON template_parameter_text (template_parameter_id)');
        $this->addSql('COMMENT ON COLUMN template_parameter_text.id IS \'Ключ параметра шаблона\'');
        $this->addSql('COMMENT ON COLUMN template_parameter_text.template_parameter_id IS \'Ключ параметра типа шаблона\'');
        $this->addSql('COMMENT ON COLUMN template_parameter_text.text IS \'Текст параметра шаблона\'');
        $this->addSql('COMMENT ON COLUMN template_parameter_text.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE diagnosis (id INT NOT NULL, name VARCHAR(256) NOT NULL, code VARCHAR(50) NOT NULL, parent_code VARCHAR(50) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN diagnosis.id IS \'Ключ диагноза\'');
        $this->addSql('COMMENT ON COLUMN diagnosis.name IS \'Название диагноза\'');
        $this->addSql('COMMENT ON COLUMN diagnosis.code IS \'Код диагноза\'');
        $this->addSql('COMMENT ON COLUMN diagnosis.parent_code IS \'Код группы диагнозов (диагноза верхнего уровня)\'');
        $this->addSql('COMMENT ON COLUMN diagnosis.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE template_parameter (id INT NOT NULL, template_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_e3c4e7a696f4f7aa ON template_parameter (template_type_id)');
        $this->addSql('COMMENT ON COLUMN template_parameter.id IS \'Ключ параметра типа шаблона\'');
        $this->addSql('COMMENT ON COLUMN template_parameter.template_type_id IS \'Ключ типа шаблона\'');
        $this->addSql('COMMENT ON COLUMN template_parameter.name IS \'Название параметра типа шаблона\'');
        $this->addSql('COMMENT ON COLUMN template_parameter.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE prescription_appointment (id INT NOT NULL, prescription_id INT NOT NULL, patient_appointment_id INT NOT NULL, staff_id INT NOT NULL, inclusion_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, confirmed_by_staff BOOLEAN DEFAULT \'false\' NOT NULL, planned_date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_885c871593db413d ON prescription_appointment (prescription_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_885c87155fa482b2 ON prescription_appointment (patient_appointment_id)');
        $this->addSql('CREATE INDEX idx_885c8715d4d57cd ON prescription_appointment (staff_id)');
        $this->addSql('COMMENT ON TABLE prescription_appointment IS \'Назначение на прием\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.id IS \'Ключ назначения на прием\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.prescription_id IS \'Ключ назначения\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.patient_appointment_id IS \'Ключ приема пациента\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.staff_id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.inclusion_time IS \'Дата и время включения в назначение\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.confirmed_by_staff IS \'Флаг подтверждения врачом назначения на прием\'');
        $this->addSql('COMMENT ON COLUMN prescription_appointment.planned_date_time IS \'Назначенные дата и время проведения приема\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE starting_point (id INT NOT NULL, name VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE starting_point IS \'Точка отсчета\'');
        $this->addSql('COMMENT ON COLUMN starting_point.name IS \'Имя свойства для точки отсчета добавления обследований по плану\'');
        $this->addSql('COMMENT ON COLUMN starting_point.title IS \'Заголовок точки отсчета добавления обследований по плану\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE plan_testing (id INT NOT NULL, analysis_group_id INT NOT NULL, time_range_id INT NOT NULL, starting_point_id INT NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, time_range_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_564c120e8e07937d ON plan_testing (time_range_id)');
        $this->addSql('CREATE INDEX idx_564c120e174dad14 ON plan_testing (analysis_group_id)');
        $this->addSql('CREATE INDEX idx_564c120ef39c0fe7 ON plan_testing (starting_point_id)');
        $this->addSql('COMMENT ON TABLE plan_testing IS \'План обследований\'');
        $this->addSql('COMMENT ON COLUMN plan_testing.id IS \'Ключ анализа по плану\'');
        $this->addSql('COMMENT ON COLUMN plan_testing.analysis_group_id IS \'Ключ группы анализов\'');
        $this->addSql('COMMENT ON COLUMN plan_testing.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN plan_testing.time_range_count IS \'Срок выполнения\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE prescription (id INT NOT NULL, medical_history_id INT NOT NULL, staff_id INT NOT NULL, medical_record_id INT DEFAULT NULL, is_completed BOOLEAN DEFAULT \'false\' NOT NULL, is_patient_confirmed BOOLEAN DEFAULT \'false\' NOT NULL, description TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, created_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, completed_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_1fbfb8d93544ad9e ON prescription (medical_history_id)');
        $this->addSql('CREATE INDEX idx_1fbfb8d9b88e2bb6 ON prescription (medical_record_id)');
        $this->addSql('CREATE INDEX idx_1fbfb8d9d4d57cd ON prescription (staff_id)');
        $this->addSql('COMMENT ON TABLE prescription IS \'Назначение\'');
        $this->addSql('COMMENT ON COLUMN prescription.id IS \'Ключ назначения\'');
        $this->addSql('COMMENT ON COLUMN prescription.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN prescription.staff_id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN prescription.medical_record_id IS \'Ключ записи в историю болезни\'');
        $this->addSql('COMMENT ON COLUMN prescription.is_completed IS \'Назначено\'');
        $this->addSql('COMMENT ON COLUMN prescription.is_patient_confirmed IS \'Подтверждение назначения пациентом\'');
        $this->addSql('COMMENT ON COLUMN prescription.description IS \'Описание назначения\'');
        $this->addSql('COMMENT ON COLUMN prescription.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN prescription.created_time IS \'Дата и время создания назначения\'');
        $this->addSql('COMMENT ON COLUMN prescription.completed_time IS \'Дата и время факта назначения\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE prescription_medicine (id INT NOT NULL, prescription_id INT NOT NULL, medicine_id INT NOT NULL, reception_method_id INT NOT NULL, staff_id INT NOT NULL, instruction TEXT NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, inclusion_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_effcda9ad4d57cd ON prescription_medicine (staff_id)');
        $this->addSql('CREATE INDEX idx_effcda9a93db413d ON prescription_medicine (prescription_id)');
        $this->addSql('CREATE INDEX idx_effcda9a2527130b ON prescription_medicine (reception_method_id)');
        $this->addSql('CREATE INDEX idx_effcda9a2f7d140a ON prescription_medicine (medicine_id)');
        $this->addSql('COMMENT ON TABLE prescription_medicine IS \'Назначение лекарства\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.id IS \'Ключ назначения препарата\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.prescription_id IS \'Ключ назначения\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.medicine_id IS \'Ключ препарата\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.reception_method_id IS \'Ключ способа приема препарата\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.staff_id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.instruction IS \'Инструкция по применению\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.inclusion_time IS \'Дата и время включения лекарства в назначение\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE reception_method (id INT NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE reception_method IS \'Способ приема препарата\'');
        $this->addSql('COMMENT ON COLUMN reception_method.id IS \'Ключ способа приема препарата\'');
        $this->addSql('COMMENT ON COLUMN reception_method.name IS \'Название способа приема\'');
        $this->addSql('COMMENT ON COLUMN reception_method.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE staff (id INT NOT NULL, hospital_id INT DEFAULT NULL, position_id INT NOT NULL, auth_user_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_426ef39263dbb69 ON staff (hospital_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_426ef392e94af366 ON staff (auth_user_id)');
        $this->addSql('CREATE INDEX idx_426ef392dd842e46 ON staff (position_id)');
        $this->addSql('COMMENT ON TABLE staff IS \'Персонал\'');
        $this->addSql('COMMENT ON COLUMN staff.id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN staff.hospital_id IS \'Ключ больницы\'');
        $this->addSql('COMMENT ON COLUMN staff.position_id IS \'Ключ должности\'');
        $this->addSql('COMMENT ON COLUMN staff.auth_user_id IS \'Ключ пользователя\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE medical_history (id INT NOT NULL, patient_id INT NOT NULL, main_disease_id INT DEFAULT NULL, life_history_id INT DEFAULT NULL, date_begin DATE NOT NULL, date_end DATE DEFAULT NULL, disease_history TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, clinical_diagnosis TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_61b89085e0cd2722 ON medical_history (main_disease_id)');
        $this->addSql('CREATE INDEX idx_61b890856b899279 ON medical_history (patient_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_61b89085fabd44d4 ON medical_history (life_history_id)');
        $this->addSql('COMMENT ON TABLE medical_history IS \'История болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_history.id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_history.patient_id IS \'Ключ пациента\'');
        $this->addSql('COMMENT ON COLUMN medical_history.main_disease_id IS \'Ключ диагноза\'');
        $this->addSql('COMMENT ON COLUMN medical_history.life_history_id IS \'Ключ текста шаблона\'');
        $this->addSql('COMMENT ON COLUMN medical_history.date_begin IS \'Дата открытия\'');
        $this->addSql('COMMENT ON COLUMN medical_history.date_end IS \'Дата закрытия\'');
        $this->addSql('COMMENT ON COLUMN medical_history.disease_history IS \'Анамнез болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_history.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN medical_history.clinical_diagnosis IS \'Клинический диагноз\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE log (id INT NOT NULL, log_action_id INT DEFAULT NULL, description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_string VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_8f3f68c58b306cbb ON log (log_action_id)');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE auth_user (id INT NOT NULL, email VARCHAR(180) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, phone VARCHAR(10) NOT NULL, first_name VARCHAR(30) NOT NULL, last_name VARCHAR(100) NOT NULL, patronymic_name VARCHAR(50) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_a3b536fd444f97dd ON auth_user (phone)');
        $this->addSql('COMMENT ON COLUMN auth_user.id IS \'Ключ пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.email IS \'Email пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.roles IS \'Роли пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.password IS \'Пароль пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.phone IS \'Телефон пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.first_name IS \'Имя пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.last_name IS \'Фамилия пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.patronymic_name IS \'Отчество пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE district (id INT NOT NULL, region_id INT NOT NULL, oktmo_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_31c15487ee0c723d ON district (oktmo_id)');
        $this->addSql('CREATE INDEX idx_31c1548798260155 ON district (region_id)');
        $this->addSql('COMMENT ON TABLE district IS \'Районы\'');
        $this->addSql('COMMENT ON COLUMN district.id IS \'Ключ района\'');
        $this->addSql('COMMENT ON COLUMN district.region_id IS \'Ключ региона\'');
        $this->addSql('COMMENT ON COLUMN district.oktmo_id IS \'Ключ ОКТМО\'');
        $this->addSql('COMMENT ON COLUMN district.name IS \'Название района\'');
        $this->addSql('COMMENT ON COLUMN district.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE analysis (id INT NOT NULL, analysis_group_id INT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_33c730174dad14 ON analysis (analysis_group_id)');
        $this->addSql('COMMENT ON COLUMN analysis.id IS \'Ключ анализа\'');
        $this->addSql('COMMENT ON COLUMN analysis.analysis_group_id IS \'Ключ группы анализов\'');
        $this->addSql('COMMENT ON COLUMN analysis.name IS \'Название анализа\'');
        $this->addSql('COMMENT ON COLUMN analysis.description IS \'Описание анализа\'');
        $this->addSql('COMMENT ON COLUMN analysis.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE patient_testing_file (id INT NOT NULL, patient_testing_id INT NOT NULL, file_name VARCHAR(255) NOT NULL, uploaded_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, extension VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_3d3cb73eb0ec09fd ON patient_testing_file (patient_testing_id)');
        $this->addSql('COMMENT ON COLUMN patient_testing_file.id IS \'Ключ файла обследования\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_file.patient_testing_id IS \'Ключ сдачи анализов\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE appointment_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE appointment_type IS \'Вид приема\'');
        $this->addSql('COMMENT ON COLUMN appointment_type.id IS \'Ключ вида приема\'');
        $this->addSql('COMMENT ON COLUMN appointment_type.name IS \'Наименование вида приема\'');
        $this->addSql('COMMENT ON COLUMN appointment_type.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE patient_testing_result (id INT NOT NULL, patient_testing_id INT NOT NULL, analysis_rate_id INT DEFAULT NULL, analysis_id INT NOT NULL, result DOUBLE PRECISION DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_82d2ca2ab0ec09fd ON patient_testing_result (patient_testing_id)');
        $this->addSql('CREATE INDEX idx_82d2ca2a7941003f ON patient_testing_result (analysis_id)');
        $this->addSql('CREATE INDEX idx_82d2ca2ac648f999 ON patient_testing_result (analysis_rate_id)');
        $this->addSql('COMMENT ON TABLE patient_testing_result IS \'Результаты анализа\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_result.id IS \'Ключ резултатов анализа\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_result.patient_testing_id IS \'Ключ сдачи анализов\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_result.analysis_rate_id IS \'Ключ нормальных значений\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_result.analysis_id IS \'Ключ анализа\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_result.result IS \'Результат анализа\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_result.enabled IS \'Ограничение использования\'');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE complications (medical_history_id INT NOT NULL, diagnosis_id INT NOT NULL, PRIMARY KEY(medical_history_id, diagnosis_id))');
        $this->addSql('CREATE INDEX idx_bc020a5f3cbe4d00 ON complications (diagnosis_id)');
        $this->addSql('CREATE INDEX idx_bc020a5f3544ad9e ON complications (medical_history_id)');
        $this->addSql('COMMENT ON COLUMN complications.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN complications.diagnosis_id IS \'Ключ диагноза\'');
        $this->addSql('CREATE SEQUENCE prescription_medicine_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE prescription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE log_action_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE patient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE reception_method_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE diagnosis_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE patient_testing_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE medicine_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE notification_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE oksm_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE patient_testing_file_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE patient_discharge_epicrisis_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE patient_appointment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE oktmo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE district_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE template_many_to_many_template_parameter_text_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE city_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE patient_testing_result_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE plan_testing_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE auth_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE discharge_epicrisis_file_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE medical_history_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE hospital_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE prescription_testing_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE country_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE medical_record_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE lpu_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE template_parameter_text_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE text_by_template_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE complaint_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE region_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE template_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE appointment_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE position_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE prescription_appointment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE plan_appointment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('CREATE SEQUENCE staff_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
     $this->addSql('ALTER TABLE prescription_medicine ADD CONSTRAINT FK_EFFCDA9A93DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE prescription_medicine ADD CONSTRAINT FK_EFFCDA9A2F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE prescription_medicine ADD CONSTRAINT FK_EFFCDA9A2527130B FOREIGN KEY (reception_method_id) REFERENCES reception_method (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE prescription_medicine ADD CONSTRAINT FK_EFFCDA9AD4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D93544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D9D4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D9B88E2BB6 FOREIGN KEY (medical_record_id) REFERENCES medical_record (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C58B306CBB FOREIGN KEY (log_action_id) REFERENCES log_action (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBE94AF366 FOREIGN KEY (auth_user_id) REFERENCES auth_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBB08FA272 FOREIGN KEY (district_id) REFERENCES district (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE analysis ADD CONSTRAINT FK_33C730174DAD14 FOREIGN KEY (analysis_group_id) REFERENCES analysis_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAD0520624 FOREIGN KEY (notification_type_id) REFERENCES notification_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAB88E2BB6 FOREIGN KEY (medical_record_id) REFERENCES medical_record (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAD4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_testing ADD CONSTRAINT FK_B5900FED174DAD14 FOREIGN KEY (analysis_group_id) REFERENCES analysis_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_testing ADD CONSTRAINT FK_B5900FED3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_testing ADD CONSTRAINT FK_B5900FEDB88E2BB6 FOREIGN KEY (medical_record_id) REFERENCES medical_record (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_testing ADD CONSTRAINT FK_B5900FED6B419504 FOREIGN KEY (plan_testing_id) REFERENCES plan_testing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_testing_file ADD CONSTRAINT FK_3D3CB73EB0EC09FD FOREIGN KEY (patient_testing_id) REFERENCES patient_testing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_discharge_epicrisis ADD CONSTRAINT FK_5E69AD6E3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_appointment ADD CONSTRAINT FK_CE3BC70BB88E2BB6 FOREIGN KEY (medical_record_id) REFERENCES medical_record (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_appointment ADD CONSTRAINT FK_CE3BC70B3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_appointment ADD CONSTRAINT FK_CE3BC70BD4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_appointment ADD CONSTRAINT FK_CE3BC70B546FBEBB FOREIGN KEY (appointment_type_id) REFERENCES appointment_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_appointment ADD CONSTRAINT FK_CE3BC70B1426AA42 FOREIGN KEY (objective_status_id) REFERENCES text_by_template (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_appointment ADD CONSTRAINT FK_CE3BC70B5A6B53F3 FOREIGN KEY (plan_appointment_id) REFERENCES plan_appointment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_appointment_complaint ADD CONSTRAINT FK_7278AD235FA482B2 FOREIGN KEY (patient_appointment_id) REFERENCES patient_appointment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_appointment_complaint ADD CONSTRAINT FK_7278AD23EDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE district ADD CONSTRAINT FK_31C1548798260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE district ADD CONSTRAINT FK_31C15487EE0C723D FOREIGN KEY (oktmo_id) REFERENCES oktmo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE template_many_to_many_template_parameter_text ADD CONSTRAINT FK_F63EADE5DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE template_many_to_many_template_parameter_text ADD CONSTRAINT FK_F63EADE52ED430A FOREIGN KEY (template_parameter_text_id) REFERENCES template_parameter_text (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B023498260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234B08FA272 FOREIGN KEY (district_id) REFERENCES district (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234EE0C723D FOREIGN KEY (oktmo_id) REFERENCES oktmo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_testing_result ADD CONSTRAINT FK_82D2CA2AB0EC09FD FOREIGN KEY (patient_testing_id) REFERENCES patient_testing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_testing_result ADD CONSTRAINT FK_82D2CA2AC648F999 FOREIGN KEY (analysis_rate_id) REFERENCES analysis_rate (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE patient_testing_result ADD CONSTRAINT FK_82D2CA2A7941003F FOREIGN KEY (analysis_id) REFERENCES analysis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE plan_testing ADD CONSTRAINT FK_564C120E174DAD14 FOREIGN KEY (analysis_group_id) REFERENCES analysis_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE plan_testing ADD CONSTRAINT FK_564C120E8E07937D FOREIGN KEY (time_range_id) REFERENCES time_range (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE plan_testing ADD CONSTRAINT FK_564C120EF39C0FE7 FOREIGN KEY (starting_point_id) REFERENCES starting_point (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE discharge_epicrisis_file ADD CONSTRAINT FK_26D6C758BCF381C FOREIGN KEY (patient_discharge_epicrisis_id) REFERENCES patient_discharge_epicrisis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE medical_history ADD CONSTRAINT FK_61B890856B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE medical_history ADD CONSTRAINT FK_61B89085E0CD2722 FOREIGN KEY (main_disease_id) REFERENCES diagnosis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE medical_history ADD CONSTRAINT FK_61B89085FABD44D4 FOREIGN KEY (life_history_id) REFERENCES text_by_template (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE background_diseases ADD CONSTRAINT FK_2C1D0B803544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE background_diseases ADD CONSTRAINT FK_2C1D0B803CBE4D00 FOREIGN KEY (diagnosis_id) REFERENCES diagnosis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE complications ADD CONSTRAINT FK_BC020A5F3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE complications ADD CONSTRAINT FK_BC020A5F3CBE4D00 FOREIGN KEY (diagnosis_id) REFERENCES diagnosis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE concomitant_diseases ADD CONSTRAINT FK_59F3A8E03544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE concomitant_diseases ADD CONSTRAINT FK_59F3A8E03CBE4D00 FOREIGN KEY (diagnosis_id) REFERENCES diagnosis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B98260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85BF2C7C2C1 FOREIGN KEY (lpu_id) REFERENCES lpu (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE prescription_testing ADD CONSTRAINT FK_9F5F767893DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE prescription_testing ADD CONSTRAINT FK_9F5F7678B0EC09FD FOREIGN KEY (patient_testing_id) REFERENCES patient_testing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE prescription_testing ADD CONSTRAINT FK_9F5F7678D4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE medical_record ADD CONSTRAINT FK_F06A283E3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE template_parameter_text ADD CONSTRAINT FK_31945AD836828783 FOREIGN KEY (template_parameter_id) REFERENCES template_parameter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE text_by_template ADD CONSTRAINT FK_268CFE595DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE text_by_template ADD CONSTRAINT FK_268CFE5996F4F7AA FOREIGN KEY (template_type_id) REFERENCES template_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_F62F176F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE template ADD CONSTRAINT FK_97601F8396F4F7AA FOREIGN KEY (template_type_id) REFERENCES template_type (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE analysis_rate ADD CONSTRAINT FK_EE5F7AD27941003F FOREIGN KEY (analysis_id) REFERENCES analysis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE analysis_rate ADD CONSTRAINT FK_EE5F7AD25DA37D00 FOREIGN KEY (measure_id) REFERENCES measure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE analysis_rate ADD CONSTRAINT FK_EE5F7AD2708A0E0 FOREIGN KEY (gender_id) REFERENCES gender (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE template_parameter ADD CONSTRAINT FK_E3C4E7A696F4F7AA FOREIGN KEY (template_type_id) REFERENCES template_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE time_range ADD CONSTRAINT FK_D6F5BB356F2301F2 FOREIGN KEY (date_interval_id) REFERENCES date_interval (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE prescription_appointment ADD CONSTRAINT FK_885C871593DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE prescription_appointment ADD CONSTRAINT FK_885C87155FA482B2 FOREIGN KEY (patient_appointment_id) REFERENCES patient_appointment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE prescription_appointment ADD CONSTRAINT FK_885C8715D4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE plan_appointment ADD CONSTRAINT FK_A81202F8E07937D FOREIGN KEY (time_range_id) REFERENCES time_range (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE plan_appointment ADD CONSTRAINT FK_A81202FF39C0FE7 FOREIGN KEY (starting_point_id) REFERENCES starting_point (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF39263DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF392DD842E46 FOREIGN KEY (position_id) REFERENCES position (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
     $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF392E94AF366 FOREIGN KEY (auth_user_id) REFERENCES auth_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE role');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE oksm');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE analysis_group');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE medical_record');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE notification_type');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE notification');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE analysis_rate');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE measure');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE gender');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE patient_appointment_complaint');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE complaint');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE medicine');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE region');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE "position"');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE hospital');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE city');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE lpu');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE country');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE oktmo');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE date_interval');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE patient');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE time_range');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE plan_appointment');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE patient_appointment');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE concomitant_diseases');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE prescription_testing');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE template_type');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE patient_testing');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE template');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE patient_discharge_epicrisis');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE text_by_template');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE template_many_to_many_template_parameter_text');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE discharge_epicrisis_file');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE background_diseases');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE log_action');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE template_parameter_text');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE diagnosis');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE template_parameter');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE prescription_appointment');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE starting_point');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE plan_testing');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE prescription');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE prescription_medicine');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE reception_method');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE staff');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE medical_history');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE log');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE auth_user');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE district');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE analysis');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE patient_testing_file');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE appointment_type');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE patient_testing_result');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE complications');
    }
}
