<?php

namespace App\Services\InfoService;

use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;

/**
 * Class PatientTestingInfoService
 * методы для работы с данными PatientTesting
 *
 * @package App\Services\InfoService
 */
class PatientTestingInfoService
{
    /** @var string Format of patient testing date */
    public const PATIENT_TESTING_DATE_FORMAT = 'd.m.Y H:i';

    /**
     * Возвращает строку с описанием анализа пациента
     *
     * @param PatientTesting $patientTesting
     *
     * @return string
     */
    static public function getPatientTestingInfoString(PatientTesting $patientTesting): string
    {
        $patientInfo = 'Пациент: ' .
            AuthUserInfoService::getFIO($patientTesting->getMedicalHistory()->getPatient()->getAuthUser(), true);
        if ($patientTesting->getPrescriptionTesting()) {
            $plannedDateTimeString = ', ' . $patientTesting->getPrescriptionTesting()->getPlannedDate()
                    ->format(self::PATIENT_TESTING_DATE_FORMAT);
        } else {
            $plannedDateTimeString = '';
        }
        if ($patientTesting->getAnalysisDate()) {
            $analysisDateString = ', ' . $patientTesting->getAnalysisDate()
                    ->format(self::PATIENT_TESTING_DATE_FORMAT);
        } else {
            $analysisDateString = '';
        }
        return
            is_null($patientTesting->getAnalysisDate())
                ?
                $patientInfo
                . ', ' . $patientTesting->getAnalysisGroup()->getName()
                . $plannedDateTimeString
                :
                $patientInfo
                . ', ' . $patientTesting->getAnalysisGroup()->getName()
                . $analysisDateString;
    }

    /**
     * Check for empty all patient testing results
     *
     * @param PatientTesting $patientTesting
     *
     * @return bool
     */
    static public function isEmptyPatientTestingResults(PatientTesting $patientTesting): bool
    {
        foreach ($patientTesting->getPatientTestingResults() as $result) {
            if ($result->getResult() !== null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check for empty all patient testing analysis rate results
     *
     * @param PatientTesting $patientTesting
     *
     * @return bool
     */
    static public function isPatientTestingResultsExists(PatientTesting $patientTesting): bool
    {
        foreach ($patientTesting->getPatientTestingResults() as $result) {
            if ($result !== null) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if patient analysis is in range of referent values
     * If analysis doesnt have referent values returns true
     *
     * @param $patientTesting
     *
     * @return bool
     */
    public static function isPatientTestingInRangeOfReferentValues($patientTesting): bool
    {
        foreach ($patientTesting->getPatientTestingResults() as $result) {
            if (!self::isPatientTestingResultEmpty($result)) {
                if (self::isResultInRangeInRangeOfReferentValues($result)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
        return true;
    }

    /**
     * Check for empty patient testing result
     *
     * @param PatientTestingResult $patientTestingResult
     *
     * @return bool
     */
    public static function isPatientTestingResultEmpty(PatientTestingResult $patientTestingResult): bool
    {
        return is_null($patientTestingResult->getResult())
            || is_null($patientTestingResult->getAnalysisRate())
            || is_null($patientTestingResult->getAnalysisRate()->getRateMax())
            || is_null($patientTestingResult->getAnalysisRate()->getRateMin());
    }

    /**
     * Check patient testing result in range of referent values
     *
     * @param PatientTestingResult $patientTestingResult
     *
     * @return bool
     */
    public static function isResultInRangeInRangeOfReferentValues(PatientTestingResult $patientTestingResult): bool
    {
        $analysisRate = $patientTestingResult->getAnalysisRate();
        $resultValue = $patientTestingResult->getResult();
        return $analysisRate->getRateMax() >= $resultValue && $resultValue >= $analysisRate->getRateMin();
    }
}