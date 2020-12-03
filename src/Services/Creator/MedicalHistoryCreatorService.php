<?php

namespace App\Services\Creator;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class MedicalHistoryCreatorService
 * @package App\Services\Creator
 */
class MedicalHistoryCreatorService
{
    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /**
     * MedicalHistoryCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return MedicalHistory
     */
    public function createMedicalHistory(){
        return new MedicalHistory();
    }

    /**
     * Persist medical history
     * @param MedicalHistory $medicalHistory
     * @param Patient $patient
     */
    public function persistMedicalHistory(MedicalHistory $medicalHistory, Patient $patient): void
    {
        $this->entityManager->persist($this->prepareMedicalHistory($medicalHistory, $patient));
    }

    /**
     * @param MedicalHistory $medicalHistory
     * @param Patient $patient
     * @return MedicalHistory
     */
    private function prepareMedicalHistory(MedicalHistory $medicalHistory, Patient $patient): MedicalHistory
    {
        return $medicalHistory
            ->setPatient($patient)
            ->setEnabled(true)
            ->setDateBegin(new DateTime());
    }
}