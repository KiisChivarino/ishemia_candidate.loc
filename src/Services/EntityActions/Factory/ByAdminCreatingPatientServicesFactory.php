<?php

namespace App\Services\EntityActions\Factory;

use App\Repository\PlanAppointmentRepository;
use App\Repository\PlanTestingRepository;
use App\Services\EntityActions\Creator\AuthUserCreatorService;
use App\Services\EntityActions\Creator\ClinicalDiagnosisCreatorService;
use App\Services\EntityActions\Creator\FirstPatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\MedicalHistoryCreatorService;
use App\Services\EntityActions\Creator\PatientCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class ByAdminCreatingPatientServicesFactory
 * @package App\Services\EntityActions\Factory
 */
class ByAdminCreatingPatientServicesFactory extends AbstractCreatingPatientServicesFactory
{
    /**
     * ByAdminCreatingPatientServicesFactory constructor.
     * @param EntityManagerInterface $entityManager
     * @param AuthUserCreatorService $authUserCreator
     * @param PatientCreatorService $patientCreator
     * @param MedicalHistoryCreatorService $medicalHistoryCreator
     * @param FirstPatientAppointmentCreatorService $patientAppointmentCreator
     * @param PlanTestingRepository $planTestingRepository
     * @param PlanAppointmentRepository $planAppointmentRepository
     * @param ClinicalDiagnosisCreatorService $clinicalDiagnosisCreator
     * @param $roles
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AuthUserCreatorService $authUserCreator,
        PatientCreatorService $patientCreator,
        MedicalHistoryCreatorService $medicalHistoryCreator,
        FirstPatientAppointmentCreatorService $patientAppointmentCreator,
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

        $this->createPatient();
    }
}