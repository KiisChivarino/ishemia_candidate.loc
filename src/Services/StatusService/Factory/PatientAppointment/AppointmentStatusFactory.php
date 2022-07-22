<?php

namespace App\Services\StatusService\Factory\PatientAppointment;

use App\Entity\PatientAppointment;
use App\Services\StatusService\DefaultStatus;
use App\Services\StatusService\StatusBuilder\PatientAppointment\FinalAppointmentStatus;
use App\Services\StatusService\StatusBuilder\PatientAppointment\MissingAppointmentStatus;
use App\Services\StatusService\StatusBuilder\PatientAppointment\OverdueAppointmentStatusBuilder;
use App\Services\StatusService\StatusBuilder\PatientAppointment\ProcessAppointmentStatusFactory;
use Exception;

/**
 * Class AppointmentStatusFactory
 * @package App\Services\StatusService
 */
class AppointmentStatusFactory
{
    /**
     * @param PatientAppointment $entity
     *
     * @return mixed|null
     *
     * @throws Exception
     */
    public function getStatus(PatientAppointment $entity)
    {
        $AppointmentStatusList = [
            new OverdueAppointmentStatusBuilder($entity),
            new ProcessAppointmentStatusFactory($entity),
            new FinalAppointmentStatus($entity),
            new MissingAppointmentStatus($entity),
            new DefaultStatus($entity),
        ];

        foreach($AppointmentStatusList as $appointmentStatus){
            if($appointmentStatus->matchStatus()){
                return $appointmentStatus;
            }
        }

        return null;
    }
}