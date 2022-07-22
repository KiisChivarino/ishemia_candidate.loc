<?php

namespace App\Services\StatusService;

use App\Entity\PatientAppointment;

/**
 * Class DefaultStatus
 * @package App\Services\StatusService
 */
class DefaultStatus extends Status
{
    /**
     * OverdueAppointmentStatusBuilder constructor.
     * @param PatientAppointment $entity
     */
    public function __construct(PatientAppointment $entity)
    {
        parent::__construct($entity);
        $this->statusRender = (new StatusRender())->setColor('')->setText('Статус не определен');
    }

    /**
     * @return bool
     */
    public function matchStatus(): bool
    {
        return true;
    }
}
