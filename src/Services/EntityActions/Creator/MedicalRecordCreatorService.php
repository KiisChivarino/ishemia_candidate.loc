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
class MedicalRecordCreatorService
{
    /**
     * @var MedicalRecordRepository
     */
    private $medicalRecordRepository;

    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /**
     * MedicalRecordCreatorService constructor.
     * @param MedicalRecordRepository $medicalRecordRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(MedicalRecordRepository $medicalRecordRepository, EntityManagerInterface $entityManager)
    {
        $this->medicalRecordRepository = $medicalRecordRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param MedicalHistory $medicalHistory
     * @return MedicalRecord
     */
    public function createMedicalRecord(MedicalHistory $medicalHistory): MedicalRecord
    {
        $medicalRecord = null;
        try {
            $medicalRecord = $this->medicalRecordRepository->getMedicalRecord($medicalHistory);
        } catch (Exception $e) {
        }
        if ($medicalRecord === null) {
            $medicalRecord = (new MedicalRecord())
                ->setEnabled(true)
                ->setMedicalHistory($medicalHistory)
                ->setRecordDate(new DateTime());
        }
        return $medicalRecord;
    }

    /**
     * Persist medical record
     * @param MedicalHistory $medicalHistory
     * @return MedicalRecord
     */
    public function persistMedicalRecord(MedicalHistory $medicalHistory): MedicalRecord
    {
        $medicalRecord = $this->createMedicalRecord($medicalHistory);
        if (!$this->entityManager->contains($medicalRecord)) {
            $this->entityManager->persist($medicalRecord);
        }
        return $medicalRecord;
    }
}