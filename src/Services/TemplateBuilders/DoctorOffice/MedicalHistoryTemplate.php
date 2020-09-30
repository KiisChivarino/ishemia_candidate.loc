<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\TemplateBuilders\AdminTemplateBuilder;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
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
        'addDocumentaryData' => 'Внести документальные данные',
        'addPersonalData' => 'Внести личные данные',
    ];

    /** @var string[] Common form and show content for medical history templates */
    protected const FORM_SHOW_CONTENT = [
        'address' => 'Адрес проживания',
        'insuranceNumber' => 'Страховой полис',
        'weight' => 'Вес',
        'height' => 'Рост',
        'hospital' => 'Больница',
        'SNILS' => 'СНИЛС',
        'phone' => 'Телефон',
        'email' => 'Email',
        'passport' => 'Паспортные данные',
        'city' => 'Город',
        'district' => 'Район',
        'diagnosis' => 'Диагнозы',
        'dateBegin' => 'Дата начала лечения',
        'dateEnd' => 'Дата окончания лечения',
        'mainDiseaseNotFound' => 'Основное заболевание не найдено!',
        'mainDisease' => 'Основное заболевание',
        'backgroundDiseasesNotFound' => 'Фоновые заболевания отсутствуют',
        'complicationsNotFound' => 'Осложнения отсутствуют',
        'concomitantDiseasesNotFound' => 'Сопутствующие заболевания отсутствуют',
        'diseaseHistoryNotFound' => 'Анамнез болезни отсутствует',
        'lifeHistoryNotFound' => 'Анамнез жизни отсутствует',
        'mainDiseasePlaceholder' => 'Выберите заболевание',
        'backgroundDiseases' => 'Фоновые заболевания',
        'backgroundDiseasesPlaceholder' => 'Выберите фоновые заболевания',
        'complications' => 'Осложнения',
        'complicationsPlaceholder' => 'Выберите осложнения',
        'concomitantDiseases' => 'Сопутствующие заболевания',
        'concomitantDiseasesPlaceholder' => 'Выберите сопутствующие заболевания',
        'diseaseHistory' => 'Анамнез заболевания',
        'lifeHistory' => 'Анамнез жизни',
    ];

    /** @var string[] Common form content for medical history templates */
    protected const FORM_CONTENT = [
        'lastName' => 'Фамилия',
        'firstName' => 'Имя',
        'patronymicName' => 'Отчество',
        'smsInforming' => 'Информировать по смс',
        'emailInforming' => 'Информировать по email',
        'dateBirth' => 'Дата рождения',
        'hospitalPlaceholder' => 'Выберите больницу',
        'cityPlaceholder' => 'Выберите город',
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
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)->setPath($this->getTemplatePath());
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)->setContents(self::EDIT_PERSONAL_DATA);
        return $this;
    }
}