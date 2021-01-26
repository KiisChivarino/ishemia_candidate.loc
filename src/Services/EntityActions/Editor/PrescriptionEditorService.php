<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\Prescription;
use App\Services\ControllerGetters\EntityActions;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
use DateTime;
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
     */
    public function __construct(MedicalRecordCreatorService $medicalRecordCreatorService)
    {
        parent::__construct(Prescription::class);
        $this->medicalRecordCreatorService = $medicalRecordCreatorService;
    }

    /**
     * @param EntityActions $entityActions
     * @param array $options
     * @throws Exception
     */
    public function after(EntityActions $entityActions, array $options = []): void
    {
        $this->prepare($entityActions);
        $this->persist($entityActions->getEntityManager());
    }

    /**
     * @param EntityActions $entityActions
     * @throws Exception
     */
    protected function prepare(EntityActions $entityActions): void
    {
        /** @var Prescription $prescription */
        $prescription = $this->getEntity();
        if($prescription->getIsCompleted() && !$prescription->getCompletedTime()){
            $this->medicalRecordCreatorService->execute(
                $entityActions,
                [
                    'medicalHistory' => $prescription->getMedicalHistory(),
                ]
            );
            $prescription->setCompletedTime(new DateTime());
        }
    }
}