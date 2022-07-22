<?php

namespace App\Services\StatusService\StatusBuilder\PatientAppointment;

use App\Entity\PatientAppointment;
use App\Services\StatusService\Status;
use App\Services\StatusService\StatusRender;

/**
 * Class MissingAppointmentStatus
 * @package App\Services\StatusService\StatusBuilder\PatientAppointment
 */
class MissingAppointmentStatus extends Status
{
    /**
     * OverdueAppointmentStatusBuilder constructor.
     * @param PatientAppointment $entity
     */
    public function __construct(PatientAppointment $entity)
    {
        parent::__construct($entity);
        $this->statusRender = (new StatusRender())->setColor('redStatus')->setText('Пропущен');
    }

    /**
     * @return bool
     */
    public function matchStatus(): bool
    {
        return $this->entity->getIsMissed();
    }
}
