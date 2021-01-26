<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Repository\MedicalRecordRepository;
use DateTime;
use Exception;

/**
 * Class MedicalRecordCreatorService
 * @package App\Services\EntityActions\Creator
 */
class MedicalRecordCreatorService extends AbstractCreatorService
{
    /**
     * @var MedicalRecordRepository
     */
    private $medicalRecordRepository;

    /**
     * MedicalRecordCreatorService constructor.
     * @param MedicalRecordRepository $medicalRecordRepository
     */
    public function __construct(MedicalRecordRepository $medicalRecordRepository)
    {
        parent::__construct(MedicalRecord::class);
        $this->medicalRecordRepository = $medicalRecordRepository;
    }

    /**
     * @throws Exception
     */
    public function create(): void
    {
        $medicalRecord = null;
        $medicalHistory = $this->options['medicalHistory'];
        $medicalRecord = $this->medicalRecordRepository->getMedicalRecord($medicalHistory);
        if ($medicalRecord === null) {
            parent::create();
            $this->getEntity()
                ->setMedicalHistory($medicalHistory)
                ->setRecordDate(new DateTime());
        } else {
            $this->setEntity($medicalRecord);
        }
    }

    protected function configureOptions()
    {
        $this->addOptionCheck(MedicalHistory::class, 'medicalHistory');
    }
}