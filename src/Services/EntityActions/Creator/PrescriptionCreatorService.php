<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\MedicalHistory;
use App\Entity\Prescription;
use App\Entity\Staff;
use App\Services\ControllerGetters\EntityActions;
use DateTime;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct(Prescription::class,  $translator);
    }

    /**
     * Actions with entity before submiting form
     * @param array $options
     * @param null $entity
     * @throws Exception
     */
    public function before(array $options = [], $entity = null): void
    {
        /** Create prescription, set enabled=true */
        parent::before($options);
        $this->getEntity()->setMedicalHistory($this->options['medicalHistory']);
    }

    /**
     * Actions with entity before persisting one
     * @param EntityActions $entityActions
     */
    protected function prepare(EntityActions $entityActions): void
    {
        /** @var Prescription $prescription */
        $prescription = $this->getEntity();
        $prescription->setIsCompleted(false);
        $prescription->setIsPatientConfirmed(false);
        $prescription->setCreatedTime(new DateTime());
        /** Executes without form */
        if (!$prescription->getStaff())
        {
            $prescription->setStaff($this->options[self::STAFF_OPTION]);
        }
    }

    protected function configureOptions()
    {
        $this->addOptionCheck(MedicalHistory::class, self::MEDICAL_HISTORY_OPTION);
        $this->addOptionCheck( Staff::class, self::STAFF_OPTION);
    }
}