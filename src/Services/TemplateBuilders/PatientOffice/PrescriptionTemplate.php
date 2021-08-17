<?php

namespace App\Services\TemplateBuilders\PatientOffice;

use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PrescriptionTemplate
 *
 * @package App\Services\TemplateBuilders\PatientOffice
 */
class PrescriptionTemplate extends PatientOfficeTemplateBuilder
{

    /** @var string[] Common prescription content */
    public const COMMON_CONTENT = [
        'doctor' => 'Врач',
        'recommended' => 'Вам рекомендовано',
        'unchangedPrescription' => 'Лечение оставить без изменений',
        'prescriptionTesting' => 'сдать анализы',
        'prescriptionAppointment' => 'консультации',
        'prescriptionMedicine' => 'приём лекарств',
    ];

    /** @var string[] New prescription content */
    public const NEWS_LIST_CONTENT = [
        'title' => 'Новые назначения',
        'h1' => 'Новые назначения',
    ];

    /** @var string[] Old prescription content */
    public const HISTORY_LIST_CONTENT = [
        'title' => 'История назначений',
        'h1' => 'История назначений',
    ];

    /**
     * @param RouteCollection $routeCollection
     * @param string $className
     * @throws Exception
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct($routeCollection, $className);
        $this->addPatientOfficeContent(
            self::COMMON_CONTENT,
            self::HISTORY_LIST_CONTENT,
            self::NEWS_LIST_CONTENT
        );
    }
}