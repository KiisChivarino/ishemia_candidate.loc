<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\TemplateBuilders\AdminTemplateBuilder;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class MedicalHistoryTemplate
 *
 * @package App\Services\TemplateBuilders\PatientOffice
 */
class MedicalHistoryTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common show content for staff templates */
    protected const SHOW_CONTENT = [
        'h1' => 'История болезни',
        'title' => 'История болезни',
    ];

    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct($routeCollection, $className);
        $this->addContent(
            self::LIST_CONTENT,
            self::NEW_CONTENT,
            self::SHOW_CONTENT,
            self::EDIT_CONTENT,
            self::FORM_CONTENT,
            self::FORM_SHOW_CONTENT,
            self::COMMON_CONTENT,
            self::FILTER_CONTENT
        );
    }
}