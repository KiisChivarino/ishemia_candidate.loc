<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\Admin\AuthUserTemplate;
use App\Services\TemplateBuilders\Admin\MedicalHistoryTemplate;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class CreateNewPatientTemplate
 *
 * @package App\Services\TemplateBuilders\DoctorOffice
 */
class CreateNewPatientTemplate extends DoctorOfficeTemplateBuilder
{
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
     * @param FilterService|null $filterService
     * @return $this|AppTemplateBuilder
     */
    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new();
//        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)->setPath($this->getTemplatePath());
        $this->setCommonTemplatePath($this->getTemplatePath());
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->addContentArray(
                array_merge(
                    AuthUserTemplate::COMMON_CONTENT,
                    AuthUserTemplate::FORM_CONTENT,
                    AuthUserTemplate::FORM_SHOW_CONTENT,
                    MedicalHistoryTemplate::FORM_SHOW_CONTENT,
                    MedicalHistoryTemplate::COMMON_CONTENT
                )
            );
        return $this;
    }
}