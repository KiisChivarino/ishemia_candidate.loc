<?php

namespace App\Services\TemplateBuilders\PatientOffice;

use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\ShowTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class ArticleTemplate
 *
 * @package App\Services\TemplateBuilders\PatientOffice
 */
class ArticleTemplate extends PatientOfficeTemplateBuilder
{
    /** @var array */
    public const COMMON_CONTENT = [
        'read' => 'Читать',
    ];

    /** @var array */
    protected const SHOW_CONTENT = [
        'title' => 'Название статьи',
        'h1' => 'Информация',
        'publishedAt' => 'Опубликовано',
        'readMore' => 'Смотрите также'
    ];

    /** @var array */
    protected const LIST_CONTENT = [
        'title' => 'Информация',
        'h1' => 'Информация',
        'read' => 'Читать'
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
            self::SHOW_CONTENT,
            null,
            null,
            null,
            self::COMMON_CONTENT
        );
    }

    /**
     * @param object|null $entity
     * @return AppTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show($entity);
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->setContent('title', 'Название статьи');

        return $this;
    }
}