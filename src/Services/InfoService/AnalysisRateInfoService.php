<?php

namespace App\Services\InfoService;

use App\Entity\AnalysisRate;
use App\Entity\PatientTestingResult;

/**
 * Class AnalysisRateInfoService
 * методы для работы с данными AnalysisRate
 *
 * @package App\Services\InfoService
 */
class AnalysisRateInfoService
{
    /**
     * Возвращает строку с описанием единицы референтных значений
     *
     * @param AnalysisRate|null $analysisRate
     *
     * @return string
     */
    static public function getAnalysisRateInfoString(?AnalysisRate $analysisRate): string
    {
        return
            $analysisRate ?
                $analysisRate->getAnalysis()->getName() .
                ', ' . $analysisRate->getRateMin() . '-' . $analysisRate->getRateMax() .
                ', ' . $analysisRate->getMeasure()->getNameRu() : '';
    }

    /**
     * Проверяет, существуют ли референтные значения для результатов анализа пациента
     *
     * @param PatientTestingResult $patientTestingResult
     * @return bool
     */
    static public function isAnalysisRatesExistForPatientTestingResult(PatientTestingResult $patientTestingResult): bool
    {
        return !empty($patientTestingResult->getAnalysisRate());
    }
}