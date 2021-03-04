<?php

namespace App\Services\EntityActions\Editor;

use App\Entity\Prescription;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PrescriptionEditorService
 * @package App\Services\EntityActions\Editor
 */
class PrescriptionEditorService extends AbstractEditorService
{

    /**
     * PrescriptionEditorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager, $entity)
    {
        parent::__construct($entityManager, $entity);
    }


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
    }
}