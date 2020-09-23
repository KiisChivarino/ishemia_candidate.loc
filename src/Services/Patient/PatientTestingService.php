<?php


namespace App\Services\Patient;

use App\Entity\PlanTesting;

class PatientTestingService
{

    /**
     * Даты начала и окончания прохождения теста
     *
     * @param PlanTesting $test
     * @param $startOfTreatment
     *
     * @return null[]
     * @throws \Exception
     */
//    public function getTestingDeadlines(PlanTesting $test, $startOfTreatment)
//    {
//        $deadlines = [
//            'begin' => null,
//            'end' => null
//        ];
//        $deadlines['begin'] = $startOfTreatment->add(new \DateInterval('P'.$test->getGestationalMinAge().'W'));
//        $deadlines['end'] = $startOfTreatment->add(new \DateInterval('P'.$test->getGestationalMaxAge().'W'));
//
//        return $deadlines;
//    }
}