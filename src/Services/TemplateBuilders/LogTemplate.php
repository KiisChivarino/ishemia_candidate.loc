<?php

namespace App\Services\TemplateBuilders;

use App\Services\FilterService\FilterService;
use App\Services\TemplateItems\NewTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AnalysisGroupTemplate
 * builds template settings for AnalysisGroup controller
 *
 * @package App\Services\TemplateBuilders
 */
class LogTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for staff templates */
    protected const COMMON_CONTENT = [
        'fullName' => 'Полное название',
        'userString' => 'Пользователь',
        'logAction' => 'Тип лога',
        'createdAt' => 'Дата и время создания'
    ];

    /** @var string[] Common form content for staff templates */
    protected const FORM_CONTENT = [
        'hospitalPlaceholder' => 'Выберите больницу',
    ];

    /** @var string[] Common list content for staff templates */
    protected const LIST_CONTENT = [
        'h1' => 'Список логов',
        'title' => 'Логи',
    ];

    /** @var string[] Common new content for staff templates */
    protected const NEW_CONTENT = [
        'h1' => 'Новый лог',
        'title' => 'Новый лог',
    ];
    /** @var string[] Common show content for staff templates */
    protected const SHOW_CONTENT = [
        'logs' => 'Логи',
    ];

    /** @var string[] Common edit content for staff templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование лога',
        'title' => 'Редактирование лога',
    ];

    /**
     * CountryTemplate constructor.
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
    public function list(?FilterService $filterService = null): AdminTemplateBuilder
    {
        parent::list($filterService);
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
            ->setIsEnabled(false);

        return $this;
    }

//    /**
//     *  Builds show template settings of AnalysisGroup controller
//     *
//     * @param object|null $analysisGroup
//     *
//     * @return $this
//     */
//    public function show(?object $analysisGroup = null): AdminTemplateBuilder
//    {
//        parent::show($analysisGroup);
//        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
//            ->setContent('title', 'Лог '.$analysisGroup->getName())
//            ->setContent('h1', 'Лог '.$analysisGroup->getName());
//        return $this;
//    }
}