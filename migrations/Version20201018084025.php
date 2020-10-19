<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201018084025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE plan_appointment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE plan_testing_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reception_method_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE medical_record_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE patient_file_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE complaint_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prescription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE patient_testing_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE appointment_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE lpu_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE patient_testing_result_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notification_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE country_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE patient_appointment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE staff_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE position_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oksm_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prescription_medicine_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE auth_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE hospital_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE patient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oktmo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE city_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE diagnosis_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prescription_testing_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE district_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE medicine_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE region_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE medical_history_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE notification (id INT NOT NULL, notification_type_id INT NOT NULL, medical_record_id INT NOT NULL, staff_id INT NOT NULL, medical_history_id INT NOT NULL, notification_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, text TEXT NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_BF5476CAD0520624 ON notification (notification_type_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAB88E2BB6 ON notification (medical_record_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAD4D57CD ON notification (staff_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA3544AD9E ON notification (medical_history_id)');
        $this->addSql('COMMENT ON TABLE notification IS \'Уведомление\'');
        $this->addSql('COMMENT ON COLUMN notification.id IS \'Ключ уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification.notification_type_id IS \'Ключ типа уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification.medical_record_id IS \'Ключ записи в историю болезни\'');
        $this->addSql('COMMENT ON COLUMN notification.staff_id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN notification.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN notification.notification_time IS \'Дата и время создания уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification.text IS \'Текст уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE analysis (id INT NOT NULL, analysis_group_id INT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_33C730174DAD14 ON analysis (analysis_group_id)');
        $this->addSql('COMMENT ON COLUMN analysis.id IS \'Ключ анализа\'');
        $this->addSql('COMMENT ON COLUMN analysis.analysis_group_id IS \'Ключ группы анализов\'');
        $this->addSql('COMMENT ON COLUMN analysis.name IS \'Название анализа\'');
        $this->addSql('COMMENT ON COLUMN analysis.description IS \'Описание анализа\'');
        $this->addSql('COMMENT ON COLUMN analysis.enabled IS \'Ограничение использования\'');
        $this->addSql('CREATE TABLE role (id INT NOT NULL, name VARCHAR(50) NOT NULL, tech_name VARCHAR(50) DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN role.id IS \'Ключ роли\'');
        $this->addSql('COMMENT ON COLUMN role.name IS \'Название роли\'');
        $this->addSql('COMMENT ON COLUMN role.tech_name IS \'Техническое название\'');
        $this->addSql('COMMENT ON COLUMN role.description IS \'Описание роли\'');
        $this->addSql(
            'CREATE TABLE plan_appointment (id INT NOT NULL, time_range_id INT NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, time_range_count INT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_A81202F8E07937D ON plan_appointment (time_range_id)');
        $this->addSql('COMMENT ON TABLE plan_appointment IS \'План приемов\'');
        $this->addSql('COMMENT ON COLUMN plan_appointment.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN plan_appointment.time_range_count IS \'Срок выполнения\'');
        $this->addSql(
            'CREATE TABLE plan_testing (id INT NOT NULL, analysis_group_id INT NOT NULL, time_range_id INT NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, time_range_count INT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_564C120E174DAD14 ON plan_testing (analysis_group_id)');
        $this->addSql('CREATE INDEX IDX_564C120E8E07937D ON plan_testing (time_range_id)');
        $this->addSql('COMMENT ON TABLE plan_testing IS \'План обследований\'');
        $this->addSql('COMMENT ON COLUMN plan_testing.id IS \'Ключ анализа по плану\'');
        $this->addSql('COMMENT ON COLUMN plan_testing.analysis_group_id IS \'Ключ группы анализов\'');
        $this->addSql('COMMENT ON COLUMN plan_testing.enabled IS \'Ограничение использования\'');
        $this->addSql('CREATE TABLE reception_method (id INT NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE reception_method IS \'Способ приема препарата\'');
        $this->addSql('COMMENT ON COLUMN reception_method.id IS \'Ключ способа приема препарата\'');
        $this->addSql('COMMENT ON COLUMN reception_method.name IS \'Название способа приема\'');
        $this->addSql('COMMENT ON COLUMN reception_method.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE analysis_group (id INT NOT NULL, name VARCHAR(50) NOT NULL, full_name VARCHAR(255) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON TABLE analysis_group IS \'Группа анализов\'');
        $this->addSql('COMMENT ON COLUMN analysis_group.id IS \'Ключ группы анализов\'');
        $this->addSql('COMMENT ON COLUMN analysis_group.name IS \'Название группы анализов (аббревиатура)\'');
        $this->addSql('COMMENT ON COLUMN analysis_group.full_name IS \'Полное название группы анализов (расшифровка аббревиатуры)\'');
        $this->addSql('COMMENT ON COLUMN analysis_group.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE medical_record (id INT NOT NULL, medical_history_id INT NOT NULL, record_date DATE NOT NULL, comment TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_F06A283E3544AD9E ON medical_record (medical_history_id)');
        $this->addSql('COMMENT ON TABLE medical_record IS \'Запись в историю болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_record.id IS \'Ключ записи в историю болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_record.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_record.record_date IS \'Дата создания записи\'');
        $this->addSql('COMMENT ON COLUMN medical_record.comment IS \'Комментарий\'');
        $this->addSql('COMMENT ON COLUMN medical_record.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE patient_file (id INT NOT NULL, patient_id INT NOT NULL, patient_testing_id INT DEFAULT NULL, file_name VARCHAR(255) NOT NULL, uploaded_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, extension VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_50E7BD86B899279 ON patient_file (patient_id)');
        $this->addSql('CREATE INDEX IDX_50E7BD8B0EC09FD ON patient_file (patient_testing_id)');
        $this->addSql('COMMENT ON COLUMN patient_file.patient_id IS \'Ключ пациента\'');
        $this->addSql('COMMENT ON COLUMN patient_file.patient_testing_id IS \'Ключ сдачи анализов\'');
        $this->addSql(
            'CREATE TABLE complaint (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON TABLE complaint IS \'Жалоба\'');
        $this->addSql('COMMENT ON COLUMN complaint.name IS \'Название жалобы\'');
        $this->addSql('COMMENT ON COLUMN complaint.description IS \'Описание жалобы\'');
        $this->addSql('COMMENT ON COLUMN complaint.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE prescription (id INT NOT NULL, medical_history_id INT NOT NULL, staff_id INT NOT NULL, medical_record_id INT DEFAULT NULL, is_completed BOOLEAN DEFAULT \'false\' NOT NULL, is_patient_confirmed BOOLEAN DEFAULT \'false\' NOT NULL, description TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, created_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, completed_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_1FBFB8D93544AD9E ON prescription (medical_history_id)');
        $this->addSql('CREATE INDEX IDX_1FBFB8D9D4D57CD ON prescription (staff_id)');
        $this->addSql('CREATE INDEX IDX_1FBFB8D9B88E2BB6 ON prescription (medical_record_id)');
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
        $this->addSql(
            'CREATE TABLE patient_testing (id INT NOT NULL, analysis_group_id INT NOT NULL, medical_history_id INT NOT NULL, medical_record_id INT DEFAULT NULL, analysis_date DATE DEFAULT NULL, processed BOOLEAN DEFAULT \'false\' NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, planned_date DATE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_B5900FED174DAD14 ON patient_testing (analysis_group_id)');
        $this->addSql('CREATE INDEX IDX_B5900FED3544AD9E ON patient_testing (medical_history_id)');
        $this->addSql('CREATE INDEX IDX_B5900FEDB88E2BB6 ON patient_testing (medical_record_id)');
        $this->addSql('COMMENT ON TABLE patient_testing IS \'Сдача анализов (обследование) пациента\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.id IS \'Ключ сдачи анализов\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.analysis_group_id IS \'Ключ группы анализов\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.medical_record_id IS \'Ключ записи в историю болезни\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.analysis_date IS \'Дата проведенного тестирования\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.processed IS \'Статус принятия в работу врачом\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN patient_testing.planned_date IS \'Планируемая дата проведения тестирования\'');
        $this->addSql('CREATE TABLE appointment_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE appointment_type IS \'Вид приема\'');
        $this->addSql('COMMENT ON COLUMN appointment_type.id IS \'Ключ вида приема\'');
        $this->addSql('COMMENT ON COLUMN appointment_type.name IS \'Наименование вида приема\'');
        $this->addSql('COMMENT ON COLUMN appointment_type.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE lpu (id INT NOT NULL, oktmo_region_id INT DEFAULT NULL, region_name VARCHAR(100) NOT NULL, years VARCHAR(255) NOT NULL, code VARCHAR(6) NOT NULL, full_name VARCHAR(255) DEFAULT NULL, caption VARCHAR(255) NOT NULL, okopf VARCHAR(5) NOT NULL, post_code VARCHAR(6) DEFAULT NULL, address VARCHAR(255) NOT NULL, director_last_name VARCHAR(50) NOT NULL, director_first_name VARCHAR(50) NOT NULL, director_patronymic_name VARCHAR(50) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, fax VARCHAR(50) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, license VARCHAR(50) DEFAULT NULL, license_date DATE DEFAULT NULL, license_date_end DATE DEFAULT NULL, medical_care_types VARCHAR(255) DEFAULT NULL, include_date DATE NOT NULL, PRIMARY KEY(id))'
        );
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
        $this->addSql(
            'CREATE TABLE patient_testing_result (id INT NOT NULL, patient_testing_id INT NOT NULL, analysis_rate_id INT DEFAULT NULL, analysis_id INT NOT NULL, result DOUBLE PRECISION DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_82D2CA2AB0EC09FD ON patient_testing_result (patient_testing_id)');
        $this->addSql('CREATE INDEX IDX_82D2CA2AC648F999 ON patient_testing_result (analysis_rate_id)');
        $this->addSql('CREATE INDEX IDX_82D2CA2A7941003F ON patient_testing_result (analysis_id)');
        $this->addSql('COMMENT ON TABLE patient_testing_result IS \'Результаты анализа\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_result.id IS \'Ключ резултатов анализа\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_result.patient_testing_id IS \'Ключ сдачи анализов\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_result.analysis_rate_id IS \'Ключ нормальных значений\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_result.analysis_id IS \'Ключ анализа\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_result.result IS \'Результат анализа\'');
        $this->addSql('COMMENT ON COLUMN patient_testing_result.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE notification_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, template TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON TABLE notification_type IS \'Тип уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification_type.id IS \'Ключ типа уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification_type.name IS \'Наименование типа уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification_type.template IS \'Шаблон типа уведомления\'');
        $this->addSql('COMMENT ON COLUMN notification_type.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE analysis_rate (id INT NOT NULL, analysis_id INT NOT NULL, measure_id INT NOT NULL, gender_id INT DEFAULT NULL, rate_min DOUBLE PRECISION NOT NULL, rate_max DOUBLE PRECISION NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_EE5F7AD27941003F ON analysis_rate (analysis_id)');
        $this->addSql('CREATE INDEX IDX_EE5F7AD25DA37D00 ON analysis_rate (measure_id)');
        $this->addSql('CREATE INDEX IDX_EE5F7AD2708A0E0 ON analysis_rate (gender_id)');
        $this->addSql('CREATE UNIQUE INDEX analysis_rate_unique ON analysis_rate (analysis_id, measure_id, gender_id)');
        $this->addSql('COMMENT ON TABLE analysis_rate IS \'Референтные значения анализа\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.id IS \'Ключ нормальных значений\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.analysis_id IS \'Ключ анализа\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.measure_id IS \'Ключ единицы измерения\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.gender_id IS \'Ключ пола\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.rate_min IS \'Минимальное нормальное значение\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.rate_max IS \'Максимальное нормальное значение\'');
        $this->addSql('COMMENT ON COLUMN analysis_rate.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE country (id INT NOT NULL, name VARCHAR(30) NOT NULL, shortcode VARCHAR(4) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON TABLE country IS \'Страна\'');
        $this->addSql('COMMENT ON COLUMN country.id IS \'Ключ страны\'');
        $this->addSql('COMMENT ON COLUMN country.name IS \'Название страны\'');
        $this->addSql('COMMENT ON COLUMN country.shortcode IS \'Код страны в формате ISO\'');
        $this->addSql('COMMENT ON COLUMN country.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE patient_appointment (id INT NOT NULL, medical_record_id INT NOT NULL, medical_history_id INT NOT NULL, staff_id INT NOT NULL, appointment_type_id INT NOT NULL, recommendation TEXT DEFAULT NULL, appointment_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, is_confirmed BOOLEAN DEFAULT \'false\' NOT NULL, complaints_comment TEXT DEFAULT NULL, objective_status TEXT DEFAULT NULL, therapy TEXT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_CE3BC70BB88E2BB6 ON patient_appointment (medical_record_id)');
        $this->addSql('CREATE INDEX IDX_CE3BC70B3544AD9E ON patient_appointment (medical_history_id)');
        $this->addSql('CREATE INDEX IDX_CE3BC70BD4D57CD ON patient_appointment (staff_id)');
        $this->addSql('CREATE INDEX IDX_CE3BC70B546FBEBB ON patient_appointment (appointment_type_id)');
        $this->addSql('COMMENT ON TABLE patient_appointment IS \'Прием пациента\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.id IS \'Ключ приема пациента\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.medical_record_id IS \'Ключ записи в историю болезни\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.staff_id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.appointment_type_id IS \'Ключ вида приема\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.recommendation IS \'Рекомендации врача\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.appointment_time IS \'Дата и время приема\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.is_confirmed IS \'Подтверждение пользователем\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.complaints_comment IS \'Комментарий врача по жалобам\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.objective_status IS \'Объективный статус\'');
        $this->addSql('COMMENT ON COLUMN patient_appointment.therapy IS \'Терапия\'');
        $this->addSql(
            'CREATE TABLE patient_appointment_complaint (patient_appointment_id INT NOT NULL, complaint_id INT NOT NULL, PRIMARY KEY(patient_appointment_id, complaint_id))'
        );
        $this->addSql('CREATE INDEX IDX_7278AD235FA482B2 ON patient_appointment_complaint (patient_appointment_id)');
        $this->addSql('CREATE INDEX IDX_7278AD23EDAE188E ON patient_appointment_complaint (complaint_id)');
        $this->addSql('COMMENT ON COLUMN patient_appointment_complaint.patient_appointment_id IS \'Ключ приема пациента\'');
        $this->addSql('CREATE TABLE staff (id INT NOT NULL, hospital_id INT DEFAULT NULL, position_id INT NOT NULL, auth_user_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_426EF39263DBB69 ON staff (hospital_id)');
        $this->addSql('CREATE INDEX IDX_426EF392DD842E46 ON staff (position_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_426EF392E94AF366 ON staff (auth_user_id)');
        $this->addSql('COMMENT ON TABLE staff IS \'Персонал\'');
        $this->addSql('COMMENT ON COLUMN staff.id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN staff.hospital_id IS \'Ключ больницы\'');
        $this->addSql('COMMENT ON COLUMN staff.position_id IS \'Ключ должности\'');
        $this->addSql('COMMENT ON COLUMN staff.auth_user_id IS \'Ключ пользователя\'');
        $this->addSql('CREATE TABLE position (id INT NOT NULL, name VARCHAR(50) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE position IS \'Должность\'');
        $this->addSql('COMMENT ON COLUMN position.id IS \'Ключ должности\'');
        $this->addSql('COMMENT ON COLUMN position.name IS \'Название должности\'');
        $this->addSql('COMMENT ON COLUMN position.enabled IS \'Ограничение использования\'');
        $this->addSql('CREATE TABLE oksm (id INT NOT NULL, a2 VARCHAR(2) NOT NULL, a3 VARCHAR(3) NOT NULL, n3 INT NOT NULL, caption VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE oksm IS \'ОКСМ\'');
        $this->addSql('COMMENT ON COLUMN oksm.id IS \'Ключ ОКСМ\'');
        $this->addSql('COMMENT ON COLUMN oksm.a2 IS \'Двузначный код страны\'');
        $this->addSql('COMMENT ON COLUMN oksm.a3 IS \'Трехзначный код страны\'');
        $this->addSql('COMMENT ON COLUMN oksm.n3 IS \'Числовой код страны\'');
        $this->addSql('COMMENT ON COLUMN oksm.caption IS \'Название страны\'');
        $this->addSql(
            'CREATE TABLE prescription_medicine (id INT NOT NULL, prescription_id INT NOT NULL, medicine_id INT NOT NULL, reception_method_id INT NOT NULL, staff_id INT NOT NULL, instruction TEXT NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, inclusion_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_EFFCDA9A93DB413D ON prescription_medicine (prescription_id)');
        $this->addSql('CREATE INDEX IDX_EFFCDA9A2F7D140A ON prescription_medicine (medicine_id)');
        $this->addSql('CREATE INDEX IDX_EFFCDA9A2527130B ON prescription_medicine (reception_method_id)');
        $this->addSql('CREATE INDEX IDX_EFFCDA9AD4D57CD ON prescription_medicine (staff_id)');
        $this->addSql('COMMENT ON TABLE prescription_medicine IS \'Назначение лекарства\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.id IS \'Ключ назначения препарата\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.prescription_id IS \'Ключ назначения\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.medicine_id IS \'Ключ препарата\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.reception_method_id IS \'Ключ способа приема препарата\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.staff_id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.instruction IS \'Инструкция по применению\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN prescription_medicine.inclusion_time IS \'Дата и время включения лекарства в назначение\'');
        $this->addSql(
            'CREATE TABLE auth_user (id INT NOT NULL, email VARCHAR(180) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, phone CHAR(10) CHECK (LENGTH(phone) = 10), first_name VARCHAR(30) NOT NULL, last_name VARCHAR(100) NOT NULL, patronymic_name VARCHAR(50) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A3B536FD444F97DD ON auth_user (phone)');
        $this->addSql('COMMENT ON COLUMN auth_user.id IS \'Ключ пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.email IS \'Email пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.roles IS \'Роли пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.password IS \'Пароль пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.phone IS \'Телефон пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.first_name IS \'Имя пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.last_name IS \'Фамилия пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.patronymic_name IS \'Отчество пользователя\'');
        $this->addSql('COMMENT ON COLUMN auth_user.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE hospital (id INT NOT NULL, region_id INT NOT NULL, city_id INT DEFAULT NULL, lpu_id INT DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(50) NOT NULL, description TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, code VARCHAR(6) NOT NULL, email VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_4282C85B98260155 ON hospital (region_id)');
        $this->addSql('CREATE INDEX IDX_4282C85B8BAC62AF ON hospital (city_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4282C85BF2C7C2C1 ON hospital (lpu_id)');
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
        $this->addSql(
            'CREATE TABLE patient (id INT NOT NULL, auth_user_id INT NOT NULL, hospital_id INT NOT NULL, city_id INT NOT NULL, district_id INT DEFAULT NULL, address VARCHAR(255) NOT NULL, sms_informing BOOLEAN DEFAULT \'true\' NOT NULL, email_informing BOOLEAN DEFAULT \'true\' NOT NULL, snils VARCHAR(20) DEFAULT NULL, insurance_number VARCHAR(50) DEFAULT NULL, passport VARCHAR(20) DEFAULT NULL, weight INTEGER CHECK (weight >= 28), height INTEGER CHECK (height >= 48), date_birth DATE DEFAULT NULL, passport_issue_date DATE DEFAULT NULL, passport_issuing_authority VARCHAR(255) DEFAULT NULL, passport_issuing_authority_code VARCHAR(7) DEFAULT NULL, heart_attack_date DATE DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1ADAD7EBE94AF366 ON patient (auth_user_id)');
        $this->addSql('CREATE INDEX IDX_1ADAD7EB63DBB69 ON patient (hospital_id)');
        $this->addSql('CREATE INDEX IDX_1ADAD7EB8BAC62AF ON patient (city_id)');
        $this->addSql('CREATE INDEX IDX_1ADAD7EBB08FA272 ON patient (district_id)');
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
        $this->addSql(
            'CREATE TABLE oktmo (id INT NOT NULL, kod VARCHAR(11) NOT NULL, kod2 VARCHAR(11) NOT NULL, sub_kod1 INT NOT NULL, sub_kod2 INT DEFAULT NULL, sub_kod3 INT DEFAULT NULL, sub_kod4 INT DEFAULT NULL, p1 INT DEFAULT NULL, p2 INT DEFAULT NULL, kch INT DEFAULT NULL, name VARCHAR(300) NOT NULL, name2 VARCHAR(300) DEFAULT NULL, notes VARCHAR(255) DEFAULT NULL, federal_district_id INT NOT NULL, federal_district_name VARCHAR(255) NOT NULL, region_id INT NOT NULL, region_name VARCHAR(255) NOT NULL, settlement_type_id INT DEFAULT NULL, settlement_type_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON TABLE oktmo IS \'ОКТМО\'');
        $this->addSql('COMMENT ON COLUMN oktmo.id IS \'Ключ ОКТМО\'');
        $this->addSql(
            'CREATE TABLE city (id INT NOT NULL, region_id INT NOT NULL, district_id INT DEFAULT NULL, oktmo_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_2D5B023498260155 ON city (region_id)');
        $this->addSql('CREATE INDEX IDX_2D5B0234B08FA272 ON city (district_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D5B0234EE0C723D ON city (oktmo_id)');
        $this->addSql('COMMENT ON COLUMN city.id IS \'Ключ города\'');
        $this->addSql('COMMENT ON COLUMN city.region_id IS \'Ключ региона\'');
        $this->addSql('COMMENT ON COLUMN city.district_id IS \'Ключ района\'');
        $this->addSql('COMMENT ON COLUMN city.oktmo_id IS \'Ключ ОКТМО\'');
        $this->addSql('COMMENT ON COLUMN city.name IS \'Название города\'');
        $this->addSql('COMMENT ON COLUMN city.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE diagnosis (id INT NOT NULL, name VARCHAR(256) NOT NULL, code VARCHAR(50) NOT NULL, parent_code VARCHAR(50) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON COLUMN diagnosis.id IS \'Ключ записи\'');
        $this->addSql('COMMENT ON COLUMN diagnosis.name IS \'Название диагноза\'');
        $this->addSql('COMMENT ON COLUMN diagnosis.code IS \'Код диагноза\'');
        $this->addSql('COMMENT ON COLUMN diagnosis.parent_code IS \'Код группы диагнозов (диагноза верхнего уровня)\'');
        $this->addSql('COMMENT ON COLUMN diagnosis.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE prescription_testing (id INT NOT NULL, prescription_id INT NOT NULL, patient_testing_id INT NOT NULL, staff_id INT NOT NULL, inclusion_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_9F5F767893DB413D ON prescription_testing (prescription_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F5F7678B0EC09FD ON prescription_testing (patient_testing_id)');
        $this->addSql('CREATE INDEX IDX_9F5F7678D4D57CD ON prescription_testing (staff_id)');
        $this->addSql('COMMENT ON TABLE prescription_testing IS \'Назначение обследования\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.id IS \'Ключ назначения обследования\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.prescription_id IS \'Ключ назначения\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.patient_testing_id IS \'Ключ сдачи анализов\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.staff_id IS \'Ключ персонала\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.inclusion_time IS \'Дата и время включения в назначение\'');
        $this->addSql('COMMENT ON COLUMN prescription_testing.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE measure (id INT NOT NULL, name_ru VARCHAR(10) NOT NULL, name_en VARCHAR(10) DEFAULT NULL, title VARCHAR(100) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON COLUMN measure.id IS \'Ключ единицы измерения\'');
        $this->addSql('COMMENT ON COLUMN measure.name_ru IS \'Русское название единицы измерения\'');
        $this->addSql('COMMENT ON COLUMN measure.name_en IS \'Английское название единицы измерения\'');
        $this->addSql('COMMENT ON COLUMN measure.title IS \'Описание единицы измерения\'');
        $this->addSql('COMMENT ON COLUMN measure.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE district (id INT NOT NULL, region_id INT NOT NULL, oktmo_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_31C1548798260155 ON district (region_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_31C15487EE0C723D ON district (oktmo_id)');
        $this->addSql('COMMENT ON TABLE district IS \'Районы\'');
        $this->addSql('COMMENT ON COLUMN district.id IS \'Ключ района\'');
        $this->addSql('COMMENT ON COLUMN district.region_id IS \'Ключ региона\'');
        $this->addSql('COMMENT ON COLUMN district.oktmo_id IS \'Ключ ОКТМО\'');
        $this->addSql('COMMENT ON COLUMN district.name IS \'Название района\'');
        $this->addSql('COMMENT ON COLUMN district.enabled IS \'Ограничение использования\'');
        $this->addSql('CREATE TABLE date_interval (id INT NOT NULL, name VARCHAR(30) NOT NULL, title VARCHAR(30) NOT NULL, format VARCHAR(1) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON TABLE date_interval IS \'Интервал даты\'');
        $this->addSql('COMMENT ON COLUMN date_interval.id IS \'Ключ интервала\'');
        $this->addSql('COMMENT ON COLUMN date_interval.name IS \'Имя интервала\'');
        $this->addSql('COMMENT ON COLUMN date_interval.title IS \'Заголовок интервала\'');
        $this->addSql('COMMENT ON COLUMN date_interval.format IS \'Формат интервала\'');
        $this->addSql('CREATE TABLE medicine (id INT NOT NULL, name VARCHAR(50) NOT NULL, description TEXT NOT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN medicine.id IS \'Ключ препарата\'');
        $this->addSql('COMMENT ON COLUMN medicine.name IS \'Название препарата\'');
        $this->addSql('COMMENT ON COLUMN medicine.description IS \'Описание использования\'');
        $this->addSql('COMMENT ON COLUMN medicine.enabled IS \'Ограничение использования\'');
        $this->addSql('CREATE TABLE gender (id INT NOT NULL, name VARCHAR(1) NOT NULL, title VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN gender.id IS \'Ключ пола\'');
        $this->addSql('COMMENT ON COLUMN gender.name IS \'Название пола\'');
        $this->addSql('COMMENT ON COLUMN gender.title IS \'Заголовок пола\'');
        $this->addSql(
            'CREATE TABLE region (id INT NOT NULL, country_id INT NOT NULL, name VARCHAR(255) NOT NULL, region_number VARCHAR(8) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, oktmo_region_id INT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_F62F176F92F3E70 ON region (country_id)');
        $this->addSql('COMMENT ON COLUMN region.id IS \'Ключ региона\'');
        $this->addSql('COMMENT ON COLUMN region.country_id IS \'Ключ страны\'');
        $this->addSql('COMMENT ON COLUMN region.name IS \'Название региона\'');
        $this->addSql('COMMENT ON COLUMN region.region_number IS \'Номер региона\'');
        $this->addSql('COMMENT ON COLUMN region.enabled IS \'Ограничение использования\'');
        $this->addSql(
            'CREATE TABLE time_range (id INT NOT NULL, date_interval_id INT NOT NULL, title VARCHAR(30) DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, multiplier INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_D6F5BB356F2301F2 ON time_range (date_interval_id)');
        $this->addSql('COMMENT ON TABLE time_range IS \'Временной диапазон\'');
        $this->addSql('COMMENT ON COLUMN time_range.date_interval_id IS \'Ключ интервала\'');
        $this->addSql('COMMENT ON COLUMN time_range.title IS \'Заголовок временного диапазона\'');
        $this->addSql('COMMENT ON COLUMN time_range.enabled IS \'Ограничение использования\'');
        $this->addSql('COMMENT ON COLUMN time_range.multiplier IS \'Множитель\'');
        $this->addSql(
            'CREATE TABLE medical_history (id INT NOT NULL, patient_id INT NOT NULL, main_disease_id INT NOT NULL, date_begin DATE NOT NULL, date_end DATE DEFAULT NULL, disease_history TEXT DEFAULT NULL, life_history TEXT DEFAULT NULL, enabled BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_61B890856B899279 ON medical_history (patient_id)');
        $this->addSql('CREATE INDEX IDX_61B89085E0CD2722 ON medical_history (main_disease_id)');
        $this->addSql('COMMENT ON TABLE medical_history IS \'История болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_history.id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_history.patient_id IS \'Ключ пациента\'');
        $this->addSql('COMMENT ON COLUMN medical_history.main_disease_id IS \'Ключ записи\'');
        $this->addSql('COMMENT ON COLUMN medical_history.date_begin IS \'Дата открытия\'');
        $this->addSql('COMMENT ON COLUMN medical_history.date_end IS \'Дата закрытия\'');
        $this->addSql('COMMENT ON COLUMN medical_history.disease_history IS \'Анамнез болезни\'');
        $this->addSql('COMMENT ON COLUMN medical_history.life_history IS \'Анамнез жизни\'');
        $this->addSql('COMMENT ON COLUMN medical_history.enabled IS \'Ограничение использования\'');
        $this->addSql('CREATE TABLE background_diseases (medical_history_id INT NOT NULL, diagnosis_id INT NOT NULL, PRIMARY KEY(medical_history_id, diagnosis_id))');
        $this->addSql('CREATE INDEX IDX_2C1D0B803544AD9E ON background_diseases (medical_history_id)');
        $this->addSql('CREATE INDEX IDX_2C1D0B803CBE4D00 ON background_diseases (diagnosis_id)');
        $this->addSql('COMMENT ON COLUMN background_diseases.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN background_diseases.diagnosis_id IS \'Ключ записи\'');
        $this->addSql('CREATE TABLE complications (medical_history_id INT NOT NULL, diagnosis_id INT NOT NULL, PRIMARY KEY(medical_history_id, diagnosis_id))');
        $this->addSql('CREATE INDEX IDX_BC020A5F3544AD9E ON complications (medical_history_id)');
        $this->addSql('CREATE INDEX IDX_BC020A5F3CBE4D00 ON complications (diagnosis_id)');
        $this->addSql('COMMENT ON COLUMN complications.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN complications.diagnosis_id IS \'Ключ записи\'');
        $this->addSql('CREATE TABLE concomitant_diseases (medical_history_id INT NOT NULL, diagnosis_id INT NOT NULL, PRIMARY KEY(medical_history_id, diagnosis_id))');
        $this->addSql('CREATE INDEX IDX_59F3A8E03544AD9E ON concomitant_diseases (medical_history_id)');
        $this->addSql('CREATE INDEX IDX_59F3A8E03CBE4D00 ON concomitant_diseases (diagnosis_id)');
        $this->addSql('COMMENT ON COLUMN concomitant_diseases.medical_history_id IS \'Ключ истории болезни\'');
        $this->addSql('COMMENT ON COLUMN concomitant_diseases.diagnosis_id IS \'Ключ записи\'');
        $this->addSql(
            'ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAD0520624 FOREIGN KEY (notification_type_id) REFERENCES notification_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAB88E2BB6 FOREIGN KEY (medical_record_id) REFERENCES medical_record (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAD4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            'ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE analysis ADD CONSTRAINT FK_33C730174DAD14 FOREIGN KEY (analysis_group_id) REFERENCES analysis_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE plan_appointment ADD CONSTRAINT FK_A81202F8E07937D FOREIGN KEY (time_range_id) REFERENCES time_range (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            'ALTER TABLE plan_testing ADD CONSTRAINT FK_564C120E174DAD14 FOREIGN KEY (analysis_group_id) REFERENCES analysis_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE plan_testing ADD CONSTRAINT FK_564C120E8E07937D FOREIGN KEY (time_range_id) REFERENCES time_range (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            'ALTER TABLE medical_record ADD CONSTRAINT FK_F06A283E3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE patient_file ADD CONSTRAINT FK_50E7BD86B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            'ALTER TABLE patient_file ADD CONSTRAINT FK_50E7BD8B0EC09FD FOREIGN KEY (patient_testing_id) REFERENCES patient_testing (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D93544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D9D4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            'ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D9B88E2BB6 FOREIGN KEY (medical_record_id) REFERENCES medical_record (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE patient_testing ADD CONSTRAINT FK_B5900FED174DAD14 FOREIGN KEY (analysis_group_id) REFERENCES analysis_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE patient_testing ADD CONSTRAINT FK_B5900FED3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE patient_testing ADD CONSTRAINT FK_B5900FEDB88E2BB6 FOREIGN KEY (medical_record_id) REFERENCES medical_record (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE patient_testing_result ADD CONSTRAINT FK_82D2CA2AB0EC09FD FOREIGN KEY (patient_testing_id) REFERENCES patient_testing (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE patient_testing_result ADD CONSTRAINT FK_82D2CA2AC648F999 FOREIGN KEY (analysis_rate_id) REFERENCES analysis_rate (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE patient_testing_result ADD CONSTRAINT FK_82D2CA2A7941003F FOREIGN KEY (analysis_id) REFERENCES analysis (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE analysis_rate ADD CONSTRAINT FK_EE5F7AD27941003F FOREIGN KEY (analysis_id) REFERENCES analysis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE analysis_rate ADD CONSTRAINT FK_EE5F7AD25DA37D00 FOREIGN KEY (measure_id) REFERENCES measure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE analysis_rate ADD CONSTRAINT FK_EE5F7AD2708A0E0 FOREIGN KEY (gender_id) REFERENCES gender (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            'ALTER TABLE patient_appointment ADD CONSTRAINT FK_CE3BC70BB88E2BB6 FOREIGN KEY (medical_record_id) REFERENCES medical_record (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE patient_appointment ADD CONSTRAINT FK_CE3BC70B3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE patient_appointment ADD CONSTRAINT FK_CE3BC70BD4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            'ALTER TABLE patient_appointment ADD CONSTRAINT FK_CE3BC70B546FBEBB FOREIGN KEY (appointment_type_id) REFERENCES appointment_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE patient_appointment_complaint ADD CONSTRAINT FK_7278AD235FA482B2 FOREIGN KEY (patient_appointment_id) REFERENCES patient_appointment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE patient_appointment_complaint ADD CONSTRAINT FK_7278AD23EDAE188E FOREIGN KEY (complaint_id) REFERENCES complaint (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF39263DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF392DD842E46 FOREIGN KEY (position_id) REFERENCES position (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF392E94AF366 FOREIGN KEY (auth_user_id) REFERENCES auth_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            'ALTER TABLE prescription_medicine ADD CONSTRAINT FK_EFFCDA9A93DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE prescription_medicine ADD CONSTRAINT FK_EFFCDA9A2F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            'ALTER TABLE prescription_medicine ADD CONSTRAINT FK_EFFCDA9A2527130B FOREIGN KEY (reception_method_id) REFERENCES reception_method (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE prescription_medicine ADD CONSTRAINT FK_EFFCDA9AD4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B98260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85BF2C7C2C1 FOREIGN KEY (lpu_id) REFERENCES lpu (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBE94AF366 FOREIGN KEY (auth_user_id) REFERENCES auth_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBB08FA272 FOREIGN KEY (district_id) REFERENCES district (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B023498260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234B08FA272 FOREIGN KEY (district_id) REFERENCES district (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234EE0C723D FOREIGN KEY (oktmo_id) REFERENCES oktmo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            'ALTER TABLE prescription_testing ADD CONSTRAINT FK_9F5F767893DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE prescription_testing ADD CONSTRAINT FK_9F5F7678B0EC09FD FOREIGN KEY (patient_testing_id) REFERENCES patient_testing (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE prescription_testing ADD CONSTRAINT FK_9F5F7678D4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE district ADD CONSTRAINT FK_31C1548798260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE district ADD CONSTRAINT FK_31C15487EE0C723D FOREIGN KEY (oktmo_id) REFERENCES oktmo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_F62F176F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE time_range ADD CONSTRAINT FK_D6F5BB356F2301F2 FOREIGN KEY (date_interval_id) REFERENCES date_interval (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE medical_history ADD CONSTRAINT FK_61B890856B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE medical_history ADD CONSTRAINT FK_61B89085E0CD2722 FOREIGN KEY (main_disease_id) REFERENCES diagnosis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql(
            'ALTER TABLE background_diseases ADD CONSTRAINT FK_2C1D0B803544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE background_diseases ADD CONSTRAINT FK_2C1D0B803CBE4D00 FOREIGN KEY (diagnosis_id) REFERENCES diagnosis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE complications ADD CONSTRAINT FK_BC020A5F3544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE complications ADD CONSTRAINT FK_BC020A5F3CBE4D00 FOREIGN KEY (diagnosis_id) REFERENCES diagnosis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE concomitant_diseases ADD CONSTRAINT FK_59F3A8E03544AD9E FOREIGN KEY (medical_history_id) REFERENCES medical_history (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE concomitant_diseases ADD CONSTRAINT FK_59F3A8E03CBE4D00 FOREIGN KEY (diagnosis_id) REFERENCES diagnosis (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE patient_testing_result DROP CONSTRAINT FK_82D2CA2A7941003F');
        $this->addSql('ALTER TABLE analysis_rate DROP CONSTRAINT FK_EE5F7AD27941003F');
        $this->addSql('ALTER TABLE prescription_medicine DROP CONSTRAINT FK_EFFCDA9A2527130B');
        $this->addSql('ALTER TABLE analysis DROP CONSTRAINT FK_33C730174DAD14');
        $this->addSql('ALTER TABLE plan_testing DROP CONSTRAINT FK_564C120E174DAD14');
        $this->addSql('ALTER TABLE patient_testing DROP CONSTRAINT FK_B5900FED174DAD14');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAB88E2BB6');
        $this->addSql('ALTER TABLE prescription DROP CONSTRAINT FK_1FBFB8D9B88E2BB6');
        $this->addSql('ALTER TABLE patient_testing DROP CONSTRAINT FK_B5900FEDB88E2BB6');
        $this->addSql('ALTER TABLE patient_appointment DROP CONSTRAINT FK_CE3BC70BB88E2BB6');
        $this->addSql('ALTER TABLE patient_appointment_complaint DROP CONSTRAINT FK_7278AD23EDAE188E');
        $this->addSql('ALTER TABLE prescription_medicine DROP CONSTRAINT FK_EFFCDA9A93DB413D');
        $this->addSql('ALTER TABLE prescription_testing DROP CONSTRAINT FK_9F5F767893DB413D');
        $this->addSql('ALTER TABLE patient_file DROP CONSTRAINT FK_50E7BD8B0EC09FD');
        $this->addSql('ALTER TABLE patient_testing_result DROP CONSTRAINT FK_82D2CA2AB0EC09FD');
        $this->addSql('ALTER TABLE prescription_testing DROP CONSTRAINT FK_9F5F7678B0EC09FD');
        $this->addSql('ALTER TABLE patient_appointment DROP CONSTRAINT FK_CE3BC70B546FBEBB');
        $this->addSql('ALTER TABLE hospital DROP CONSTRAINT FK_4282C85BF2C7C2C1');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAD0520624');
        $this->addSql('ALTER TABLE patient_testing_result DROP CONSTRAINT FK_82D2CA2AC648F999');
        $this->addSql('ALTER TABLE region DROP CONSTRAINT FK_F62F176F92F3E70');
        $this->addSql('ALTER TABLE patient_appointment_complaint DROP CONSTRAINT FK_7278AD235FA482B2');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAD4D57CD');
        $this->addSql('ALTER TABLE prescription DROP CONSTRAINT FK_1FBFB8D9D4D57CD');
        $this->addSql('ALTER TABLE patient_appointment DROP CONSTRAINT FK_CE3BC70BD4D57CD');
        $this->addSql('ALTER TABLE prescription_medicine DROP CONSTRAINT FK_EFFCDA9AD4D57CD');
        $this->addSql('ALTER TABLE prescription_testing DROP CONSTRAINT FK_9F5F7678D4D57CD');
        $this->addSql('ALTER TABLE staff DROP CONSTRAINT FK_426EF392DD842E46');
        $this->addSql('ALTER TABLE staff DROP CONSTRAINT FK_426EF392E94AF366');
        $this->addSql('ALTER TABLE patient DROP CONSTRAINT FK_1ADAD7EBE94AF366');
        $this->addSql('ALTER TABLE staff DROP CONSTRAINT FK_426EF39263DBB69');
        $this->addSql('ALTER TABLE patient DROP CONSTRAINT FK_1ADAD7EB63DBB69');
        $this->addSql('ALTER TABLE patient_file DROP CONSTRAINT FK_50E7BD86B899279');
        $this->addSql('ALTER TABLE medical_history DROP CONSTRAINT FK_61B890856B899279');
        $this->addSql('ALTER TABLE city DROP CONSTRAINT FK_2D5B0234EE0C723D');
        $this->addSql('ALTER TABLE district DROP CONSTRAINT FK_31C15487EE0C723D');
        $this->addSql('ALTER TABLE hospital DROP CONSTRAINT FK_4282C85B8BAC62AF');
        $this->addSql('ALTER TABLE patient DROP CONSTRAINT FK_1ADAD7EB8BAC62AF');
        $this->addSql('ALTER TABLE medical_history DROP CONSTRAINT FK_61B89085E0CD2722');
        $this->addSql('ALTER TABLE background_diseases DROP CONSTRAINT FK_2C1D0B803CBE4D00');
        $this->addSql('ALTER TABLE complications DROP CONSTRAINT FK_BC020A5F3CBE4D00');
        $this->addSql('ALTER TABLE concomitant_diseases DROP CONSTRAINT FK_59F3A8E03CBE4D00');
        $this->addSql('ALTER TABLE analysis_rate DROP CONSTRAINT FK_EE5F7AD25DA37D00');
        $this->addSql('ALTER TABLE patient DROP CONSTRAINT FK_1ADAD7EBB08FA272');
        $this->addSql('ALTER TABLE city DROP CONSTRAINT FK_2D5B0234B08FA272');
        $this->addSql('ALTER TABLE time_range DROP CONSTRAINT FK_D6F5BB356F2301F2');
        $this->addSql('ALTER TABLE prescription_medicine DROP CONSTRAINT FK_EFFCDA9A2F7D140A');
        $this->addSql('ALTER TABLE analysis_rate DROP CONSTRAINT FK_EE5F7AD2708A0E0');
        $this->addSql('ALTER TABLE hospital DROP CONSTRAINT FK_4282C85B98260155');
        $this->addSql('ALTER TABLE city DROP CONSTRAINT FK_2D5B023498260155');
        $this->addSql('ALTER TABLE district DROP CONSTRAINT FK_31C1548798260155');
        $this->addSql('ALTER TABLE plan_appointment DROP CONSTRAINT FK_A81202F8E07937D');
        $this->addSql('ALTER TABLE plan_testing DROP CONSTRAINT FK_564C120E8E07937D');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CA3544AD9E');
        $this->addSql('ALTER TABLE medical_record DROP CONSTRAINT FK_F06A283E3544AD9E');
        $this->addSql('ALTER TABLE prescription DROP CONSTRAINT FK_1FBFB8D93544AD9E');
        $this->addSql('ALTER TABLE patient_testing DROP CONSTRAINT FK_B5900FED3544AD9E');
        $this->addSql('ALTER TABLE patient_appointment DROP CONSTRAINT FK_CE3BC70B3544AD9E');
        $this->addSql('ALTER TABLE background_diseases DROP CONSTRAINT FK_2C1D0B803544AD9E');
        $this->addSql('ALTER TABLE complications DROP CONSTRAINT FK_BC020A5F3544AD9E');
        $this->addSql('ALTER TABLE concomitant_diseases DROP CONSTRAINT FK_59F3A8E03544AD9E');
        $this->addSql('DROP SEQUENCE notification_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE role_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE plan_appointment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE plan_testing_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reception_method_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE medical_record_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE patient_file_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE complaint_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prescription_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE patient_testing_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE appointment_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE lpu_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE patient_testing_result_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notification_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE country_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE patient_appointment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE staff_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE position_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oksm_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prescription_medicine_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE auth_user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE hospital_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE patient_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oktmo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE city_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE diagnosis_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prescription_testing_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE district_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE medicine_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE region_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE medical_history_id_seq CASCADE');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE analysis');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE plan_appointment');
        $this->addSql('DROP TABLE plan_testing');
        $this->addSql('DROP TABLE reception_method');
        $this->addSql('DROP TABLE analysis_group');
        $this->addSql('DROP TABLE medical_record');
        $this->addSql('DROP TABLE patient_file');
        $this->addSql('DROP TABLE complaint');
        $this->addSql('DROP TABLE prescription');
        $this->addSql('DROP TABLE patient_testing');
        $this->addSql('DROP TABLE appointment_type');
        $this->addSql('DROP TABLE lpu');
        $this->addSql('DROP TABLE patient_testing_result');
        $this->addSql('DROP TABLE notification_type');
        $this->addSql('DROP TABLE analysis_rate');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE patient_appointment');
        $this->addSql('DROP TABLE patient_appointment_complaint');
        $this->addSql('DROP TABLE staff');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE oksm');
        $this->addSql('DROP TABLE prescription_medicine');
        $this->addSql('DROP TABLE auth_user');
        $this->addSql('DROP TABLE hospital');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE oktmo');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE diagnosis');
        $this->addSql('DROP TABLE prescription_testing');
        $this->addSql('DROP TABLE measure');
        $this->addSql('DROP TABLE district');
        $this->addSql('DROP TABLE date_interval');
        $this->addSql('DROP TABLE medicine');
        $this->addSql('DROP TABLE gender');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE time_range');
        $this->addSql('DROP TABLE medical_history');
        $this->addSql('DROP TABLE background_diseases');
        $this->addSql('DROP TABLE complications');
        $this->addSql('DROP TABLE concomitant_diseases');
    }
}
