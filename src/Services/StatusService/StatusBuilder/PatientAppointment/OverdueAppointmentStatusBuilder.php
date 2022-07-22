<?php

namespace App\Services\StatusService\StatusBuilder\PatientAppointment;

use App\Entity\PatientAppointment;
use App\Services\StatusService\Status;
use App\Services\StatusService\StatusRender;

/**
 * Class OverdueAppointmentStatusBuilder
 * @package App\Services\StatusService\StatusBuilder
 */
class OverdueAppointmentStatusBuilder extends Status
{
    /**
     * OverdueAppointmentStatusBuilder constructor.
     * @param PatientAppointment $entity
     */
    public function __construct(PatientAppointment $entity)
    {
        parent::__construct($entity);
        $this->statusRender = (new StatusRender())->setColor('redStatus')->setText('Просрочено');
    }

    /**
     * @return bool
     */
    public function matchStatus(): bool
    {
        if ($this->entity
                ->getPrescriptionAppointment() !== null) {
            return date_format($this->entity
                    ->getPrescriptionAppointment()
                    ->getPlannedDateTime(), 'Y-m-d') < date('Y-m-d');
        }
        return false;
    }
}
