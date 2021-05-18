<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientTesting;
use App\Entity\PlanTesting;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class CreatorHelper
 * @package App\Services\EntityActions\Creator
 */
class CreatorHelper
{
    /** @var string Property dateBegin of medical history */
    protected const MEDICAL_HISTORY_DATE_BEGIN_PROPERTY = 'dateBegin';

    /** @var string Property heartAttackDate of patient */
    protected const PATIENT_HEART_ATTACK_DATE_PROPERTY = 'heartAttackDate';

    /** @var FlashBagInterface $flashBag */
    protected $flashBag;

    /**
     * CreatorHelper constructor.
     * @param FlashBagInterface $flashBag
     */
    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * Get planned date
     * @param DateTimeInterface $startingPointDate
     * @param int $timeRangeCount
     * @param int $multiplier
     * @param string $format
     *
     * @return DateTime|null
     */
    static public function getPlannedDate(
        DateTimeInterface $startingPointDate,
        int $timeRangeCount,
        int $multiplier,
        string $format
    ): ?DateTime
    {
        try {
            $plannedDate = clone $startingPointDate;
            $today = new DateTime('today');
            $plannedDate
                ->add(
                    new DateInterval(
                        'P' .
                        ($timeRangeCount * $multiplier) .
                        $format
                    )
                )
                ->setTime(0, 0, 0);
            if ($plannedDate < $today) {
                $plannedDate = $today;
            }
            return $plannedDate;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get starting point date
     * @param string $startingPointName
     * @param DateTimeInterface $medicalHistoryDateBegin
     * @param DateTimeInterface $patientHeartAttackDate
     * @return DateTimeInterface
     * @throws Exception
     */
    static public function getStartingPointDate(
        string $startingPointName,
        DateTimeInterface $medicalHistoryDateBegin,
        DateTimeInterface $patientHeartAttackDate
    ): DateTimeInterface
    {
        switch ($startingPointName) {
            case self::MEDICAL_HISTORY_DATE_BEGIN_PROPERTY :
                return $medicalHistoryDateBegin;
            case self::PATIENT_HEART_ATTACK_DATE_PROPERTY:
                return $patientHeartAttackDate;
            default:
                throw new Exception('Не удалось получить точку отсчета для обследования по плану!');
        }
    }

    /**
     * Get planned date of testing
     * @param PlanTesting $planTesting
     * @param PatientTesting $patientTesting
     * @return DateTimeInterface|null
     * @throws Exception
     */
    protected function getTestingPlannedDate(
        PlanTesting $planTesting,
        PatientTesting $patientTesting
    ): ?DateTimeInterface
    {
        try {
            if (!$plannedDate = CreatorHelper::getPlannedDate(
                CreatorHelper::getStartingPointDate(
                    $planTesting->getStartingPoint()->getName(),
                    clone $patientTesting->getMedicalHistory()->getDateBegin(),
                    clone $patientTesting->getMedicalHistory()->getPatient()->getHeartAttackDate()
                ),
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
}