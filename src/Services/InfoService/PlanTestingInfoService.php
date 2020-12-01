<?php

namespace App\Services\InfoService;

use App\Entity\PlanTesting;

/**
 * Class PlanTestingInfoService
 * @package App\Services\InfoService
 */
class PlanTestingInfoService
{
    /**
     * Get info string of testing plan
     * @param PlanTesting $planTesting
     * @return string
     */
    static public function getPlanTestingInfoString(PlanTesting $planTesting): string
    {
        $planTestingInfoString =
            'Обследование: ' .$planTesting->getAnalysisGroup()->getName()
            . ', Временной диапазон: '. $planTesting->getTimeRange()->getTitle();
        if($planTesting->getTimeRange()->getIsRegular()){
            $planTestingInfoString .= ', регулярное';
        }
        return $planTestingInfoString;
    }
}