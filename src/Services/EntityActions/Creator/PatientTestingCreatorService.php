<?php

namespace App\Services\Creator;

use App\Entity\MedicalHistory;
use App\Entity\PatientTesting;
use App\Entity\PlanTesting;
use App\Entity\Staff;
use App\Repository\PlanTestingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class CreatingPatientTestingService
 * @package App\Services\CreatingPatientTesting
 */
class PatientTestingCreatorService
{
    /** @var PlanTestingRepository $planTestingRepository */
    private $planTestingRepository;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var PatientTestingResultsCreatorService $patientTestingResultsCreator */
    private $patientTestingResultsCreator;

    /** @var PrescriptionCreatorService $prescriptionCreator */
    private $prescriptionCreator;

    /** @var PrescriptionTestingCreatorService $prescriptionTestingCreator */
    private $prescriptionTestingCreator;

    /**
     * PatientTestingCreatorService constructor.
     * @param PlanTestingRepository $planTestingRepository
     * @param EntityManagerInterface $entityManager
     * @param PatientTestingResultsCreatorService $patientTestingResultsCreator
     * @param PrescriptionCreatorService $prescriptionCreator
     * @param PrescriptionTestingCreatorService $prescriptionTestingCreator
     */
    public function __construct(
        PlanTestingRepository $planTestingRepository,
        EntityManagerInterface $entityManager,
        PatientTestingResultsCreatorService $patientTestingResultsCreator,
        PrescriptionCreatorService $prescriptionCreator,
        PrescriptionTestingCreatorService $prescriptionTestingCreator
    )
    {
        $this->planTestingRepository = $planTestingRepository;
        $this->entityManager = $entityManager;
        $this->patientTestingResultsCreator = $patientTestingResultsCreator;
        $this->prescriptionCreator = $prescriptionCreator;
        $this->prescriptionTestingCreator = $prescriptionTestingCreator;
    }

    /**
     * Create Patient testing entity object
     * @param MedicalHistory $medicalHistory
     * @param PlanTesting|null $planTesting
     * @param bool $isFirst
     * @return PatientTesting
     */
    public function createPatientTesting(
        MedicalHistory $medicalHistory,
        PlanTesting $planTesting = null,
        bool $isFirst = false
    ): PatientTesting
    {
        return (new PatientTesting())
            ->setMedicalHistory($medicalHistory)
            ->setAnalysisGroup($planTesting->getAnalysisGroup())
            ->setProcessed(false)
            ->setEnabled(true)
            ->setAnalysisDate(null)
            ->setIsFirst($isFirst)
            ->setIsByPlan(false)
            ->setPlanTesting($planTesting);
    }

    /**
     * Persist first patient tests by plan
     * @param MedicalHistory $medicalHistory
     * @return array
     */
    public function persistFirstPatientTestsByPlan(MedicalHistory $medicalHistory): array
    {
        $patientTests = [];
        /** @var PlanTesting $planTesting */
        foreach ($this->planTestingRepository->getPlanOfFirstTestings() as $planTesting) {
            $patientTesting = $this->createPatientTesting($medicalHistory, $planTesting, true);
            $this->preparePatientTestingByPlan($patientTesting);
            $this->entityManager->persist($patientTesting);
            $this->patientTestingResultsCreator->persistTestingResultsForTesting($patientTesting);
            $patientTests[] = $patientTesting;
        }
        return $patientTests;
    }

    /**
     * Persist patient tests by plan
     * @param MedicalHistory $medicalHistory
     * @param Staff $staff
     * @return array
     * @throws Exception
     */
    public function persistPatientTestsByPlan(MedicalHistory $medicalHistory, Staff $staff): array
    {
        $patientTests = [];
        /** @var PlanTesting $test */
        foreach ($this->planTestingRepository->getStandardPlanTesting() as $planTesting) {
            $patientTesting = $this->createPatientTesting($medicalHistory, $planTesting);
            $this->preparePatientTestingByPlan($patientTesting);
            $patientTests[] =
                $this->persistPatientTesting(
                    $medicalHistory,
                    $planTesting,
                    $staff,
                    $patientTesting
                );
        }
        return $patientTests;
    }

    /**
     * Persist patient testing
     * @param MedicalHistory $medicalHistory
     * @param PlanTesting $planTesting
     * @param Staff $staff
     * @param PatientTesting $patientTesting
     * @return PatientTesting
     * @throws Exception
     */
    public function persistPatientTesting(
        MedicalHistory $medicalHistory,
        PlanTesting $planTesting,
        Staff $staff,
        PatientTesting $patientTesting
    ): PatientTesting
    {
        $this->entityManager->persist($patientTesting);
        $this->patientTestingResultsCreator->persistTestingResultsForTesting($patientTesting);
        $prescription = $this->prescriptionCreator->createPrescription($medicalHistory, $staff);
        $this->entityManager->persist($prescription);
        $this->entityManager->persist(
            $this->prescriptionTestingCreator->createPrescriptionTesting(
                $prescription,
                $staff,
                $patientTesting,
                $planTesting
            )
        );
        return $patientTesting;
    }

    /**
     * Check patient testing for regular
     * @param PatientTesting $patientTesting
     * @return bool
     */
    public function checkPatientTestingForRegular(PatientTesting $patientTesting): bool
    {
        return $patientTesting->getProcessed()
            && $patientTesting->getIsByPlan()
            && $patientTesting->getPlanTesting()->getTimeRange()->getIsRegular();
    }

    /**
     * Check patient testing for regular and if regular create new regular patient testing
     * @param PatientTesting $patientTesting
     * @throws Exception
     */
    public function checkAndPersistRegularPatientTesting(PatientTesting $patientTesting): void
    {
        if ($this->checkPatientTestingForRegular($patientTesting)) {
            $newPatientTesting = $this->createPatientTesting(
                $patientTesting->getMedicalHistory(),
                $patientTesting->getPlanTesting()
            );
            $this->preparePatientTestingByPlan($newPatientTesting);
            $this->persistPatientTesting(
                $patientTesting->getMedicalHistory(),
                $patientTesting->getPlanTesting(),
                $patientTesting->getPrescriptionTesting()->getStaff(),
                $newPatientTesting
            );
        }
    }

    /**
     * @param PatientTesting $patientTesting
     * @return PatientTesting
     */
    public function preparePatientTestingByPlan(PatientTesting $patientTesting): PatientTesting
    {
        return $patientTesting->setIsByPlan(true);
    }
}