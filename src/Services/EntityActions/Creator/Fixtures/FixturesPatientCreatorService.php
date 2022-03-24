<?php

namespace App\Services\EntityActions\Creator\Fixtures;

use App\Entity\City;
use App\Entity\Hospital;
use App\Entity\Patient;
use App\Services\EntityActions\Creator\PatientCreatorService;

/**
 * Class FixturesPatientCreatorService
 * @package App\Services\EntityActions\Creator
 */
class FixturesPatientCreatorService extends PatientCreatorService
{
    /** @const string */
    public const
        ADDRESS_OPTION = 'address',
        HOSPITAL_OPTION = 'hospital',
        CITY_OPTION = 'city',
        BIRTH_DATE_OPTION = 'birthDate',
        HEART_ATTACK_DATE_OPTION = 'heartAttackDate';

    /**
     * Actions with entity before persist
     */
    protected function prepare(): void
    {
        parent::prepare();

        /** @var Patient $patient */
        $patient = $this->getEntity();
        $patient
            ->setHospital($this->options[self::HOSPITAL_OPTION])
            ->setAddress($this->options[self::ADDRESS_OPTION])
            ->setDateBirth($this->options[self::BIRTH_DATE_OPTION])
            ->setCity($this->options[self::CITY_OPTION])
            ->setHeartAttackDate($this->options[self::HEART_ATTACK_DATE_OPTION]);
    }

    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptionCheck('string', self::ADDRESS_OPTION);
        $this->addOptionCheck(Hospital::class, self::HOSPITAL_OPTION);
        $this->addOptionCheck(City::class, self::CITY_OPTION);
        $this->addOptionCheck(\Datetime::class, self::BIRTH_DATE_OPTION);
        $this->addOptionCheck(\Datetime::class, self::HEART_ATTACK_DATE_OPTION);
    }
}