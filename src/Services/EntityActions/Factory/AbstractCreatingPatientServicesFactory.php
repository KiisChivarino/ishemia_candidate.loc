<?php

namespace App\Services\EntityActions\Factory;

use App\Entity\Analysis;
use App\Entity\AuthUser;
use App\Entity\ClinicalDiagnosis;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\PatientDischargeEpicrisis;
use App\Entity\PatientTesting;
use App\Entity\PlanTesting;
use App\Repository\PlanAppointmentRepository;
use App\Repository\PlanTestingRepository;
use App\Services\EntityActions\Creator\AuthUserCreatorService;
use App\Services\EntityActions\Creator\ClinicalDiagnosisCreatorService;
use App\Services\EntityActions\Creator\FirstPatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\FirstPatientTestingCreatorService;
use App\Services\EntityActions\Creator\MedicalHistoryCreatorService;
use App\Services\EntityActions\Creator\PatientAppointmentCreatorService;
use App\Services\EntityActions\Creator\PatientCreatorService;
use App\Services\EntityActions\Creator\PatientTestingCreatorService;
use App\Services\EntityActions\Creator\PatientTestingResultsCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Class AbstractCreatingPatientServicesFactory
 * @package App\Services\EntityActions\Factory
 */
abstract class AbstractCreatingPatientServicesFactory
{
    /**
     * @var array
     * yaml:config/services/roles.yml
     */
    private static $ROLES;

    /**
     * @var AuthUserCreatorService
     */
    private $authUserCreator;

    /**
     * @var PatientCreatorService
     */
    private $patientCreator;

    /**
     * @var ClinicalDiagnosisCreatorService
     */
    private $clinicalDiagnosisCreator;

    /**
     * @var MedicalHistoryCreatorService
     */
    private $medicalHistoryCreator;

    /**
     * @var PatientAppointmentCreatorService
     */
    private $patientAppointmentCreator;

    /**
     * AbstractCreatingPatientServicesFactory constructor.
     * @param EntityManagerInterface $entityManager
     * @param AuthUserCreatorService $authUserCreator
     * @param PatientCreatorService $patientCreator
     * @param MedicalHistoryCreatorService $medicalHistoryCreator
     * @param FirstPatientAppointmentCreatorService $patientAppointmentCreator
     * @param PlanTestingRepository $planTestingRepository
     * @param PlanAppointmentRepository $planAppointmentRepository
     * @param ClinicalDiagnosisCreatorService $clinicalDiagnosisCreator
     * @param $roles
     * @throws NonUniqueResultException
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
        self::$ROLES = $roles;
        $this->authUserCreator = $authUserCreator->before(
            [
                AuthUserCreatorService::ROLE_OPTION => self::$ROLES[3]['techName'],
            ]
        );
        $this->patientCreator = $patientCreator->before(
            [
                PatientCreatorService::AUTH_USER_OPTION => $this->getAuthUser(),
            ]
        );
        $this->clinicalDiagnosisCreator = $clinicalDiagnosisCreator->execute();
        $this->medicalHistoryCreator = $medicalHistoryCreator->before(
            [
                MedicalHistoryCreatorService::PATIENT_OPTION => $this->getPatient(),
                MedicalHistoryCreatorService::CLINICAL_DIAGNOSIS_OPTION => $this->getClinicalDiagnosis(),
                MedicalHistoryCreatorService::DISCHARGE_EPICRISIS_OPTION => new PatientDischargeEpicrisis(),
            ]
        );
        $this->patientAppointmentCreator = $patientAppointmentCreator->before(
            [
                PatientAppointmentCreatorService::MEDICAL_HISTORY_OPTION => $this->getMedicalHistory(),
                FirstPatientAppointmentCreatorService::FIRST_APPOINTMENT_PLAN_OPTION =>
                    $planAppointmentRepository->getPlanOfFirstAppointment(),
            ]
        );
        /** @var PlanTesting $planTesting */
        foreach ($planTestingRepository->getPlanOfFirstTestings() as $planTesting) {
            $patientTestingCreator = (new FirstPatientTestingCreatorService($entityManager))->execute(
                [
                    PatientTestingCreatorService::MEDICAL_HISTORY_OPTION => $this->getMedicalHistory(),
                    FirstPatientTestingCreatorService::PLAN_TESTING_OPTION => $planTesting,
                ]
            );
            /** @var PatientTesting $patientTesting */
            $patientTesting = $patientTestingCreator->getEntity();
            /** @var Analysis $analysis */
            foreach ($patientTesting->getAnalysisGroup()->getAnalyses() as $analysis) {
                (new PatientTestingResultsCreatorService($entityManager))->execute(
                    [
                        PatientTestingResultsCreatorService::ANALYSIS_OPTION => $analysis,
                        PatientTestingResultsCreatorService::PATIENT_TESTING_OPTION => $patientTesting,
                    ]
                );
            }
        }
    }

    /**
     * @return ClinicalDiagnosisCreatorService
     */
    public function getClinicalDiagnosisCreator(): ClinicalDiagnosisCreatorService
    {
        return $this->clinicalDiagnosisCreator;
    }

    /**
     * @return AuthUserCreatorService
     */
    public function getAuthUserCreator(): AuthUserCreatorService
    {
        return $this->authUserCreator;
    }

    /**
     * @return PatientCreatorService
     */
    public function getPatientCreator(): PatientCreatorService
    {
        return $this->patientCreator;
    }

    /**
     * @return MedicalHistoryCreatorService
     */
    public function getMedicalHistoryCreator(): MedicalHistoryCreatorService
    {
        return $this->medicalHistoryCreator;
    }

    /**
     * @return PatientAppointmentCreatorService
     */
    public function getPatientAppointmentCreator(): PatientAppointmentCreatorService
    {
        return $this->patientAppointmentCreator;
    }

    /**
     * @return AuthUser
     */
    public function getAuthUser(): AuthUser
    {
        return $this->getAuthUserCreator()->getEntity();
    }

    /**
     * @return Patient
     */
    public function getPatient(): Patient
    {
        return $this->getPatientCreator()->getEntity();
    }

    /**
     * @return ClinicalDiagnosis
     */
    public function getClinicalDiagnosis(): ClinicalDiagnosis
    {
        return $this->getClinicalDiagnosisCreator()->getEntity();
    }

    /**
     * @return MedicalHistory
     */
    public function getMedicalHistory(): MedicalHistory
    {
        return $this->getMedicalHistoryCreator()->getEntity();
    }

    /**
     * @return PatientAppointment
     */
    public function getPatientAppointment(): PatientAppointment
    {
        return $this->getPatientAppointmentCreator()->getEntity();
    }
}