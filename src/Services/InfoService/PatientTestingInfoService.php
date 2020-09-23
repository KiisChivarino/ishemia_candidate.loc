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
    /**
     * Возвращает строку с описанием анализа пациента
     *
     * @param PatientTesting $patientTesting
     *
     * @return string
     */
    public function getPatientTestingInfoString(PatientTesting $patientTesting)
    {
        return
            is_null($patientTesting->getAnalysisDate())
                ?
                $patientTesting->getMedicalHistory()->getPatient()->getAuthUser()->getLastName()
                .', '.$patientTesting->getAnalysisGroup()->getName()
                :
                $patientTesting->getMedicalHistory()->getPatient()->getAuthUser()->getLastName()
                .', '.$patientTesting->getAnalysisGroup()->getName()
                .', '.$patientTesting->getAnalysisDate()->format('d.m.Y');
    }
}