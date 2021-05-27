<?php

namespace App\Services\EntityActions\Creator;
use App\Entity\PatientTesting;
use App\Entity\PlanTesting;

/**
 * Class FirstPatientTestingCreatorService
 * creates first patient testing
 * @package App\Services\EntityActions\Creator
 */
class FirstPatientTestingCreatorService extends PatientTestingCreatorService
{
    /** @var string Option plan testing */
    public const PLAN_TESTING_OPTION = 'planTesting';

    protected function prepare(): void
    {
        parent::prepare();
        /** @var PatientTesting $patientTesting */
        $patientTesting = $this->getEntity();
        /** @var PlanTesting $planTesting */
        $planTesting = $this->options[self::PLAN_TESTING_OPTION];
        $patientTesting
            ->setIsFirst(true)
            ->setIsByPlan(false)
            ->setPlanTesting($this->options[self::PLAN_TESTING_OPTION])
            ->setAnalysisGroup($planTesting->getAnalysisGroup());
    }

    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptionCheck(PlanTesting::class, self::PLAN_TESTING_OPTION);
    }
}