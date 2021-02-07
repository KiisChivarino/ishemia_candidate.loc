<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\Prescription;
use App\Services\ControllerGetters\EntityActions;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PrescriptionEditorService
 * @package App\Services\EntityActions\Editor
 */
class PrescriptionEditorService extends AbstractEditorService
{
    /** @var MedicalRecordCreatorService $medicalRecordCreatorService */
    private $medicalRecordCreatorService;

    /**
     * PrescriptionEditorService constructor.
     * @param MedicalRecordCreatorService $medicalRecordCreatorService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        MedicalRecordCreatorService $medicalRecordCreatorService,
        EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->medicalRecordCreatorService = $medicalRecordCreatorService;
    }

    /**
     * @param EntityActions $entityActions
     * @throws Exception
     */
    protected function prepare(): void
    {
        /** @var Prescription $prescription */
        $prescription = $this->getEntity();
        if($prescription->getIsCompleted() && !$prescription->getCompletedTime()){
            $this->medicalRecordCreatorService->execute(
                [
                    'medicalHistory' => $prescription->getMedicalHistory(),
                ]
            );
            $prescription->setCompletedTime(new DateTime());
        }
    }

    protected function configureOptions(): void
    {
        $this->setEntityClass(Prescription::class);
    }
}