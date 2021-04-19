<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\Prescription;
use App\Services\EntityActions\Core\AbstractCreatorService;
use App\Services\EntityActions\Core\EntityActionsInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PrescriptionCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PrescriptionCreatorService extends AbstractCreatorService
{
    /** @var string Staff option of entity actions */
    const STAFF_OPTION = 'staff';

    /** @var string Medical option of entity action */
    const MEDICAL_HISTORY_OPTION = 'medicalHistory';

    /**
     * PrescriptionCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, Prescription::class);
    }

    /**
     * Actions with entity before submiting form
     * @param array $options
     * @param null $entity
     * @return EntityActionsInterface
     * @throws Exception
     */
    public function before(array $options = [], $entity = null): EntityActionsInterface
    {
        /** Create prescription, set enabled=true */
        parent::before($options);
        $this->getEntity()->setMedicalHistory($this->options['medicalHistory']);
        return $this;
    }

    /**
     * Actions with entity before persisting one
     */
    protected function prepare(): void
    {
        /** @var Prescription $prescription */
        $prescription = $this->getEntity();
        $prescription->setIsCompleted(false);
        $prescription->setIsPatientConfirmed(false);
        $prescription->setCreatedTime(new DateTime());
        /** Executes without form */
    }

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->addOptionCheck(MedicalHistory::class, self::MEDICAL_HISTORY_OPTION);
    }
}