<?php

namespace App\AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

/**
 * Class MenuBuilder
 *
 * @package App\AppBundle\Menu
 */
class MenuBuilder
{
    /** @var FactoryInterface $factory */
    private $factory;

    /** @var ContainerInterface $container */
    private $container;

    /** @var Security $security */
    private $security;

    /**
     * @param FactoryInterface $factory
     * @param ContainerInterface $container
     * @param Security $security
     */
    public function __construct(FactoryInterface $factory, ContainerInterface $container, Security $security)
    {
        $this->factory = $factory;
        $this->container = $container;
        $this->security = $security;
    }

    /**
     * Меню на главной (версия для разработки)
     *
     * @return ItemInterface
     */
    public function createMainMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild(
            'index', [
                'label' => 'Главная',
                'route' => 'index'
            ]
        );
        $menu->setChildrenAttribute('class', 'test');
        $menu->addChild(
            'login', [
                'label' => 'Войти',
                'route' => 'app_login'
            ]
        );
        $menu->addChild(
            'admin', [
                'label' => 'Админка',
                'route' => 'admin'
            ]
        );
        $menu->addChild(
            'patientOffice', [
                'label' => 'Кабинет пациента',
                'route' => 'patient_office_main'
            ]
        );
        $menu->addChild(
            'doctorOffice', [
                'label' => 'Кабинет врача',
                'route' => 'patients_list'
            ]
        );
        $menu['index']->setAttribute('class', 'btn');
        $menu['index']->setAttribute('icon', 'fa fa-tasks');

        return $menu;
    }

    /**
     * Меню админки (версия для разработки)
     *
     * @param RequestStack $requestStack
     *
     * @return ItemInterface
     */
    public function createAdminMenu(RequestStack $requestStack): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild(
            'index', [
                'label' => 'Главная',
                'route' => 'index'
            ]
        );
        $menu->addChild(
            'users', [
                'label' => 'Управление пользователями',
            ]
        )->setAttribute('class', 'sublist');
        $menu['users']->addChild(
            'authUserList', [
                'label' => 'Пользователи',
                'route' => 'auth_user_list'
            ]
        );
        $menu['users']->addChild(
            'roleList', [
                'label' => 'Роли',
                'route' => 'role_list'
            ]
        );
        $menu['users']->addChild(
            'patientsList', [
                'label' => 'Пациенты',
                'route' => 'patient_list'
            ]
        );
        $menu['users']->addChild(
            'staffList', [
                'label' => 'Сотрудники',
                'route' => 'staff_list'
            ]
        );
        $menu['users']->addChild(
            'positionList', [
                'label' => 'Должности',
                'route' => 'position_list'
            ]
        );
        $menu->addChild(
            'medicalHistory', [
                'label' => 'Управление историями болезни',
            ]
        )->setAttribute('class', 'sublist');
        $menu['medicalHistory']->addChild(
            'medicalHistoryList', [
                'label' => 'Истории болезни',
                'route' => 'medical_history_list'
            ]
        );
        $menu['medicalHistory']->addChild(
            'patientTestingList', [
                'label' => 'Сдача анализов',
                'route' => 'patient_testing_list'
            ]
        );
        $menu['medicalHistory']->addChild(
            'patientTestingResultList', [
                'label' => 'Результаты анализов',
                'route' => 'patient_testing_result_list'
            ]
        );
        $menu['medicalHistory']->addChild(
            'medicalRecordsList', [
                'label' => 'Записи в историю болезни',
                'route' => 'medical_record_list'
            ]
        );
        $menu['medicalHistory']->addChild(
            'appointmentList', [
                'label' => 'Приемы пациентов',
                'route' => 'patient_appointment_list'
            ]
        );
        $menu['medicalHistory']->addChild(
            'prescriptionList', [
                'label' => 'Назначения',
                'route' => 'prescription_list'
            ]
        );
        $menu['medicalHistory']->addChild(
            'prescriptionMedicineList', [
                'label' => 'Назначения лекарств',
                'route' => 'prescription_medicine_list'
            ]
        );
        $menu['medicalHistory']->addChild(
            'prescriptionTestingList', [
                'label' => 'Назначения на обследование',
                'route' => 'prescription_testing_list'
            ]
        );
        $menu['medicalHistory']->addChild(
            'prescriptionAppointmentList', [
                'label' => 'Назначения на прием',
                'route' => 'prescription_appointment_list'
            ]
        );
        $menu->addChild(
            'medical_guides', [
                'label' => 'Медицинские справочники',
            ]
        )->setAttribute('class', 'sublist');
        $menu['medical_guides']->addChild(
            'diagnosisList', [
                'label' => 'Диагнозы',
                'route' => 'diagnosis_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'medicineList', [
                'label' => 'Препараты',
                'route' => 'medicine_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'receptionMethodList', [
                'label' => 'Способы приема',
                'route' => 'reception_method_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'appointmentTypeList', [
                'label' => 'Виды приема',
                'route' => 'appointment_type_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'analysisGroupList', [
                'label' => 'Группы анализов',
                'route' => 'analysis_group_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'analysisList', [
                'label' => 'Анализы',
                'route' => 'analysis_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'analysisRateList', [
                'label' => 'Референтные значения',
                'route' => 'analysis_rate_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'planTestingList', [
                'label' => 'План анализов',
                'route' => 'plan_testing_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'planAppointmentList', [
                'label' => 'План приемов',
                'route' => 'plan_appointment_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'measureList', [
                'label' => 'Единицы измерения',
                'route' => 'measure_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'complaintList', [
                'label' => 'Жалобы',
                'route' => 'complaint_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'timeRangeList', [
                'label' => 'Временные диапазоны',
                'route' => 'time_range_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'templates', [
                'label' => 'Шаблоны',
                'route' => 'template_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'templatesTypes', [
                'label' => 'Типы шаблонов',
                'route' => 'template_type_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'templatesParametres', [
                'label' => 'Параметры шаблонов',
                'route' => 'template_parameter_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'templatesParametersTexts', [
                'label' => 'Тексты параметров шаблонов',
                'route' => 'template_parameter_text_list'
            ]
        );
        $menu['medical_guides']->addChild(
            'startingPoints', [
                'label' => 'Точки отсчета',
                'route' => 'starting_point_list'
            ]
        );
        $menu->addChild(
            'locations', [
                'label' => 'Управление локациями',
            ]
        )->setAttribute('class', 'sublist');
        $menu['locations']->addChild(
            'countriesList', [
                'label' => 'Страны',
                'route' => 'country_list'
            ]
        );
        $menu['locations']->addChild(
            'regionsList', [
                'label' => 'Регионы',
                'route' => 'region_list'
            ]
        );
        $menu['locations']->addChild(
            'districtList', [
                'label' => 'Районы',
                'route' => 'district_list'
            ]
        );
        $menu['locations']->addChild(
            'cityList', [
                'label' => 'Города',
                'route' => 'city_list'
            ]
        );
        $menu['locations']->addChild(
            'hospitalList', [
                'label' => 'Больницы',
                'route' => 'hospital_list'
            ]
        );
        $menu->addChild(
            'log', [
                'label' => 'Логи',
            ]
        )->setAttribute('class', 'sublist');
        $menu['log']->addChild(
            'log', [
                'label' => 'Лог',
                'route' => 'log_list'
            ]
        );
        $menu['log']->addChild(
            'logAction', [
                'label' => 'Типы логов',
                'route' => 'log_action_list'
            ]
        );
        $menu->addChild(
            'sms', [
                'label' => 'Принятые SMS',
                'route' => 'patient_sms_list'
            ]
        );
        $menu->addChild(
            'notification', [
                'label' => 'Уведомления',
            ]
        )->setAttribute('class', 'sublist');
        $menu['notification']->addChild(
            'notification', [
                'label' => 'Уведомления',
                'route' => 'notification_list'
            ]
        );
        $menu['notification']->addChild(
            'smsNotifications', [
                'label' => 'SMS Уведомления',
                'route' => 'sms_notification_list'
            ]
        );
        $menu['notification']->addChild(
            'emailNotifications', [
                'label' => 'Email Уведомления',
                'route' => 'email_notification_list'
            ]
        );
        $menu['notification']->addChild(
            'webNotifications', [
                'label' => 'Web Уведомления',
                'route' => 'web_notification_list'
            ]
        );
        foreach ($menu->getChildren() as $item) {
            foreach ($item->getChildren() as $childrenItem) {
                if ($childrenItem->getUri() == $requestStack->getCurrentRequest()->getRequestUri()) {
                    $childrenItem->setCurrent(true);
                }
            }
        }
        return $menu;
    }

    /**
     * Меню кабинета врача в header
     *
     * @return ItemInterface
     */
    public function createDoctorOfficeHeaderMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'main-nav__list');
        $menu->addChild(
            'add_patient', [
                'label' => 'Добавить пациента',
                'route' => 'adding_patient_by_doctor'
            ]
        );
        $menu->addChild(
            'logout', [
                'label' => 'Выйти',
                'route' => 'logout_from_app'
            ]
        );
        return $menu;
    }

    /**
     * @return ItemInterface
     */
    public function createAdminHeaderMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'main-nav__list');
        $menu->addChild(
            'logout', [
                'label' => 'Выйти',
                'route' => 'logout_from_app'
            ]
        );
        return $menu;
    }

    /**
     * Меню кабинета врача в sidebar
     *
     * @return ItemInterface
     */
    public function createDoctorOfficeSidebarMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'sidebar__list');
        $menu->addChild(
            'patientsList', [
                'label' => 'Все',
                'route' => 'patients_list'
            ]
        );
        $menu->addChild(
            'patientsWithNoResultsList', [
                'label' => 'Нет анализов',
                'route' => 'patients_with_no_results_list'
            ]
        );
        $menu->addChild(
            'patientsWithNoProcessedList', [
                'label' => 'Обработать анализы',
                'route' => 'patients_with_no_processed_list'
            ]
        );
        $menu->addChild(
            'patientsWithOpenedPrescriptionsList', [
                'label' => 'Закрыть назначения',
                'route' => 'patients_with_opened_prescriptions_list'
            ]
        );
        $menu->addChild(
            'patientsWithProcessedResultsList', [
                'label' => 'Обработанные',
                'route' => 'patients_with_processed_results_list'
            ]
        );

        return $menu;
    }
}
