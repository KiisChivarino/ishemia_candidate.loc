<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Repository\MedicalRecordRepository;
use DateTime;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @param TranslatorInterface $translator
     */
    public function __construct(MedicalRecordRepository $medicalRecordRepository, TranslatorInterface $translator)
    {
        parent::__construct(MedicalRecord::class, $translator);
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