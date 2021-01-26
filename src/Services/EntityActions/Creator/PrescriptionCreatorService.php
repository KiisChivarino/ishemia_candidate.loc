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
     * PrescriptionCreatorService constructor.
     */
    public function __construct()
    {
        parent::__construct(Prescription::class);
    }

    /**
     * @param array $options
     * @param null $entity
     * @throws Exception
     */
    public function before(array $options = [], $entity = null): void
    {
        parent::before($options);
        $this->getEntity()->setMedicalHistory($this->options['medicalHistory']);
    }

    /**
     * @param EntityActions $entityActions
     */
    protected function prepare(EntityActions $entityActions): void
    {
        /** @var Prescription $prescription */
        $prescription = $this->getEntity();
        $prescription->setIsCompleted(false);
        $prescription->setIsPatientConfirmed(false);
        $prescription->setCreatedTime(new DateTime());
    }

    protected function configureOptions()
    {
        $this->addOptionCheck(MedicalHistory::class, 'medicalHistory');
    }
}