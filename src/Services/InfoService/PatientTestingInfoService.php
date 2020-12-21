<?php

namespace App\Services\InfoService;

use App\Entity\PatientTesting;

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
        $patientInfo = 'Пациент: '.
            AuthUserInfoService::getFIO($patientTesting->getMedicalHistory()->getPatient()->getAuthUser(), true);
        if ($patientTesting->getPrescriptionTesting()) {
            $plannedDateTimeString = ', '.$patientTesting->getPrescriptionTesting()->getPlannedDate()
                    ->format(self::PATIENT_TESTING_DATE_FORMAT);
        } else {
            $plannedDateTimeString = '';
        }
        if ($patientTesting->getAnalysisDate()) {
            $analysisDateString = ', '.$patientTesting->getAnalysisDate()
                ->format(self::PATIENT_TESTING_DATE_FORMAT);
        } else {
            $analysisDateString = '';
        }
        return
            is_null($patientTesting->getAnalysisDate())
                ?
                $patientInfo
                .', '.$patientTesting->getAnalysisGroup()->getName()
                .$plannedDateTimeString
                :
                $patientInfo
                .', '.$patientTesting->getAnalysisGroup()->getName()
                .$analysisDateString;
    }

    /**
     * Check for empty all patient testing results
     * @param PatientTesting $patientTesting
     * @return bool
     */
    static public function isEmptyPatientTestingResults(PatientTesting $patientTesting): bool
    {
        foreach ($patientTesting->getPatientTestingResults() as $result) {
            if($result->getResult() !== null){
                return false;
            }
        }
        return true;
    }
}