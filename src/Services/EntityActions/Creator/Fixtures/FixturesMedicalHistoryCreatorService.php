<?php

namespace App\Services\EntityActions\Creator\Fixtures;

use App\Entity\MedicalHistory;
use App\Services\EntityActions\Creator\MedicalHistoryCreatorService;
use DateTime;

/**
 * Class FixturesMedicalHistoryCreatorService
 * @package App\Services\EntityActions\Creator
 */
class FixturesMedicalHistoryCreatorService extends MedicalHistoryCreatorService
{
    /**
     * Actions with entity before persist
     */
    protected function prepare(): void
    {
        parent::prepare();
        /** @var MedicalHistory $medicalHistory */
        $medicalHistory = $this->getEntity();
        $medicalHistory->setDateBegin(new DateTime());
    }
}