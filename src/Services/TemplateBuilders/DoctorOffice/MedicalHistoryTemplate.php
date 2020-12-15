<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\PatientTestingResultTemplate;
use App\Services\TemplateBuilders\Admin\PatientTestingTemplate;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateBuilders\Admin\AuthUserTemplate;
use App\Services\TemplateBuilders\Admin\PatientAppointmentTemplate;
use App\Services\TemplateBuilders\Admin\PatientTemplate;
use App\Services\TemplateItems\FormTemplateItem;
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
        'anamnesticData' => 'Анамнестические данные',
        'objectiveData' => 'Объективные данные',
        'addAnamnesticData' => 'Внести анамнестические данные',
        'addPersonalData' => 'Внести личные данные',
        'addObjectiveData' => 'Внести объективные данные',
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
        'laboratoryData' => 'Лабораторные данные',
        'addPatientTestingResults' => 'Внести результаты обследования',
        'firstTestings' => 'Обследования',
        'addDischargeEpicrises' => 'Добавить выписные эпикризы',
    ];

    /** @var string[] Common form and show content for medical history templates */
    protected const FORM_SHOW_CONTENT = [
        'dateBegin' => 'Дата начала лечения',
        'dateEnd' => 'Дата окончания лечения',
    ];

    /** @var string[] Common form content for edit templates */
    protected const EDIT_CONTENT = [
        'personal_h2' => 'Редактирование персональных данных',
        'personal_title' => 'Редактирование персональных данных',
        'anamnestic_h2' => 'Редактирование анамнестических данных',
        'anamnestic_title' => 'Редактирование анамнестических данных',
        'objective_h2' => 'Редактирование объективных данных',
        'objective_title' => 'Редактирование объективных данных',
    ];

    protected const NEW_CONTENT = [
        'discharge_epicrisis' => 'Добавление выписных эпикризов',
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
            self::LIST_CONTENT,
            self::NEW_CONTENT,
            self::SHOW_CONTENT,
            self::EDIT_CONTENT,
            self::FORM_CONTENT,
            self::FORM_SHOW_CONTENT,
            self::COMMON_CONTENT,
            self::FILTER_CONTENT
        );
    }

    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new($filterService);
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->setPath($this->getTemplatePath());
        return $this;
    }

    /**
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->setPath($this->getTemplatePath())
            ->addContentArray(
                array_merge(
                    PatientAppointmentTemplate::COMMON_CONTENT,
                    PatientAppointmentTemplate::FORM_SHOW_CONTENT,
                    PatientAppointmentTemplate::FORM_CONTENT,
                    \App\Services\TemplateBuilders\Admin\MedicalHistoryTemplate::FORM_SHOW_CONTENT,
                    \App\Services\TemplateBuilders\Admin\MedicalHistoryTemplate::COMMON_CONTENT,
                    AuthUserTemplate::COMMON_CONTENT,
                    AuthUserTemplate::FORM_CONTENT,
                    AuthUserTemplate::FORM_SHOW_CONTENT,
                    PatientTemplate::COMMON_CONTENT,
                    PatientTemplate::FORM_SHOW_CONTENT,
                    PatientTemplate::FORM_CONTENT,
                    PatientTestingTemplate::COMMON_CONTENT,
                    PatientTestingResultTemplate::COMMON_CONTENT
                )
            );
        return $this;
    }

    /**
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show();
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