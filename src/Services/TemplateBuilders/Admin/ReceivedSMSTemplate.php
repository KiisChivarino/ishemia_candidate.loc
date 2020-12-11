<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AnalysisGroupTemplate
 * builds template settings for AnalysisGroup controller
 *
 * @package App\Services\TemplateBuilders
 */
class ReceivedSMSTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for staff templates */
    protected const COMMON_CONTENT = [
        'fullName' => 'Полное название',
        'userString' => 'Пользователь',
        'logAction' => 'Тип лога',
        'createdAt' => 'Дата и время создания',
        'patient' => 'Пациент',
        'phone' => 'Номер телефона',
        'text' => 'Сообщение',
        'created_at' => 'Дата и время создания',
        'processed' => 'Обработано'
    ];

    /** @var string[] Common form content for staff templates */
    protected const FORM_CONTENT = [
        'hospitalPlaceholder' => 'Выберите больницу',
    ];

    /** @var string[] Common list content for staff templates */
    protected const LIST_CONTENT = [
        'h1' => 'Список полученных SMS',
        'title' => 'Логи',
    ];

    /** @var string[] Common new content for staff templates */
    protected const NEW_CONTENT = [
        'h1' => 'Новый лог',
        'title' => 'Новый лог',
    ];
    /** @var string[] Common show content for staff templates */
    protected const SHOW_CONTENT = [
        'logs' => 'SMS',
    ];

    /** @var string[] Common edit content for staff templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование SMS',
        'title' => 'Редактирование SMS',
    ];

    /**
     * Received SMS constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     */
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
            self::COMMON_CONTENT
        );
    }

    /**
     * @param FilterService|null $filterService
     *
     * @return $this|AdminTemplateBuilder
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list($filterService);
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
            ->setIsEnabled(false);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->setIsEnabled(false);

        return $this;
    }

    /**
     * Builds edit template settings of Patient controller
     *
     * @param object|null $entity
     *
     * @return $this|AdminTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->addContentArray(
                array_merge(
                    AuthUserTemplate::COMMON_CONTENT,
                    AuthUserTemplate::FORM_CONTENT,
                    AuthUserTemplate::FORM_SHOW_CONTENT
                )
            );
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        return $this;
    }
}