<?php

namespace App\Services\TemplateBuilders\PatientOffice;

use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class NotificationTemplate
 *
 * @package App\Services\TemplateBuilders\PatientOffice
 */
class NotificationTemplate extends PatientOfficeTemplateBuilder
{
    /** @var string[] Common notifications content  */
    public const COMMON_CONTENT = [
        'notificationWith' => 'Уведомление от'
    ];

    /** @var string[] New notifications content */
    public const NEWS_LIST_CONTENT = [
        'title' => 'Новые уведомления',
        'h1' => 'Новые уведомления',
        'toConfirm' => 'Подтвердить'
    ];

    /** @var string[] Old notifications content */
    public const HISTORY_LIST_CONTENT = [
        'title' => 'История уведомлений',
        'h1' => 'История уведомлений'
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
