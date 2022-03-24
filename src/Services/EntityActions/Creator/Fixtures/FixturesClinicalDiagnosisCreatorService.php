<?php

namespace App\Services\EntityActions\Creator\Fixtures;

use App\Entity\ClinicalDiagnosis;
use App\Entity\Diagnosis;
use App\Services\EntityActions\Creator\ClinicalDiagnosisCreatorService;

/**
 * Class FixturesClinicalDiagnosisCreatorService
 * @package App\Services\EntityActions\Creator
 */
class FixturesClinicalDiagnosisCreatorService extends ClinicalDiagnosisCreatorService
{
    /** @var string MKB code in Diagnosis */
    public const MKB_CODE_OPTION = 'mkbCode';

    /** @const string MKB code in Diagnosis */
    public const DESCRIPTION_OPTION = 'description';

    /**
     * Actions with entity before persist
     */
    protected function prepare(): void
    {
        /** @var ClinicalDiagnosis $clinicalDiagnosis */
        $clinicalDiagnosis = $this->getEntity();
        $clinicalDiagnosis
            ->setText($this->options[self::DESCRIPTION_OPTION])
            ->setMKBCode(
                $this->entityManager->getRepository(Diagnosis::class)->findOneBy(
                    [
                        'code' => $this->options[self::MKB_CODE_OPTION]
                    ]
                )
            );
    }

    /**
     * @return void
     */
    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptionCheck('string', self::MKB_CODE_OPTION);
        $this->addOptionCheck('string', self::DESCRIPTION_OPTION);
    }
}