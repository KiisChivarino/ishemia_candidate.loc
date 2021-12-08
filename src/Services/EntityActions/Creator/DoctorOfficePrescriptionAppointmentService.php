<?php

namespace App\Services\EntityActions\Creator;

use App\Entity\PrescriptionAppointment;
use App\Entity\Staff;
use DateTime;
use Exception;

/**
 * Class DoctorOfficePrescriptionAppointmentService
 * @package App\Services\EntityActions\Creator
 */
class DoctorOfficePrescriptionAppointmentService extends PrescriptionAppointmentCreatorService
{
    /**
     * Actions with entity before persisting one
     * @throws Exception
     */
    protected function prepare(): void
    {
        parent::prepare();
        /** @var PrescriptionAppointment $prescriptionAppointment */
        $prescriptionAppointment = $this->getEntity();

        $plannedDateTime = new DateTime(
                date(
                    'Y-m-d ',
                    $prescriptionAppointment->getPlannedDateTime()->getTimestamp()
                ) . date('H:i', (new DateTime())->getTimestamp())
            )
        ;

        $prescriptionAppointment
            ->setPlannedDateTime($plannedDateTime)
            ->setStaff($this->options[self::STAFF_OPTION])
        ;
    }

    /**
     * Options of Doctor Office Creator Service
     * @throws Exception
     */
    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptionCheck(Staff::class, self::STAFF_OPTION);
    }

}