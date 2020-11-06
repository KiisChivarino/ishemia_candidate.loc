<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\TemplateBuilders\AdminTemplateBuilder;
use App\Services\TemplateBuilders\AuthUserTemplate;
use App\Services\TemplateBuilders\PatientAppointmentTemplate;
use App\Services\TemplateBuilders\PatientTemplate;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class CreateNewPatientTemplate
 *
 * @package App\Services\TemplateBuilders\DoctorOffice
 */
class CreateNewPatientTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common show content for medical history templates */
    protected const SHOW_CONTENT = [

    ];

    /** @var string[] Common form and show content for medical history templates */
    protected const FORM_SHOW_CONTENT = [
        'dateBegin' => 'Дата начала лечения',
        'dateEnd' => 'Дата окончания лечения',
        'lastName' => 'Фамилия',
        'firstName' => 'Имя',
        'patronymicName' => 'Отчество',
        'phone' => 'Телефон',
        'phoneHelp' => 'Введите телефон 10 цифр',
        'snils' => 'СНИЛС',
        'address' => 'Адрес',
        'smsInforming' => 'Информировать по смс',
        'emailInforming' => 'Информировать по email',
        'passport' => 'Паспорт',
        'weight' => 'Вес',
        'height' => 'Рост',
        'city' => 'Город',
        'district' => 'Район',
        'hospital' => 'Больница',
        'heartAttackDate' => 'Дата возникновения инфаркта',
        'email' => 'Email',
        'password' => 'Пароль',
        'passwordHelp' => 'Введите пароль не менее 6 знаков, включая английские символы, спецсимволы и цифры',
        'dateBirth' => 'Дата рождения',
        'insuranceNumber' => 'Номер страховки',
        'cityPlaceholder' => 'Выберите город',
        'hospitalPlaceholder' => 'Выберите больницу',
        'mainDisease' => 'Основное заболевание',
        'mainDiseasePlaceholder' => 'Выберите заболевание',
        'staff' => 'Отправивший врач',
        'appointmentType' => 'Вид приема',
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