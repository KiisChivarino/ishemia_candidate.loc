<?php

namespace App\Services\TemplateBuilders;

use App\Services\TemplateItems\ShowTemplateItem;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AnalysisGroupTemplate
 * builds template settings for AnalysisGroup controller
 *
 * @package App\Services\TemplateBuilders
 */
class AnalysisGroupTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for staff templates */
    protected const COMMON_CONTENT = [
        'fullName' => 'Полное название'
    ];

    /** @var string[] Common form content for staff templates */
    protected const FORM_CONTENT = [
        'hospitalPlaceholder' => 'Выберите больницу',
    ];

    /** @var string[] Common list content for staff templates */
    protected const LIST_CONTENT = [
        'h1' => 'Список групп анализов',
        'title' => 'Группы анализов',
    ];

    /** @var string[] Common new content for staff templates */
    protected const NEW_CONTENT = [
        'h1' => 'Новая группа анализов',
        'title' => 'Новая группа анализов',
    ];
    /** @var string[] Common show content for staff templates */
    protected const SHOW_CONTENT = [
        'analyzesLink' => 'Анализы',
    ];

    /** @var string[] Common edit content for staff templates */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактрование группы анализов',
        'title' => 'Редактрование группы анализов',
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
     *  Builds show template settings of AnalysisGroup controller
     *
     * @param object|null $analysisGroup
     *
     * @return $this
     */
    public function show(?object $analysisGroup = null): AdminTemplateBuilder
    {
        parent::show($analysisGroup);
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->setContent('title', 'Группа анализов '.$analysisGroup->getName())
            ->setContent('h1', 'Группа анализов '.$analysisGroup->getName());
        return $this;
    }
}