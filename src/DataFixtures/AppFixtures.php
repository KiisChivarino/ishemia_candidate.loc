<?php

namespace App\DataFixtures;

use App\AppBundle\DataSowing\DataSowing;
use App\Entity\Analysis;
use App\Entity\AnalysisGroup;
use App\Entity\AnalysisRate;
use App\Entity\AppointmentType;
use App\Entity\ChannelType;
use App\Entity\DateInterval;
use App\Entity\Diagnosis;
use App\Entity\Gender;
use App\Entity\Logger\LogAction;
use App\Entity\LPU;
use App\Entity\City;
use App\Entity\Measure;
use App\Entity\NotificationReceiverType;
use App\Entity\NotificationTemplate;
use App\Entity\NotificationTemplateText;
use App\Entity\OKSM;
use App\Entity\Oktmo;
use App\Entity\PlanAppointment;
use App\Entity\PlanTesting;
use App\Entity\Position;
use App\Entity\Region;
use App\Entity\Country;
use App\Entity\AuthUser;
use App\Entity\District;
use App\Entity\Hospital;
use App\Entity\Staff;
use App\Entity\StartingPoint;
use App\Entity\TemplateParameter;
use App\Entity\TemplateParameterText;
use App\Entity\TemplateType;
use App\Entity\TimeRange;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\ORMException;

class AppFixtures extends Fixture
{
    /** @var string PATH_TO_CSV */
    const PATH_TO_CSV = 'data/AppFixtures/';

    /** @var DataSowing $dataSowing */
    private $dataSowing;

    /**
     * AppFixtures constructor.
     * @param DataSowing $dataSowing
     */
    public function __construct(DataSowing $dataSowing)
    {
        $this->dataSowing = $dataSowing;
    }

    /**
     * @param ObjectManager $manager
     * @throws ORMException
     */
    public function load(ObjectManager $manager)
    {
        /** begin Должности */
        echo "Заполнение справочника \"Должности\"\n";
        $this->dataSowing->setEntitiesFromCsv($manager,self::PATH_TO_CSV . 'position.csv', Position::class, '|', [], ['enabled' => true]);
        /** end Должности */

        /** begin Виды приема */
        echo "Заполнение справочника \"Вид приема\"\n";
        $this->dataSowing->setEntitiesFromCsv($manager,self::PATH_TO_CSV . 'appointment_type.csv', AppointmentType::class, '|', [], ['enabled' => true]);
        /** end Виды приема */

        /** begin Пользователи */
        echo "Добавление пользователей\n";
        $manager->getRepository(AuthUser::class)->addUserFromFixtures('9999999999', 'System', 'System', 'ROLE_SYSTEM', '111111', true);
        $manager->getRepository(AuthUser::class)->addUserFromFixtures('8888888888', 'Admin', 'Admin', 'ROLE_ADMIN', '111111', true);
        /** @var Position $positionDoctor */
        $positionDoctor = $manager->getRepository(Position::class)->findOneBy(['name' => 'Врач']);
        $manager->getRepository(Staff::class)->addStaffFromFixtures('0000000000', 'Максим', 'Хруслов', 'ROLE_DOCTOR_CONSULTANT', '111111', true, $positionDoctor);
        /** end Пользователи */

        /** begin Точки отсчета */
        echo "Добавление точек отсчета\n";
        $manager->getRepository(StartingPoint::class)->addStartingPointFromFixtures(1, 'dateBegin', 'Включение в историю болезни');
        $manager->getRepository(StartingPoint::class)->addStartingPointFromFixtures(2, 'heartAttackDate', 'Дата возникновения инфаркта');
        /** end Точки отсчета */

        /** begin Пол */
        echo "Добавление пола\n";
        $manager->getRepository(Gender::class)->addGenderFromFixtures('м', 'мужчина');
        $manager->flush();
        $manager->getRepository(Gender::class)->addGenderFromFixtures('ж', 'женщина');
        $manager->flush();
        $manager->getRepository(Gender::class)->addGenderFromFixtures('н', 'не важен');
        /** end Пол */

        /** begin Роли*/
        echo "Внесение ролей\n";
        $this->dataSowing->addRoles();
        /** end Роли*/

        /** begin Типы каналов*/
        echo "Внесение типов каналов\n";
        $this->dataSowing->addChannelTypes();
        /** end Типы каналов*/

        /** begin Типы получателей уведомлений */
        echo "Внесение типов получателей уведомлений\n";
        $this->dataSowing->addReceiverTypes();
        /** end Типы получателей уведомлений*/

        /** begin OKSM */
        echo "Заполнение справочника ОКСМ\n";
        $this->dataSowing->setEntitiesFromCsv($manager, self::PATH_TO_CSV . 'OKSM.csv', OKSM::class);
        /** end OKSM */

        /** begin Страна */
        echo "Добавление России\n";
        $this->dataSowing->addEntitiesFromCatalog(
            $manager->getRepository(OKSM::class)->getRussiaCountry(),
            Country::class,
            [
                'name' => 'caption',
                'shortCode' => 'A3',
                'enabled' => true,
            ]
        );
        $manager->flush();
        /** end Страна */

        /** begin Регионы */
        echo "Заполнение справочника регионов\n";
        $russia = $manager->getRepository(Country::class)->getRussiaCountry();
        $this->dataSowing->setEntitiesFromCsv(
            $manager, self::PATH_TO_CSV . 'regions.csv', Region::class, ';',
            [
                'code' => 'regionNumber',
                'oktmo_region_id' => null,
                'OKTMO_ID' => 'oktmoRegionId',
                'FederalDistrictID' => null,
                'FederalDistrictName' => null,
            ],
            [
                'country' => $russia,
                'enabled' => true,
            ]
        );
        $manager->flush();
        /** end Регионы */

        /** begin Адреса */
        //todo Изменить запрос с использованием "in"
        echo "Заполнение справочника адресов\n";
        $this->dataSowing->setEntitiesFromCsv($manager, self::PATH_TO_CSV . 'Oktmo.csv', Oktmo::class, ';', ['ID' => null]);
        /** end Адреса */

        /** begin Районы */
        echo "Заполнение справочника районов\n";
        $kurskRegion = $manager->getRepository(Region::class)->getKurskRegion();
        $this->dataSowing->addEntitiesFromCatalog(
            $manager->getRepository(Oktmo::class)->getKurskRegionDistricts(),
            District::class,
            [
                'name' => 'name',
                'region' => $kurskRegion,
                'oktmo' => Oktmo::class,
                'enabled' => true,
            ]
        );
        /** end Районы */

        // /** begin Города */
        echo "Добавление городов по Курской области\n";
        $this->dataSowing->addEntitiesFromCatalog(
            $manager->getRepository(Oktmo::class)->getKurskRegionCities(),
            City::class,
            [
                'region' => $kurskRegion,
                'name' => 'name',
                'enabled' => true,
                'oktmo' => Oktmo::class,
            ]
        );
        /** end Города */

        /** begin LPU */
        echo "Заполнение справочника ЛПУ\n";
        $this->dataSowing->setEntitiesFromCsv($manager, self::PATH_TO_CSV . 'LPU.csv', LPU::class, '|');
        $manager->flush();
        /** end LPU */

        /** begin Больницы */
        echo "Добавление ЛПУ по Курской области\n";
        $this->dataSowing->addEntitiesFromCatalog(
            $manager->getRepository(LPU::class)->getKurskRegionLPU(),
            Hospital::class,
            [
                'region' => $kurskRegion,
                'code' => 'code',
                'name' => 'caption',
                'description' => 'fullName',
                'address' => 'address',
                'phone' => 'phone',
                'email' => 'email',
                'lpu' => LPU::class,
                'enabled' => false,
            ]
        );
        $manager->flush();
        /** end Больницы */

        /** begin Патологии (диагнозы) */
        echo "Добавление патологий (диагнозов)\n";
        $this->dataSowing->setEntitiesFromCsv($manager, self::PATH_TO_CSV . 'mkb10.csv', Diagnosis::class, '|', [], ['enabled' => true]);
        /** end Патологии */

        /** begin Группы анализов (тестирования) */
        echo "Заполнение справочника \"Группы анализов\"\n";
        $this->dataSowing->setEntitiesFromCsv($manager, self::PATH_TO_CSV . 'analysis_group.csv', AnalysisGroup::class, '|', [], ['enabled' => true]);
        /** end Группы анализов (тестирования) */

        /** begin Интервал даты */
        echo "Заполнение справочника \"Интервал даты\"\n";
        $this->dataSowing->setEntitiesFromCsv($manager, self::PATH_TO_CSV . 'date_interval.csv', DateInterval::class, '|');
        /** end Интервал даты */

        /** begin Временной диапазон */
        echo "Заполнение справочника \"Временной диапазон\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager, self::PATH_TO_CSV . 'time_range.csv', TimeRange::class, '|', [], [], [
                'dateInterval' => DateInterval::class,
            ]
        );
        /** end Временной диапазон */

        /** begin Стандартный план тестирования */
        echo "Заполнение справочника \"Стандартный план тестирования\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager,
            self::PATH_TO_CSV . 'plan_testing.csv',
            PlanTesting::class,
            '|',
            [],
            ['enabled' => true],
            [
                'analysisGroup' => AnalysisGroup::class,
                'timeRange' => TimeRange::class,
                'startingPoint' => StartingPoint::class,
            ]
        );
        /** end Стандартный план тестирования */

        /** begin Стандартный план приемов */
        echo "Заполнение справочника \"Стандартный план приемов\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager,
            self::PATH_TO_CSV . 'plan_appointment.csv',
            PlanAppointment::class,
            '|',
            [],
            ['enabled' => true],
            [
                'timeRange' => TimeRange::class,
                'startingPoint' => StartingPoint::class,
            ]
        );
        /** end Стандартный план приемов */

        /** begin Анализы */
        echo "Заполнение справочника \"Анализы\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager,
            self::PATH_TO_CSV . 'analysis.csv',
            Analysis::class, '|',
            ['enabled' => null],
            ['enabled' => true],
            ['analysisGroup' => AnalysisGroup::class]
        );
        /** end Анализы */

        /** begin Единицы измерения */
        echo "Заполнение справочника \"Единицы измерения\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager,
            self::PATH_TO_CSV . 'measure.csv',
            Measure::class,
            '|',
            [],
            ['enabled' => true]
        );
        /** end Единицы измерения */

        /** begin Референтные значения */
        echo "Заполнение справочника \"Референтные значения\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager,
            self::PATH_TO_CSV . 'analysis_rate.csv',
            AnalysisRate::class,
            '|',
            [
                'rate_min' => 'rateMin',
                'rate_max' => 'rateMax'
            ],
            ['enabled' => true],
            [
                'analysis' => Analysis::class,
                'measure' => Measure::class,
                'gender' => Gender::class,
            ]
        );
        /** end Референтные значения */

        /** begin Типы шаблонов */
        echo "Заполнение справочника \"Типы шаблонов\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager,
            self::PATH_TO_CSV . 'template_types.csv',
            TemplateType::class,
            ',',
            [],
            [
                'enabled' => true
            ]
        );
        /** end Типы шаблонов */

        /** begin Параметры шаблонов */
        echo "Заполнение справочника \"Параметры шаблонов\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager,
            self::PATH_TO_CSV . 'template_parameters.csv',
            TemplateParameter::class,
            ',',
            [],
            [
                'enabled' => true
            ],
            [
                'templateType' => TemplateType::class
            ]
        );
        /** end Параметры шаблонов */

        /** begin Тексты параметров шаблонов */
        echo "Заполнение справочника \"Тексты параметров шаблонов\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager,
            self::PATH_TO_CSV . 'template_parameters_texts.csv',
            TemplateParameterText::class,
            ';',
            [],
            [
                'enabled' => true
            ],
            [
                'templateParameter' => TemplateParameter::class
            ]
        );
        /** end Тексты параметров шаблонов */

        /** begin Типы логов */
        echo "Заполнение \"Типов логов\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager,
            self::PATH_TO_CSV . 'log_actions.csv',
            LogAction::class,
            '|',
            [],
            ['enabled' => true]);
        /** end Типы логов */

        /** begin Шаблоны уведомлений */
        echo "Заполнение справочника \"Шаблоны уведомлений\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager,
            self::PATH_TO_CSV . 'notification_templates.csv',
            NotificationTemplate::class,
            ',',
            [],
            [],
            [
                'notificationReceiverType' => NotificationReceiverType::class
            ]
        );
        /** end Шаблоны уведомлений */

        /** begin Тексты шаблонов уведомлений */
        echo "Заполнение справочника \"Тексты шаблонов уведомлений\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager,
            self::PATH_TO_CSV . 'notification_template_texts.csv',
            NotificationTemplateText::class,
            ',',
            [],
            [],
            [
                'notificationTemplate' => NotificationTemplate::class,
                'channelType' => ChannelType::class,
            ]
        );
        /** end Тексты шаблонов уведомлений */
    }
}