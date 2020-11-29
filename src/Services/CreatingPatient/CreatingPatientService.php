<?php

namespace App\Services\CreatingPatient;

use App\Entity\AuthUser;
use App\Entity\Diagnosis;
use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use App\Entity\PlanAppointment;
use App\Entity\PlanTesting;
use App\Entity\Prescription;
use App\Entity\PrescriptionAppointment;
use App\Entity\PrescriptionTesting;
use App\Entity\Staff;
use App\Repository\DiagnosisRepository;
use App\Repository\MedicalRecordRepository;
use App\Repository\PlanAppointmentRepository;
use App\Repository\PlanTestingRepository;
use App\Services\InfoService\AuthUserInfoService;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class CreatingPatientService
 * @package App\Services\CreatingPatient
 */
class CreatingPatientService
{
    protected const USER_DIAGNOSIS_CODE = 'userDiagnosis';

    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var MedicalRecordRepository $medicalRecordRepository */
    protected $medicalRecordRepository;

    /** @var PlanTestingRepository $planTestingRepository */
    protected $planTestingRepository;

    /** @var FlashBagInterface $flashBag */
    protected $flashBag;

    /** @var AuthUserInfoService $authUserInfoService */
    protected $authUserInfoService;

    /**
     * CreatingPatientService constructor.
     * @param EntityManagerInterface $entityManager
     * @param MedicalRecordRepository $medicalRecordRepository
     * @param PlanTestingRepository $planTestingRepository
     * @param FlashBagInterface $flashBag
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        MedicalRecordRepository $medicalRecordRepository,
        PlanTestingRepository $planTestingRepository,
        FlashBagInterface $flashBag
    )
    {
        $this->entityManager = $entityManager;
        $this->medicalRecordRepository = $medicalRecordRepository;
        $this->planTestingRepository = $planTestingRepository;
        $this->flashBag = $flashBag;
    }

    /**
     * Persist patient
     * @param Patient $patient
     * @param AuthUser $patientAuthUser
     * @param MedicalHistory $medicalHistory
     * @param PatientAppointment $firstPatientAppointment
     * @param Staff $staff
     * @throws Exception
     */
    public function persistPatient(
        Patient $patient,
        AuthUser $patientAuthUser,
        MedicalHistory $medicalHistory,
        PatientAppointment $firstPatientAppointment,
        Staff $staff
    ): void
    {
        $patient
            ->setAuthUser($patientAuthUser)
            ->setSmsInforming(true)
            ->setEmailInforming(true);
        $medicalHistory
            ->setPatient($patient);
        $firstPatientAppointment
            ->setMedicalHistory($medicalHistory)
            ->setStaff($staff);
        $this->persistMedicalHistory($medicalHistory);
        $this->persistFirstPatientAppointment($firstPatientAppointment);
        $this->persistFirstPatientTestsByPlan($medicalHistory);
        $this->persistPatientTestsByPlan($medicalHistory, $staff);
        $this->persistPatientAppointmentsByPlan($medicalHistory, $staff);
        $this->entityManager->persist($patient);
    }

    /**
     * Persist medical history
     * @param MedicalHistory $medicalHistory
     */
    protected function persistMedicalHistory(MedicalHistory $medicalHistory): void
    {
        $medicalHistory
            ->setEnabled(true)
            ->setDateBegin(new DateTime());
        $this->entityManager->persist($medicalHistory);
    }

    /**
     * Persist patient appointment
     * @param PatientAppointment $patientAppointment
     * @throws Exception
     */
    protected function persistFirstPatientAppointment(PatientAppointment $patientAppointment): void
    {
        $patientAppointment
            ->setEnabled(true)
            ->setMedicalRecord(
                $this->persistMedicalRecord($patientAppointment->getMedicalHistory())
            )
            ->setIsConfirmed(false)
            ->setIsFirst(true);
        $this->entityManager->persist($patientAppointment);
    }

    /**
     * Persist medical record
     * @param MedicalHistory $medicalHistory
     * @return MedicalRecord
     */
    protected function persistMedicalRecord(MedicalHistory $medicalHistory): MedicalRecord
    {
        $medicalRecord = null;
        try {
            $medicalRecord = $this->medicalRecordRepository->getMedicalRecord($medicalHistory);
        } catch (Exception $e) {
        }
        if ($medicalRecord === null) {
            $medicalRecord = (new MedicalRecord())
                ->setEnabled(true)
                ->setMedicalHistory($medicalHistory)
                ->setRecordDate(new DateTime());
            $this->entityManager->persist($medicalRecord);
        }
        return $medicalRecord;
    }

    /**
     * Persist first patient tests by plan
     * @param MedicalHistory $medicalHistory
     * @return array
     */
    protected function persistFirstPatientTestsByPlan(MedicalHistory $medicalHistory): array
    {
        $patientTests = [];
        /** @var PlanTesting $test */
        foreach ($this->planTestingRepository->getPlanOfFirstTestings() as $test) {
            $patientTesting = $this->persistPatientTest($medicalHistory, $test, true);
            $patientTests[] = $patientTesting;
        }
        return $patientTests;
    }

    /**
     * Persist patient tests by plan
     * @param MedicalHistory $medicalHistory
     * @param Staff $staff
     * @return array
     */
    protected function persistPatientTestsByPlan(MedicalHistory $medicalHistory, Staff $staff): array
    {
        $patientTests = [];
        /** @var PlanTesting $test */
        foreach ($this->planTestingRepository->getStandardPlanTesting() as $test) {
            $patientTesting = $this->persistPatientTest($medicalHistory, $test);
            $patientTests[] = $patientTesting;
            $prescription = (new Prescription())
                ->setMedicalHistory($medicalHistory)
                ->setEnabled(true)
                ->setIsCompleted(false)
                ->setStaff($staff)
                ->setCreatedTime(new DateTime())
                ->setIsPatientConfirmed(false);
            $this->entityManager->persist($prescription);
            $this->entityManager->persist(
                (new PrescriptionTesting())
                    ->setStaff($staff)
                    ->setEnabled(true)
                    ->setInclusionTime(new DateTime())
                    ->setConfirmedByStaff(false)
                    ->setPrescription($prescription)
                    ->setPatientTesting($patientTesting)
                    ->setPlannedDate($this->getTestingPlannedDate($test))
            );
        }
        return $patientTests;
    }

    /**
     * Persist patient testing
     * @param MedicalHistory $medicalHistory
     * @param PlanTesting $test
     * @param bool $isFirst
     * @return PatientTesting
     */
    protected function persistPatientTest(MedicalHistory $medicalHistory, PlanTesting $test, bool $isFirst = false): PatientTesting
    {
        $patientTesting = (new PatientTesting())
            ->setMedicalHistory($medicalHistory)
            ->setAnalysisGroup($test->getAnalysisGroup())
            ->setProcessed(false)
            ->setEnabled(true)
            ->setAnalysisDate(null)
            ->setIsFirst($isFirst);
        $this->entityManager->persist($patientTesting);
        $this->persistTestingResultsForTesting($patientTesting);
        return $patientTesting;
    }

    /**
     * Get planned date of testing
     * @param PlanTesting $planTesting
     * @return DateTimeInterface|null
     */
    protected function getTestingPlannedDate(PlanTesting $planTesting): ?DateTimeInterface
    {
        try {
            if (!$plannedDate = $this->getPlannedDate(
                new DateTime(),
                (int)$planTesting->getTimeRangeCount(),
                (int)$planTesting->getTimeRange()->getMultiplier(),
                $planTesting->getTimeRange()->getDateInterval()->getFormat()
            )) {
                throw new Exception('Не удалось добавить планируемую дату обследования!');
            }
        } catch (Exception $e) {
            $this->flashBag->add('error', $e);
            return null;
        }
        return $plannedDate;
    }

    /**
     * Persist testing results for testing
     * @param PatientTesting $patientTesting
     */
    public function persistTestingResultsForTesting(PatientTesting $patientTesting): void
    {
        foreach ($patientTesting->getAnalysisGroup()->getAnalyses() as $analysis) {
            if ($analysis->getEnabled()) {
                $analysisTestingResult = new PatientTestingResult();
                $analysisTestingResult->setPatientTesting($patientTesting);
                $analysisTestingResult->setAnalysis($analysis);
                $analysisTestingResult->setEnabled(false);
                $this->entityManager->persist($analysisTestingResult);
            }
        }
    }

    /**
     * Persist patient appointments by plan
     * @param MedicalHistory $medicalHistory
     * @param Staff $staff
     * @return array
     */
    protected function persistPatientAppointmentsByPlan(MedicalHistory $medicalHistory, Staff $staff): array
    {
        $patientAppointments = [];
        /** @var PlanAppointmentRepository $planAppointmentRepository */
        $planAppointmentRepository = $this->entityManager->getRepository(PlanAppointment::class);
        /** @var PlanAppointment $appointment */
        foreach ($planAppointmentRepository->getStandardPlanAppointment() as $appointment) {
            $patientAppointment = (new PatientAppointment())
                ->setMedicalHistory($medicalHistory)
                ->setEnabled(true)
                ->setIsConfirmed(false)
                ->setStaff($staff)
                ->setIsFirst(false);
            $this->entityManager->persist($patientAppointment);
            $prescription = (new Prescription())
                ->setMedicalHistory($medicalHistory)
                ->setEnabled(true)
                ->setIsCompleted(false)
                ->setStaff($staff)
                ->setCreatedTime(new DateTime())
                ->setIsPatientConfirmed(false);
            $this->entityManager->persist($prescription);
            $this->entityManager->persist(
                (new PrescriptionAppointment())
                    ->setStaff($staff)
                    ->setEnabled(true)
                    ->setInclusionTime(new DateTime())
                    ->setConfirmedByStaff(false)
                    ->setPrescription($prescription)
                    ->setPatientAppointment($patientAppointment)
                    ->setPlannedDateTime($this->getAppointmentPlannedDate($appointment))
            );
            $patientAppointments[] = $patientAppointment;
        }
        return $patientAppointments;
    }

    /**
     * Get planned date of appointment
     * @param PlanAppointment $planAppointment
     * @return DateTime|null
     */
    protected function getAppointmentPlannedDate(PlanAppointment $planAppointment): ?DateTime
    {
        try {
            if (!$plannedDate = $this->getPlannedDate(
                new DateTime(),
                (int)(int)$planAppointment->getTimeRangeCount(),
                (int)(int)$planAppointment->getTimeRange()->getMultiplier(),
                $planAppointment->getTimeRange()->getDateInterval()->getFormat()
            )) {
                throw new Exception('Не удалось добавить дату приема по плану!');
            }
        } catch (Exception $e) {
            $this->flashBag->add('error', $e);
            return null;
        }
        return $plannedDate;
    }

    /**
     * Get planned date
     * @param DateTime $currDate
     * @param int $timeRangeCount
     * @param int $multiplier
     * @param string $format
     *
     * @return DateTime|null
     */
    protected function getPlannedDate(
        DateTime $currDate,
        int $timeRangeCount,
        int $multiplier,
        string $format
    ): ?DateTime
    {
        try {
            return $currDate->add(
                new DateInterval(
                    'P' .
                    (string)($timeRangeCount * $multiplier) .
                    $format
                )
            )->setTime(0, 0, 0);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Persists diagnosis by name
     * @param string $diagnosisName
     * @return Diagnosis
     * @throws NonUniqueResultException
     */
    public function persistDiagnosis(string $diagnosisName)
    {
        /** @var DiagnosisRepository $diagnosisRepository */
        $diagnosisRepository = $this->entityManager->getRepository(Diagnosis::class);
        $diagnosis = $diagnosisRepository->findDiagnosisByNameAndCode($diagnosisName, self::USER_DIAGNOSIS_CODE);
        if ($diagnosis === null) {
            $diagnosis = (new Diagnosis())->setName($diagnosisName)
                ->setCode(self::USER_DIAGNOSIS_CODE)
                ->setEnabled(true);
            $this->entityManager->persist($diagnosis);
            $this->entityManager->flush();

        }
        return $diagnosis;
    }
}