<?php

namespace App\Services\TemplateBuilders\PatientOffice;

use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientMainTemplate
 *
 * @package App\Services\TemplateBuilders\PatientOffice
 */
class PatientMainTemplate extends PatientOfficeTemplateBuilder
{
    /** @var array */
    protected const SHOW_CONTENT = [
        'title' => 'Главная',
        'h1' => 'Главная',
        'read' => ArticleTemplate::COMMON_CONTENT['read'],
        'notificationWith' => NotificationTemplate::COMMON_CONTENT['notificationWith'],
        'recommendedForYou' => PrescriptionTemplate::COMMON_CONTENT['recommended'],
        'information' => 'Справка',
        'applicationConsultationTitle' => 'Заявка на неплановую консультацию',
        'applicationConsultationButton' => 'Заявка на консультацию',
        'applicationConsultationReason' => 'Напишите причину обращения',
        'applicationConsultationPlaceholder' => 'Кровотечение',
        'toConfirm' => NotificationTemplate::NEWS_LIST_CONTENT['toConfirm'],
        'nowDay' => 'сегодняшний день',
        'eventDay' => 'день обследования',
        'unchangedPrescription' => PrescriptionTemplate::COMMON_CONTENT['unchangedPrescription'],
        'prescriptionTesting' => PrescriptionTemplate::COMMON_CONTENT['prescriptionTesting'],
        'prescriptionAppointment' => PrescriptionTemplate::COMMON_CONTENT['prescriptionAppointment'],
        'prescriptionMedicine' => PrescriptionTemplate::COMMON_CONTENT['prescriptionMedicine'],
        'pressureWidgetTitle' => 'Давление',
        'pulseWidgetTitle' => 'Пульс',
        'weightWidgetTitle' => 'Вес',
        'pressureWidgetCap' => '??',
        'pulseWidgetCap' => '??/??',
        'weightWidgetCap' => '??',
    ];

    /**
     * @param RouteCollection $routeCollection
     * @param string $className
     * @throws Exception
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct($routeCollection, $className);
        $this->addContent(
            self::LIST_CONTENT,
            null,
            self::SHOW_CONTENT
        );
    }
}