<?php

namespace App\Services\InfoService;

use App\Entity\Patient;
use DateTime;

class PatientInfoService
{
    /**
     * Считает индекс массы тела
     *
     * @param Patient $patient
     *
     * @return false|float|string
     */
    static public function getBodyMassIndex(Patient $patient)
    {
        $heightSquare = ($patient->getHeight() / 100) * ($patient->getHeight() / 100);
        return $heightSquare ? round($patient->getWeight() / $heightSquare, 2) . '%': 'Нет данных';
    }

    /**
     * Возраст пациента
     *
     * @param Patient $patient
     *
     * @return int
     */
    static public function getAge(Patient $patient): int
    {
        return (new DateTime())->diff($patient->getDateBirth())->y;
    }
}