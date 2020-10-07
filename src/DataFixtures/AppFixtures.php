<?php

namespace App\DataFixtures;

use App\AppBundle\DataSowing\DataSowing;
use App\Entity\Analysis;
use App\Entity\AnalysisGroup;
use App\Entity\AnalysisRate;
use App\Entity\Diagnosis;
use App\Entity\Gender;
use App\Entity\LPU;
use App\Entity\City;
use App\Entity\Measure;
use App\Entity\OKSM;
use App\Entity\Oktmo;
use App\Entity\Region;
use App\Entity\Country;
use App\Entity\AuthUser;
use App\Entity\District;
use App\Entity\Hospital;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    const PATH_TO_CSV = 'data/AppFixtures/';

    private $dataSowing;

    public function __construct(DataSowing $dataSowing)
    {
        $this->dataSowing = $dataSowing;
    }

    public function load(ObjectManager $manager)
    {
        /** begin Админ */
        echo "Добавление админа\n";
        $manager->getRepository(AuthUser::class)->addUserFromFixtures('8888888888', 'Admin', 'Admin', 'ROLE_ADMIN', '111111', true);
        $manager->getRepository(AuthUser::class)->addUserFromFixtures('0000000000', 'DoctorFirstName', 'DoctorLastName', 'ROLE_DOCTOR_CONSULTANT', '111111', true);
        /** end Админ */

        /** begin Пол */
        echo "Добавление пола\n";
        $manager->getRepository(Gender::class)->addGenderFromFixtures('м', 'мужчина');
        $manager->flush();
        $manager->getRepository(Gender::class)->addGenderFromFixtures('ж', 'женщина');
        $manager->flush();
        $manager->getRepository(Gender::class)->addGenderFromFixtures('о', 'отсутствует');
        /** end Пол */

        /** begin Роли*/
        echo "Внесение ролей\n";
        $this->dataSowing->addRoles($manager);
        /** end Роли*/

        /** begin OKSM */
        echo "Заполнение справочника ОКСМ\n";
        $this->dataSowing->setEntitiesFromCsv($manager, self::PATH_TO_CSV.'OKSM.csv', OKSM::class, ';');
        /** end OKSM */

        /** begin Страна */
        echo "Добавление России\n";
        $this->dataSowing->addEntitiesFromCatalog(
            $manager,
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
            $manager, self::PATH_TO_CSV.'regions.csv', Region::class, ';',
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
        $this->dataSowing->setEntitiesFromCsv($manager, self::PATH_TO_CSV.'Oktmo.csv', Oktmo::class, ';', ['ID' => null]);
        /** end Адреса */

        /** begin Районы */
        echo "Заполнение справочника районов\n";
        $kurskRegion = $manager->getRepository(Region::class)->getKurskRegion();
        $this->dataSowing->addEntitiesFromCatalog(
            $manager,
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
            $manager,
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
        $this->dataSowing->setEntitiesFromCsv($manager, self::PATH_TO_CSV.'LPU.csv', LPU::class, '|');
        $manager->flush();
        /** end LPU */

        /** begin Больницы */
        echo "Добавление ЛПУ по Курской области\n";
        $this->dataSowing->addEntitiesFromCatalog(
            $manager,
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
        $this->dataSowing->setEntitiesFromCsv($manager, self::PATH_TO_CSV.'mkb10.csv', Diagnosis::class, '|', [], ['enabled' => true]);
        /** end Патологии */

        /** begin Группы анализов (тестирования) */
        echo "Заполнение справочника \"Группы анализов\"\n";
        $this->dataSowing->setEntitiesFromCsv($manager, self::PATH_TO_CSV.'analysis_group.csv', AnalysisGroup::class, '|', [], ['enabled' => true]);
        /** end Группы анализов (тестирования) */

        /** begin Стандартный план анализвов (тестирования)*/
//        echo "Заполнение справочника \"Стандартный план тестирования\"\n";
//        $this->dataSowing->setEntitiesFromCsv(
//            $manager,
//            self::PATH_TO_CSV.'plan_testing.csv',
//            PlanTesting::class,
//            '|',
//            [],
//            ['enabled' => true],
//            ['analysisGroup' => AnalysisGroup::class]
//        );
        /** end Стандартный план анализвов (тестирования) */

        /** begin Периоды*/
//        echo "Заполнение справочника \"Периоды\"\n";
//        $this->dataSowing->setEntitiesFromCsv($manager, self::PATH_TO_CSV.'period.csv', Period::class, '|', [], ['enabled' => true]);
        /** end Периоды */

        /** begin Анализы */
        echo "Заполнение справочника \"Анализы\"\n";
        $this->dataSowing->setEntitiesFromCsv(
            $manager,
            self::PATH_TO_CSV.'analysis.csv',
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
            self::PATH_TO_CSV.'measure.csv',
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
            self::PATH_TO_CSV.'analysis_rate.csv',
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
    }
}