<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Repository\MedicalRecordRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(MedicalRecordRepository $medicalRecordRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, MedicalRecord::class);
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

    /**
     * @throws Exception
     */
    protected function configureOptions():void
    {
        $this->addOptionCheck(MedicalHistory::class, 'medicalHistory');
    }
}