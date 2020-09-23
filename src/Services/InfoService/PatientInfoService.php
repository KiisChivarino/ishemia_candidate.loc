<?php

namespace App\Services\InfoService;

use App\Entity\Patient;
use App\Entity\PatientTesting;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;

class PatientInfoService
{
    /** @var int Максимальное количество недель беременности */
//    const GESTATION_WEEKS_COUNT = 40;

    /** @var int Минимальное количество недель беременности */
//    const MIN_WEEKS_COUNT = 2;

    /**
     * Считает индекс массы тела
     *
     * @param Patient $patient
     *
     * @return false|float|string
     */
    public function getBodyMassIndex(Patient $patient)
    {
        $heightSquare = ($patient->getHeight() / 100) * ($patient->getHeight() / 100);
        return $heightSquare ? round($patient->getWeight() / $heightSquare, 2) : 'Нет данных';
    }

    /**
     * Возраст пациента
     *
     * @param Patient $patient
     *
     * @return int
     */
    public function getAge(Patient $patient)
    {
        return (new DateTime())->diff($patient->getDateBirth())->y;
    }


    /**
     * Срок гестации в неделях
     *
     * @param DateTimeInterface $startOfTreatment
     *
     * @return bool|int
     */
//    public function getGestationWeeks(DateTimeInterface $startOfTreatment)
//    {
//        $currentDate = new DateTime();
//        $interval = $currentDate->diff($startOfTreatment); // получаем разницу в виде объекта DateInterval
//        $weeksNumber = $interval->invert === 1 ? $interval->days / 7 : $interval->days / 7 * -1;
//        if ($weeksNumber < self::MIN_WEEKS_COUNT || $weeksNumber > self::GESTATION_WEEKS_COUNT) {
//            return false;
//        }
//        return (int)$weeksNumber + 1;
//    }

    /**
     * Примерная дата окончания срока гестации
     *
     * @param DateTimeInterface $startOfTreatment
     *
     * @return DateTimeInterface
     * @throws Exception
     */
//    public function getGestationEndDate(DateTimeInterface $startOfTreatment): DateTimeInterface
//    {
//        return $startOfTreatment->add(new DateInterval('P'.self::GESTATION_WEEKS_COUNT.'W'));
//    }

    /**
     * Возвращает необработанные анализы
     *
     * @param Patient $patient
     *
     * @return array
     */
//    public function getUnprocessedTestings(Patient $patient): array
//    {
//        $patientTestings = [];
//        foreach ($patient->getMedicalHistories() as $medicalHistory){
//            array_merge($patientTestings, is_array($medicalHistory->getPatientTestings()) ? $medicalHistory->getPatientTestings() : []);
//        }
//        $patientUnprocessedTestings = [];
//        foreach ($patientTestings as $testing) {
//            if (!$testing->getProcessed() && $testing->getGestationalMinAge() <= $this->getGestationWeeks($patient->getDateStartOfTreatment())) {
//                $duplicateTesting = $this->checkTestingForDuplicateAnalysisGroup($testing, $patientUnprocessedTestings);
//                if ($duplicateTesting && $duplicateTesting->getGestationalMinAge() < $testing->getGestationalMinAge()) {
//                    unset($duplicateTesting, $patientUnprocessedTestings);
//                }
//                $patientUnprocessedTestings[] = $testing;
//            }
//        }
//        return $patientUnprocessedTestings;
//    }

    /**
     * @param PatientTesting $testing
     * @param array $patientTestings
     *
     * @return PatientTesting|bool
     */
    private function checkTestingForDuplicateAnalysisGroup(PatientTesting $testing, array $patientTestings)
    {
        /** @var PatientTesting $patientTesting */
        foreach ($patientTestings as $patientTesting) {
            if ($testing->getAnalysisGroup() === $patientTesting->getAnalysisGroup()) {
                return $patientTesting;
            }
        }
        return false;
    }
}