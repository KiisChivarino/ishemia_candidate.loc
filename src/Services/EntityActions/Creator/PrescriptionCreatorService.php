<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\Prescription;
use App\Services\ControllerGetters\EntityActions;
use DateTime;
use Exception;

/**
 * Class PrescriptionCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PrescriptionCreatorService extends AbstractCreatorService
{
    /**
     * @param string $entityClass
     * @param array $options
     * @throws Exception
     */
    public function before(string $entityClass, array $options = []): void
    {
        parent::before($entityClass, $options);
        /** @var Prescription $prescriptionEntity */
        $prescriptionEntity = $this->getEntity();
        $prescriptionEntity
            ->setMedicalHistory($this->options['medicalHistory'])
            ->setEnabled(true);
    }

    /**
     * @param EntityActions $entityActions
     * @param array $options
     * @throws Exception
     */
    public function after(EntityActions $entityActions, array $options = []): void
    {
        parent::after($entityActions, $options);
        $this->getEntity()->setIsCompleted(false);
        $this->getEntity()->setIsPatientConfirmed(false);
        $this->getEntity()->setCreatedTime(new DateTime());
    }

    protected function configureOptions()
    {
        $this->addOptionCheck(MedicalHistory::class, 'medicalHistory');
    }
}