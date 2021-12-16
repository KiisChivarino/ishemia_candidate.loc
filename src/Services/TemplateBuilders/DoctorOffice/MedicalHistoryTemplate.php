<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateBuilders\Admin\AuthUserTemplate;
use App\Services\TemplateBuilders\Admin\PatientAppointmentTemplate;
use App\Services\TemplateBuilders\Admin\PatientTemplate;
use App\Services\TemplateItems\ShowTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class MedicalHistoryTemplate
 *
 * @package App\Services\TemplateBuilders\PatientOffice
 */
class MedicalHistoryTemplate extends DoctorOfficeTemplateBuilder
{
    /** @var string[] Common show content for medical history templates */
    protected const SHOW_CONTENT = [
        'h1' => 'История болезни',
        'title' => 'История болезни',
        'fio' => 'ФИО',
        'age' => 'Возраст',
        'imt' => 'ИМТ',
        'bySms' => 'Информируется по смс',
        'byEmail' => 'Информируется по email',
        'personalData' => 'Личные данные',
        'anamnesticData' => 'Клинический диагноз',
        'objectiveData' => 'Первичный осмотр пациента',
        'addAnamnesticData' => 'Внести клинический диагноз',
        'addPersonalData' => 'Внести личные данные',
        'addObjectiveData' => 'Внести первичный осмотр пациента',
        'recommendationNotFound' => 'Рекомендации врача отсутствуют',
        'mainDiseaseNotFound' => 'Основное заболевание не найдено!',
        'backgroundDiseasesNotFound' => 'Фоновые заболевания отсутствуют',
        'concomitantDiseasesNotFound' => 'Сопутствующие заболевания отсутствуют',
        'appointmentTypeNotFound' => 'Вид приема пациента отсутствует',
        'complicationsNotFound' => 'Осложнения основного заболевания отсутствуют',
        'diseaseHistoryNotFound' => 'Анамнез болезни отсутствует',
        'lifeHistoryNotFound' => 'Анамнез жизни отсутствует',
        'complaintsCommentNotFound' => 'Комментарий врача по жалобам отсутствует',
        'objectiveStatusNotFound' => 'Объективный статус отсутствует',
        'therapyNotFound' => 'Терапия отсутствует',
        'laboratoryData' => 'Медицинская документация',
        'addPatientTestingResults' => 'Внести результаты обследования',
        'firstTestings' => 'Обследования',
        'addDischargeEpicrises' => 'Добавить выписные эпикризы',
        'diseaseHistory' => 'Анамнез болезни',
        'resultData' => 'Данные результатов обследования',
        'files' => 'Файлы',
    ];

    /** @var string[] Common ENTITY_CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => \App\Services\TemplateBuilders\Admin\MedicalHistoryTemplate::ENTITY_CONTENT['entity'],
    ];

    /** @var string[] Common COMMON_CONTENT */
    protected const COMMON_CONTENT = [
        'text' => 'Текст клинического диагноза',
        'MKBCode' => 'Код клинического диагноза',
    ];

    /**
     * MedicalHistoryTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct($routeCollection, $className);
        $this->addContent(
            null,
            null,
            self::SHOW_CONTENT,
            null,
            null,
            null,
            null,
            null,
            self::ENTITY_CONTENT
        );
    }

    /**
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show($entity);
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)->addContentArray(
            array_merge(
                PatientAppointmentTemplate::COMMON_CONTENT,
                PatientAppointmentTemplate::FORM_SHOW_CONTENT,
                PatientAppointmentTemplate::SHOW_CONTENT,
                \App\Services\TemplateBuilders\Admin\MedicalHistoryTemplate::FORM_SHOW_CONTENT,
                \App\Services\TemplateBuilders\Admin\MedicalHistoryTemplate::COMMON_CONTENT,
                AuthUserTemplate::COMMON_CONTENT,
                AuthUserTemplate::FORM_CONTENT,
                AuthUserTemplate::FORM_SHOW_CONTENT,
                PatientTemplate::COMMON_CONTENT,
                PatientTemplate::FORM_SHOW_CONTENT,
                PatientTemplate::SHOW_CONTENT,
                PatientTemplate::FORM_CONTENT,
                $this->showContent
            )
        );
        return $this;
    }
}