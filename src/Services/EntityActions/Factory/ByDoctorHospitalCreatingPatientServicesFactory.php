<?php

namespace App\Services\EntityActions\Factory;

use App\Repository\PlanAppointmentRepository;
use App\Repository\PlanTestingRepository;
use App\Services\EntityActions\Creator\AuthUserCreatorService;
use App\Services\EntityActions\Creator\ByDoctorFirstPatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\ByDoctorHospitalPatientCreatorService;
use App\Services\EntityActions\Creator\ClinicalDiagnosisCreatorService;
use App\Services\EntityActions\Creator\MedicalHistoryCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class ByDoctorHospitalCreatingPatientServicesFactory
 * @package App\Services\EntityActions\Factory
 */
class ByDoctorHospitalCreatingPatientServicesFactory extends AbstractCreatingPatientServicesFactory
{
    /**
     * ByDoctorHospitalCreatingPatientServicesFactory constructor.
     * @param EntityManagerInterface $entityManager
     * @param AuthUserCreatorService $authUserCreator
     * @param ByDoctorHospitalPatientCreatorService $patientCreator
     * @param MedicalHistoryCreatorService $medicalHistoryCreator
     * @param ByDoctorFirstPatientAppointmentCreatorService $patientAppointmentCreator
     * @param PlanTestingRepository $planTestingRepository
     * @param PlanAppointmentRepository $planAppointmentRepository
     * @param ClinicalDiagnosisCreatorService $clinicalDiagnosisCreator
     * @param $roles
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AuthUserCreatorService $authUserCreator,
        ByDoctorHospitalPatientCreatorService $patientCreator,
        MedicalHistoryCreatorService $medicalHistoryCreator,
        ByDoctorFirstPatientAppointmentCreatorService $patientAppointmentCreator,
        PlanTestingRepository $planTestingRepository,
        PlanAppointmentRepository $planAppointmentRepository,
        ClinicalDiagnosisCreatorService $clinicalDiagnosisCreator,
        $roles
    )
    {
        parent::__construct(
            $entityManager,
            $authUserCreator,
            $patientCreator,
            $medicalHistoryCreator,
            $patientAppointmentCreator,
            $planTestingRepository,
            $planAppointmentRepository,
            $clinicalDiagnosisCreator,
            $roles
        );
    }
}