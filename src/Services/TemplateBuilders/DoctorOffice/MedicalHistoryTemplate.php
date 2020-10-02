<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\TemplateBuilders\AdminTemplateBuilder;
use App\Services\TemplateBuilders\AuthUserTemplate;
use App\Services\TemplateBuilders\PatientAppointmentTemplate;
use App\Services\TemplateBuilders\PatientTemplate;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class MedicalHistoryTemplate
 *
 * @package App\Services\TemplateBuilders\PatientOffice
 */
class MedicalHistoryTemplate extends AdminTemplateBuilder
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
        'documentaryData' => 'Документальные данные',
        'objectiveData' => 'Объективные данные',
        'addDocumentaryData' => 'Внести документальные данные',
        'addPersonalData' => 'Внести личные данные',
        'addObjectiveData' => 'Внести объективные данные',
        'recommendationNotFound' => 'Рекомендации врача отсутствуют',
        'mainDiseaseNotFound' => 'Основное заболевание не найдено!',
        'backgroundDiseasesNotFound' => 'Фоновые заболевания отсутствуют',
        'concomitantDiseasesNotFound' => 'Сопутствующие заболевания отсутствуют',
        'appointmentTypeNotFound' => 'Вид приема пациента отсутствует',
        'complicationsNotFound' => 'Осложнения отсутствуют',
        'diseaseHistoryNotFound' => 'Анамнез болезни отсутствует',
        'lifeHistoryNotFound' => 'Анамнез жизни отсутствует',
        'complaintsCommentNotFound' => 'Комментарий врача по жалобам отсутствует',
        'objectiveStatusNotFound' => 'Объективный статус отсутствует',
        'therapyNotFound' => 'Терапия отсутствует',
    ];

    /** @var string[] Common form and show content for medical history templates */
    protected const FORM_SHOW_CONTENT = [
        'dateBegin' => 'Дата начала лечения',
        'dateEnd' => 'Дата окончания лечения',
    ];

    /** @var string[] Common form content for edit templates */
    protected const EDIT_PERSONAL_DATA = [
        'h2' => 'Редактирование персональных данных',
        'title' => 'Редактирование персональных данных',
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

    /**
     * @param object|null $entity
     *
     * @return $this|AdminTemplateBuilder
     */
    public function edit(?object $entity = null): AdminTemplateBuilder
    {
        parent::edit();
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->setPath($this->getTemplatePath())
            ->addContentArray(
                array_merge(
                    PatientAppointmentTemplate::COMMON_CONTENT,
                    PatientAppointmentTemplate::FORM_SHOW_CONTENT,
                    PatientAppointmentTemplate::FORM_CONTENT,
                    \App\Services\TemplateBuilders\MedicalHistoryTemplate::FORM_SHOW_CONTENT,
                    \App\Services\TemplateBuilders\MedicalHistoryTemplate::COMMON_CONTENT,
                    AuthUserTemplate::COMMON_CONTENT,
                    AuthUserTemplate::FORM_CONTENT,
                    AuthUserTemplate::FORM_SHOW_CONTENT,
                    PatientTemplate::COMMON_CONTENT,
                    PatientTemplate::FORM_SHOW_CONTENT,
                    PatientTemplate::FORM_CONTENT
                )
            );
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)->setContents(self::EDIT_PERSONAL_DATA);
        return $this;
    }

    /**
     * @param object|null $entity
     *
     * @return $this|AdminTemplateBuilder
     */
    public function show(?object $entity = null): AdminTemplateBuilder
    {
        parent::show();
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)->addContentArray(
            array_merge(
                PatientAppointmentTemplate::COMMON_CONTENT,
                PatientAppointmentTemplate::FORM_SHOW_CONTENT,
                PatientAppointmentTemplate::SHOW_CONTENT,
                \App\Services\TemplateBuilders\MedicalHistoryTemplate::FORM_SHOW_CONTENT,
                \App\Services\TemplateBuilders\MedicalHistoryTemplate::COMMON_CONTENT,
                AuthUserTemplate::COMMON_CONTENT,
                AuthUserTemplate::FORM_CONTENT,
                AuthUserTemplate::FORM_SHOW_CONTENT,
                PatientTemplate::COMMON_CONTENT,
                PatientTemplate::FORM_SHOW_CONTENT,
                PatientTemplate::SHOW_CONTENT,
                PatientTemplate::FORM_CONTENT
            )
        );
        return $this;
    }
}