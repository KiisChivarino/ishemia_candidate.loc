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
    public function createMainMenu()
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
    public function createAdminMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild(
            'index', [
                'label' => 'Главная',
                'route' => 'index'
            ]
        );
        if (in_array('ROLE_DEVELOPER', $this->security->getUser()->getRoles())) {
            $menu->addChild(
                'blog', [
                    'label' => 'Блог',
                ]
            )->setAttribute('class', 'sublist');
            $menu['blog']->addChild(
                'blogRecordList', [
                    'label' => 'Записи в блог',
                    'route' => 'blog_record_list'
                ]
            );
            $menu['blog']->addChild(
                'blogItemList', [
                    'label' => 'Изменения',
                    'route' => 'blog_item_list'
                ]
            );
        }
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
        $menu->addChild(
            'patients', [
                'label' => 'Управление пациентами',
            ]
        )->setAttribute('class', 'sublist');
        $menu['patients']->addChild(
            'patientsList', [
                'label' => 'Пациенты',
                'route' => 'patient_list'
            ]
        );
        $menu['patients']->addChild(
            'patientTestingList', [
                'label' => 'Сдача анализов',
                'route' => 'patient_testing_list'
            ]
        );
        $menu['patients']->addChild(
            'patientTestingResultList', [
                'label' => 'Результаты анализов',
                'route' => 'patient_testing_result_list'
            ]
        );
        $menu->addChild(
            'appointment', [
                'label' => 'Управление приемами пациентов',
            ]
        )->setAttribute('class', 'sublist');
        $menu['appointment']->addChild(
            'appointmentTypeList', [
                'label' => 'Виды приема',
                'route' => 'appointment_type_list'
            ]
        );
        $menu['appointment']->addChild(
            'appointmentList', [
                'label' => 'Приемы пациентов',
                'route' => 'patient_appointment_list'
            ]
        );
        $menu->addChild(
            'medicalHistories', [
                'label' => 'Управление историями болезни',
            ]
        )->setAttribute('class', 'sublist');
        $menu['medicalHistories']->addChild(
            'medicalHistoriesList', [
                'label' => 'Истории болезни',
                'route' => 'medical_history_list'
            ]
        );
        $menu['medicalHistories']->addChild(
            'medicalRecordsList', [
                'label' => 'Записи в историю болезни',
                'route' => 'medical_record_list'
            ]
        );
        $menu->addChild(
            'notification', [
                'label' => 'Управление уведомлениями',
            ]
        )->setAttribute('class', 'sublist');
        $menu['notification']->addChild(
            'notificationTypeList', [
                'label' => 'Виды уведомления',
                'route' => 'notification_type_list'
            ]
        );
        $menu['notification']->addChild(
            'notificationList', [
                'label' => 'Уведомления',
                'route' => 'notification_list'
            ]
        );
        $menu->addChild(
            'prescriptions', [
                'label' => 'Управление назначениями',
            ]
        )->setAttribute('class', 'sublist');
        $menu['prescriptions']->addChild(
            'prescriptionList', [
                'label' => 'Назначения',
                'route' => 'prescription_list'
            ]
        );
        $menu['prescriptions']->addChild(
            'prescriptionMedicineList', [
                'label' => 'Назначения лекарств',
                'route' => 'prescription_medicine_list'
            ]
        );
        $menu['prescriptions']->addChild(
            'prescriptionTestingList', [
                'label' => 'Назначения на обследование',
                'route' => 'prescription_testing_list'
            ]
        );
        $menu->addChild(
            'staffs', [
                'label' => 'Управление персоналом',
            ]
        )->setAttribute('class', 'sublist');
        $menu['staffs']->addChild(
            'staffList', [
                'label' => 'Сотрудники',
                'route' => 'staff_list'
            ]
        );
        $menu['staffs']->addChild(
            'positionList', [
                'label' => 'Должности',
                'route' => 'position_list'
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
        $menu->addChild(
            'analysis', [
                'label' => 'Управление анализами',
            ]
        )->setAttribute('class', 'sublist');
        $menu['analysis']->addChild(
            'analysisGroupList', [
                'label' => 'Группы анализов',
                'route' => 'analysis_group_list'
            ]
        );
        $menu['analysis']->addChild(
            'analysisList', [
                'label' => 'Анализы',
                'route' => 'analysis_list'
            ]
        );
        $menu['analysis']->addChild(
            'analysisRateList', [
                'label' => 'Референтные значения',
                'route' => 'analysis_rate_list'
            ]
        );
        $menu['analysis']->addChild(
            'planTestingList', [
                'label' => 'План анализов',
                'route' => 'plan_testing_list'
            ]
        );
        $menu['analysis']->addChild(
            'measureList', [
                'label' => 'Единицы измерения',
                'route' => 'measure_list'
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
    public function createDoctorOfficeHeaderMenu()
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'main-nav__list');
        $menu->addChild(
            'help', [
                'label' => 'Помощь',
                'route' => 'doctor_office_help'
            ]
        );
        return $menu;
    }

    /**
     * Меню кабинета врача в sidebar
     *
     * @return ItemInterface
     */
    public function createDoctorOfficeSidebarMenu()
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'sidebar__list');
        $menu->addChild(
            'patientsList', [
                'label' => 'Пациенты',
                'route' => 'patients_list'
            ]
        );
        return $menu;
    }
}
