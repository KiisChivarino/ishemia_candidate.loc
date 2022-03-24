<?php

namespace App\Services\EntityActions\Factory;

use App\Entity\City;
use App\Entity\Hospital;
use App\Repository\PlanAppointmentRepository;
use App\Repository\PlanTestingRepository;
use App\Services\EntityActions\Creator\FirstPatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\Fixtures\FixturesAuthUserCreatorService;
use App\Services\EntityActions\Creator\Fixtures\FixturesClinicalDiagnosisCreatorService;
use App\Services\EntityActions\Creator\Fixtures\FixturesMedicalHistoryCreatorService;
use App\Services\EntityActions\Creator\Fixtures\FixturesPatientCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class FixturesCreatingPatientServicesFactory
 * @package App\Services\EntityActions\Factory
 */
class FixturesCreatingPatientServicesFactory extends AbstractCreatingPatientServicesFactory
{
    /**
     * FixturesCreatingPatientServicesFactory constructor.
     *
     * @param EntityManagerInterface                  $entityManager
     * @param FixturesAuthUserCreatorService          $fixturesAuthUserCreatorService
     * @param FixturesPatientCreatorService           $fixturesPatientCreatorService
     * @param FixturesMedicalHistoryCreatorService    $fixturesMedicalHistoryCreator
     * @param FirstPatientAppointmentCreatorService   $patientAppointmentCreator
     * @param PlanTestingRepository                   $planTestingRepository
     * @param PlanAppointmentRepository               $planAppointmentRepository
     * @param FixturesClinicalDiagnosisCreatorService $fixturesClinicalDiagnosisCreatorService
     * @param                                         $roles
     *
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FixturesAuthUserCreatorService $fixturesAuthUserCreatorService,
        FixturesPatientCreatorService $fixturesPatientCreatorService,
        FixturesMedicalHistoryCreatorService $fixturesMedicalHistoryCreator,
        FirstPatientAppointmentCreatorService $patientAppointmentCreator,
        PlanTestingRepository $planTestingRepository,
        PlanAppointmentRepository $planAppointmentRepository,
        FixturesClinicalDiagnosisCreatorService $fixturesClinicalDiagnosisCreatorService,
        $roles
    ) {
        parent::__construct(
            $entityManager,
            $fixturesAuthUserCreatorService,
            $fixturesPatientCreatorService,
            $fixturesMedicalHistoryCreator,
            $patientAppointmentCreator,
            $planTestingRepository,
            $planAppointmentRepository,
            $fixturesClinicalDiagnosisCreatorService,
            $roles
        );
    }

    /**
     * Saving patient creation data when loading fixtures
     *
     * @throws Exception
     */
    public function afterCreatePatient(
        string $password,
        string $firstName,
        string $lastName,
        string $phone,
        string $address,
        string $hospitalName,
        string $cityName,
        \DateTime $birthDate,
        \DateTime $heartAttackDate,
        string $clinicalDiagnosisDescription,
        string $clinicalDiagnosisMkbCode
    ): void {
        $this->getAuthUserCreator()->after(
            [
                FixturesAuthUserCreatorService::PASSWORD_OPTION   => $password,
                FixturesAuthUserCreatorService::FIRST_NAME_OPTION => $firstName,
                FixturesAuthUserCreatorService::LAST_NAME_OPTION  => $lastName,
                FixturesAuthUserCreatorService::PHONE_OPTION      => $phone,

            ]
        );
        $hospital =
            $this->entityManager->getRepository(Hospital::class)->findOneBy(
                [
                    'name' => $hospitalName
                ]
            );
        $city =
            $this->entityManager->getRepository(City::class)->findOneBy(
                [
                    'name' => $cityName
                ]
            );
        $this->getPatientCreator()->after(
            [
                FixturesPatientCreatorService::ADDRESS_OPTION           => $address,
                FixturesPatientCreatorService::HOSPITAL_OPTION          => $hospital,
                FixturesPatientCreatorService::CITY_OPTION              => $city,
                FixturesPatientCreatorService::BIRTH_DATE_OPTION        => $birthDate,
                FixturesPatientCreatorService::HEART_ATTACK_DATE_OPTION => $heartAttackDate,
            ]
        );
        $this->getClinicalDiagnosisCreator()->after(
            [
                FixturesClinicalDiagnosisCreatorService::DESCRIPTION_OPTION => $clinicalDiagnosisDescription,
                FixturesClinicalDiagnosisCreatorService::MKB_CODE_OPTION => $clinicalDiagnosisMkbCode
            ]
        );
        $this->getMedicalHistoryCreator()->after();
        $this->getPatientAppointmentCreator()->after();
    }
}