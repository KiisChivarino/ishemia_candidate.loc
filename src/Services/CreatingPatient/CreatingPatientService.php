<?php

namespace App\Services\CreatingPatient;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\PatientAppointment;
use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use App\Entity\PlanAppointment;
use App\Entity\PlanTesting;
use App\Repository\MedicalRecordRepository;
use App\Repository\PlanAppointmentRepository;
use App\Repository\PlanTestingRepository;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class CreatingPatientService
 * @package App\Services\CreatingPatient
 */
class CreatingPatientService
{
    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var MedicalRecordRepository $medicalRecordRepository */
    protected $medicalRecordRepository;

    /** @var PlanTestingRepository $planTestingRepository */
    protected $planTestingRepository;

    /** @var FlashBagInterface $flashBag */
    protected $flashBag;

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
     * @param PatientAppointment $patientAppointment
     * @throws Exception
     */
    public function persistPatient(
        PatientAppointment $patientAppointment
    ): void
    {
        $medicalHistory = $patientAppointment->getMedicalHistory();
        $this->persistMedicalHistory($medicalHistory);
        $this->persistPatientAppointment($patientAppointment);
        $this->persistPatientTestsByPlan($medicalHistory);
        $this->persistPatientAppointmentsByPlan($medicalHistory);
        $this->entityManager->persist($medicalHistory->getPatient());
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
    protected function persistPatientAppointment(PatientAppointment $patientAppointment): void
    {
        $patientAppointment
            ->setEnabled(true)
            ->setMedicalRecord(
                $this->persistMedicalRecord($patientAppointment->getMedicalHistory())
            )
            ->setIsConfirmed(false)
            ->setPlannedTime(new DateTime());
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
     * Persist patient tests by plan
     * @param MedicalHistory $medicalHistory
     * @return array
     */
    protected function persistPatientTestsByPlan(MedicalHistory $medicalHistory): array
    {
        $patientTests = [];
        /** @var PlanTesting $test */
        foreach ($this->planTestingRepository->getStandardPlanTesting() as $test) {
            $patientTest = new PatientTesting();
            $patientTest->setMedicalHistory($medicalHistory);
            $patientTest->setAnalysisGroup($test->getAnalysisGroup());
            $patientTest->setProcessed(false);
            $patientTest->setEnabled(true);
            $patientTest->setAnalysisDate(null);
            $patientTest->setPlannedDate($this->getTestingPlannedDate($test));
            $this->entityManager->persist($patientTest);
            $patientTests[] = $patientTest;
            $this->persistTestingResultsForTesting($patientTest);
        }
        return $patientTests;
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
     * @return array
     */
    protected function persistPatientAppointmentsByPlan(MedicalHistory $medicalHistory): array
    {
        $patientAppointments = [];
        /** @var PlanAppointmentRepository $planAppointmentRepository */
        $planAppointmentRepository = $this->entityManager->getRepository(PlanAppointment::class);
        /** @var PlanAppointment $appointment */
        foreach ($planAppointmentRepository->getStandardPlanAppointment() as $appointment) {
            $patientAppointment = (new PatientAppointment())
                ->setMedicalHistory($medicalHistory)
                ->setEnabled(true)
                ->setPlannedTime($this->getAppointmentPlannedDate($appointment))
                ->setIsConfirmed(false);
            $this->entityManager->persist($patientAppointment);
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
            );
        } catch (Exception $e) {
            return null;
        }
    }
}