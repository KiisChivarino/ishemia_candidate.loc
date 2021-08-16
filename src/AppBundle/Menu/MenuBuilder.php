<?php

namespace App\AppBundle\Menu;

use App\Entity\Patient;
use App\Entity\PatientSMS;
use App\Entity\Staff;
use App\Repository\PatientTestingCounterRepository;
use App\Repository\PrescriptionRepository;
use App\Services\InfoService\AuthUserInfoService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class MenuBuilder
 *
 * @package App\AppBundle\Menu
 */
class MenuBuilder
{
    /** @var string Name of patient get parameter */
    private const PATIENT_GET_PARAMETER_NAME = 'id';

    /** @var FactoryInterface $factory */
    private $factory;

    /** @var ContainerInterface $container */
    private $container;

    /** @var Security $security */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /** @var string Параметр запроса */
    const PATIENT_QUERY_PARAMETER = 'id';

    /**
     * @param FactoryInterface $factory
     * @param ContainerInterface $container
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        FactoryInterface $factory,
        ContainerInterface $container,
        Security $security,
        EntityManagerInterface $entityManager
    )
    {
        $this->factory = $factory;
        $this->container = $container;
        $this->security = $security;
        $this->entityManager = $entityManager;
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
        $menu->setAttribute('templateName', 'admin_knp_menu.html.twig');
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
            'patientMedicineList', [
                'label' => 'Лекарства пациентов',
                'route' => 'patient_medicine_list'
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
                'route' => 'prescription_medicine_list',
            ]
        );
        $menu['medicalHistory']->addChild(
            'prescriptionTestingList', [
                'label' => 'Назначения на обследование',
                'route' => 'admin_prescription_testing_list'
            ]
        );
        $menu['medicalHistory']->addChild(
            'prescriptionAppointmentList', [
                'label' => 'Назначения на прием',
                'route' => 'prescription_appointment_list'
            ]
        );


        //Медицинские справочники
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
            'diagnosisList', [
                'label' => 'Клинические диагнозы',
                'route' => 'clinical_diagnosis_list',
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
        $this->activateStoringSelectedMenuItem($menu, $requestStack);
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
        $menu->setAttribute('templateName', 'doctor_office_knp_menu.html.twig');
        $menu->setChildrenAttribute('class', 'main-nav__list');
        /** @var AuthUser $authUser */
        $authUser = $this->security->getUser();
        if (AuthUserInfoService::isDoctorHospital($authUser)) {
            $menu->addChild(
                'add_patient', [
                    'label' => 'Добавить пациента',
                    'route' => 'adding_patient_by_hospital_doctor',
                ]
            );
        }
        if (AuthUserInfoService::isDoctorConsultant($authUser)) {
            $menu->addChild(
                'add_patient', [
                    'label' => 'Добавить пациента',
                    'route' => 'adding_patient_by_doctor_consultant',
                ]
            );
        }
        $patientId = $this->getEntityId(self::PATIENT_QUERY_PARAMETER);
        if ($this->isMenuForEntity(Patient::class, self::PATIENT_GET_PARAMETER_NAME)) {
            $menu->addChild(
                'add_prescription', [
                    'label' => 'Добавить назначение',
                    'route' => 'adding_prescriprion_by_doctor',
                    'routeParameters' => [
                        self::PATIENT_GET_PARAMETER_NAME => $patientId
                    ]
                ]
            );
            $menu->addChild(
                'create_doctor_notification', [
                    'label' => 'Сообщение пациенту',
                    'route' => 'doctor_create_notification',
                    'routeParameters' => [
                        self::PATIENT_GET_PARAMETER_NAME => $patientId
                    ]
                ]
            );
        }
        return $menu;
    }

    /**
     * @return ItemInterface
     */
    public function createAdminHeaderMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'main-nav__list');
        $menu->setAttribute('templateName', 'admin_knp_menu.html.twig');
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
     * @param RequestStack $requestStack
     * @param PatientTestingCounterRepository $patientTestingCounterRepository
     * @param PrescriptionRepository $prescriptionRepository
     * @return ItemInterface
     */
    public function createDoctorOfficeSidebarMenu(
        RequestStack $requestStack,
        PatientTestingCounterRepository $patientTestingCounterRepository,
        PrescriptionRepository $prescriptionRepository
    ): ItemInterface
    {
        /** @var AuthUser $authUser */
        $authUser = $this->security->getUser();
        $hospital = AuthUserInfoService::isDoctorHospital($authUser)
            ? $this->entityManager->getRepository(Staff::class)->getStaff($authUser)->getHospital()
            : null;
        $menu = $this->factory->createItem('root');
        $menu->setAttribute('class', 'sidebar__list');
        $menu->setAttribute('templateName', 'doctor_office_knp_menu.html.twig');
        $patientsNoResultsTestingsCount = $patientTestingCounterRepository->getNoResultsTestingsCount($hospital);
        $patientsNoProcessedTestingsCount = $patientTestingCounterRepository->getNoProcessedTestingsCount($hospital);
        $patientsOpenedPrescriptionsCount = $prescriptionRepository->getOpenedPrescriptionsCount($hospital);
        $menu->addChild(
            'patientsList', [
                'label' => $this->getLabelWithNotificationNumber(
                    'Пациенты',
                    $patientsNoResultsTestingsCount
                    + $patientsNoProcessedTestingsCount
                    + $patientsOpenedPrescriptionsCount
                )
            ]
        )->setAttribute('class', 'sublist');
        $menu['patientsList']->addChild(
            'patientsList',
            [
                'label' => 'Все',
                'route' => 'patients_list'
            ]
        );
        $menu['patientsList']->addChild(
            'patientsWithNoResultsList', [
                'label' => $this->getLabelWithNotificationNumber(
                    'Без анализов',
                    $patientsNoResultsTestingsCount
                ),
                'route' => 'patients_with_no_results_list'
            ]
        );
        $menu['patientsList']->addChild(
            'patientsWithNoProcessedList', [
                'label' => $this->getLabelWithNotificationNumber(
                    'Обработать анализы',
                    $patientsNoProcessedTestingsCount
                ),
                'route' => 'patients_with_no_processed_list'
            ]
        );
        $menu['patientsList']->addChild(
            'patientsWithOpenedPrescriptionsList', [
                'label' => $this->getLabelWithNotificationNumber(
                    'Закрыть назначения',
                    $patientsOpenedPrescriptionsCount
                ),
                'route' => 'patients_with_opened_prescriptions_list'
            ]
        );
        $menu['patientsList']->addChild(
            'patientsWithProcessedResultsList', [
                'label' => 'Обработанные',
                'route' => 'patients_with_processed_results_list'
            ]
        );
        if ($this->isMenuForEntity(Patient::class, 'id')) {
            $patientId = $this->getEntityId(self::PATIENT_QUERY_PARAMETER);
            $noProcessedTestingsCounter = $patientTestingCounterRepository
                ->getNoProcessedTestingsCount($patientId);
            $plannedTestingsCounter = $patientTestingCounterRepository
                ->getPlannedTestingsCount($patientId);
            $overdueTestingsCounter = $patientTestingCounterRepository
                ->getOverdueTestingsCount($patientId);
            $menu->addChild(
                'patient', [
                    'label' => '<strong>' . AuthUserInfoService::getFIO(
                            $this->entityManager->getRepository(Patient::class)->find($patientId)->getAuthUser(),
                            true
                        ) . '</strong>',
                    'route' => 'doctor_medical_history',
                    'routeParameters' => ['id' => $patientId]
                ]
            );
            $menu->addChild(
                'patientTestings', [
                    'label' => $this->getLabelWithNotificationNumber(
                        'Обследования пациента',
                        $noProcessedTestingsCounter + $plannedTestingsCounter + $overdueTestingsCounter
                    )
                ]
            )->setAttribute('class', 'sublist');
            $menu['patientTestings']->addChild(
                'patient_testing_list', [
                    'label' => 'Все',
                    'route' => 'doctor_patient_testing_list',
                    'routeParameters' => ['id' => $patientId]
                ]
            );
            $menu['patientTestings']->addChild(
                'patient_testing_no_processed_list', [
                    'label' => $this->getLabelWithNotificationNumber(
                        'Обработать',
                        $noProcessedTestingsCounter
                    ),
                    'route' => 'doctor_patient_testing_not_processed_list',
                    'routeParameters' => ['id' => $patientId]
                ]
            );
            $menu['patientTestings']->addChild(
                'patient_testing_planned_list', [
                    'label' => $this->getLabelWithNotificationNumber(
                        'По плану',
                        $plannedTestingsCounter
                    ),
                    'route' => 'doctor_patient_testing_planned_list',
                    'routeParameters' => ['id' => $patientId]
                ]
            );
            $menu['patientTestings']->addChild(
                'patient_testing_overdue_list', [
                    'label' => $this->getLabelWithNotificationNumber(
                        'Просроченные',
                        $overdueTestingsCounter
                    ),
                    'route' => 'doctor_patient_testing_overdue_list',
                    'routeParameters' => ['id' => $patientId]
                ]
            );
            $menu['patientTestings']->addChild(
                'patient_testing_history_list', [
                    'label' => 'История',
                    'route' => 'doctor_patient_testing_history_list',
                    'routeParameters' => ['id' => $patientId]
                ]
            );
            $menu->addChild(
                'notifications_list', [
                    'label' => 'Уведомления пациенту',
                    'route' => 'notifications_list',
                    'routeParameters' => ['id' => $patientId]
                ]
            );
            $patientSmsCounter = $this->entityManager->getRepository(PatientSMS::class)
                ->getPatientSMSMenu($patientId);
            $menu->addChild(
                'patient_sms_list', [
                    'label' => $this->getLabelWithNotificationNumber(
                        'Сообщения от пациента',
                        $patientSmsCounter,
                        'patientSMSCount'
                    ),
                    'route' => 'received_sms_from_patient_list',
                    'routeParameters' => ['id' => $patientId]
                ]
            );
        }
        $menu->addChild(
            'doctor_office_notification_list', [
                'label' => 'Уведомления пациентам',
                'route' => 'doctor_office_notification_list'
            ]
        );
        $this->activateStoringSelectedMenuItem($menu, $requestStack);
        if (AuthUserInfoService::isDoctorConsultant($authUser)) {
            $menu->addChild(
                'hospitalsList', [
                    'label' => 'Больницы',
                    'route' => 'doctor_office_hospital_list'
                ]
            );
        }
        return $menu;
    }

    /**
     * Меню кабинета пациента
     *
     * @param RequestStack $requestStack
     * @return ItemInterface
     */
    public function createPatientOfficeSidebarMenu(
        RequestStack $requestStack
    ): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setAttribute('templateName', 'patient_office_knp_menu.html.twig');
        $menu->setAttribute('sidebar_disable', true);
        $menu->addChild(
            'patient_office_main', [
                'label' => 'Главная',
                'route' => 'patient_office_main'
            ]
        );
        $menu->addChild(
            'patient_office_prescription', [
                'label' => 'Назначения',
                'route' => 'patient_office_prescription'
            ]
        );
        $menu->addChild(
            'patient_office_notification_news', [
                'label' => 'Уведомления',
                'route' => 'patient_office_notification_news'
            ]
        );
        $menu->addChild(
            'patient_office_testing', [
                'label' => 'Обследования',
                'route' => 'patient_office_testing'
            ]
        );
        $menu->addChild(
            'patient_office_information', [
                'label' => 'Информация',
                'route' => 'patient_office_article'
            ]
        );
        $menu->addChild(
            'logout_from_app', [
                'label' => 'Выйти',
                'route' => 'logout_from_app'
            ]
        );
        $this->activateStoringSelectedMenuItemPatientOffice($menu, $requestStack);
        return $menu;
    }


    /**
     * @param string $label
     * @param int $number
     * @param string|null $customClasses
     * @return string
     */
    private function getLabelWithNotificationNumber(string $label, int $number, string $customClasses = ""): string
    {
        return $number
            ? $label . '<div class="notificationNumber ' . $customClasses . '">' . $number . '</div>'
            : $label;
    }

    /**
     * Checks if current page and menu item belongs to the entity object pages block
     * @param string $entityClass
     * @param string $GETParameterKey
     * @return bool
     */
    private function isMenuForEntity(string $entityClass, string $GETParameterKey): bool
    {
        $entityId = $this->getEntityId($GETParameterKey);
        if ($entityId == null) {
            return false;
        }
        $entity = $this->getEntityById($entityId, $entityClass);
        return ($entity && is_a($entity, $entityClass));
    }

    /**
     * Returns id of entity object by GET parameter
     * @param string $GETParameterKey
     * @return int|null
     */
    private function getEntityId(string $GETParameterKey): ?int
    {
        $entityId = $this->container->get('request_stack')->getCurrentRequest()->get($GETParameterKey);
        if (is_object($entityId)) {
            return $entityId->getId();
        }
        return $entityId;
    }

    /**
     * Returns entity object by id
     * @param int $entityId
     * @param string $entityClass
     * @return object|null
     */
    private function getEntityById(int $entityId, string $entityClass): ?object
    {
        return $this->entityManager->find($entityClass, $entityId);
    }

    /**
     * Activates storing the selected menu item
     * @param ItemInterface $menu
     * @param RequestStack $requestStack
     * @return void
     */
    private function activateStoringSelectedMenuItem(ItemInterface $menu, RequestStack $requestStack): void
    {
        foreach ($menu->getChildren() as $item) {
            foreach ($item->getChildren() as $childrenItem) {
                if (
                    $childrenItem->getUri() == $requestStack->getCurrentRequest()->getRequestUri()
                    || preg_replace(
                        '/\/new|\/\d+\/(edit|show)$/', // Вырезает с конца /число/(edit или show) или /new
                        "",
                        $requestStack->getCurrentRequest()->getRequestUri()
                    ) == $childrenItem->getUri()
                ) {
                    $childrenItem->setCurrent(true);
                }
            }
        }
    }

    /**
     * Activates storing the selected menu item
     * @param ItemInterface $menu
     * @param RequestStack $requestStack
     * @return void
     */
    private function activateStoringSelectedMenuItemPatientOffice(ItemInterface $menu, RequestStack $requestStack): void
    {
        foreach ($menu->getChildren() as $item) {
            $uri = str_replace('news', '', $item->getUri());
            $RequestUri = $requestStack->getCurrentRequest()->getRequestUri();
            if ($uri == $RequestUri
                || preg_replace(
                    '/(history|news|\d+)\/?(\?.+=.+)?(&.+=.+)*$/',
                    "",
                    $RequestUri
                ) == $uri
            ) {
                $item->setCurrent(true);
            }
        }
    }

}
