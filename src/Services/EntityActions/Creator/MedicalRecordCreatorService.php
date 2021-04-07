<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class MedicalRecordCreatorService
 * Receives today's medical record or creates new one if it is not exists
 *
 * @package App\Services\EntityActions\Creator
 */
class MedicalRecordCreatorService extends AbstractCreatorService
{
    /** @var string Name of option: MedicalHistory entity */
    public const MEDICAL_HISTORY_OPTION_NAME = 'medicalHistory';

    /**
     * MedicalRecordCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, MedicalRecord::class);
    }

    /**
     * Creates new medical record or sets medical record with the current date if it exists
     * @throws Exception
     */
    public function create(): void
    {
        $medicalRecord = null;
        $medicalHistory = $this->options[self::MEDICAL_HISTORY_OPTION_NAME];
        $medicalRecord = $this->entityManager->getRepository(MedicalRecord::class)
            ->getCurrentMedicalRecord($medicalHistory);
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
    protected function configureOptions(): void
    {
        $this->addOptionCheck(MedicalHistory::class, self::MEDICAL_HISTORY_OPTION_NAME);
    }
}