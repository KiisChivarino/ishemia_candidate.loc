<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\Prescription;
use App\Entity\PrescriptionMedicine;
use App\Entity\Staff;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PrescriptionMedicineCreatorService
 * @package App\Services\EntityActions\Creator
 */
class PrescriptionMedicineCreatorService extends AbstractCreatorService
{
    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    private $STAFF_OPTION;

    /**
     * @var string
     * yaml:config/services/entityActions/doctor_office_entity_actions.yml
     */
    private $PRESCRITION_OPTION;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $staffOption,
        string $prescriptionOption
    )
    {
        parent::__construct($entityManager, PrescriptionMedicine::class);
        $this->STAFF_OPTION = $staffOption;
        $this->PRESCRITION_OPTION = $prescriptionOption;
    }

    /**
     * @throws Exception
     */
    protected function prepare(): void
    {
        /** @var PrescriptionMedicine $prescriptionMedicine */
        $prescriptionMedicine = $this->getEntity();
        $prescriptionMedicine
            ->setInclusionTime(new DateTime())
            ->setPrescription($this->options['prescription']);
        /** Executes without form */
        if (!$prescriptionMedicine->getStaff()) {
            $prescriptionMedicine->setStaff($this->options[$this->STAFF_OPTION]);
        }
    }

    /**
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        $this->addOptionCheck(Prescription::class, $this->PRESCRITION_OPTION);
        $this->addOptionCheck(Staff::class, $this->STAFF_OPTION);
    }
}