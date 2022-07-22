<?php

namespace App\Services\StatusService\StatusBuilder\PatientAppointment;

use App\Entity\PatientAppointment;
use App\Services\StatusService\Status;
use App\Services\StatusService\StatusRender;

class FinalAppointmentStatus extends Status
{
    /**
     * OverdueAppointmentStatusBuilder constructor.
     * @param PatientAppointment $entity
     */
    public function __construct(PatientAppointment $entity)
    {
        parent::__construct($entity);
        $this->statusRender = (new StatusRender())->setColor('greenStatus')->setText('Пройдено');
    }

    /**
     * @return bool
     */
    public function matchStatus(): bool
    {
        return $this->entity->getAppointmentTime() != null
            and $this->entity->getIsProcessedByStaff()
            and $this->entity->getIsMissed() == false;
    }
}
