<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PatientAppointment;
use App\Entity\Prescription;
use App\Services\EntityActions\Core\AbstractCreatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PatientAppointmentCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PatientAppointmentCreatorService extends AbstractCreatorService
{
    /** @var string Name of Prescription option */
    public const PRESCRIPTION_OPTION = 'prescription';

    /** @var string Name of Staff option */
    public const STAFF_OPTION = 'staff';

    /**
     * PatientTestingCreatorService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws Exception
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($entityManager, PatientAppointment::class);
    }

    protected function prepare(): void
    {
        $patientAppointment = $this->getEntity();
        $patientAppointment
            ->setIsConfirmed(false)
            ->setIsFirst(false)
            ->setIsByPlan(false)
            ->setMedicalHistory($this->options[self::PRESCRIPTION_OPTION]->getMedicalHistory());
    }

    protected function configureOptions(): void
    {
        $this->addOptionCheck(Prescription::class, self::PRESCRIPTION_OPTION);
    }
}