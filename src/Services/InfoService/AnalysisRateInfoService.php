<?php

namespace App\Services\InfoService;

use App\Entity\AnalysisRate;

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
    static public function getAnalysisRateInfoString(?AnalysisRate $analysisRate)
    {
        return
            $analysisRate ?
                $analysisRate->getAnalysis()->getName() .
                ', ' . $analysisRate->getRateMin() . '-' . $analysisRate->getRateMax() .
                ', ' . $analysisRate->getMeasure()->getNameRu() : '';
    }
}