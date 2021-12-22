<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Entity\TemplateRoutes;
use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;
use RuntimeException;

/**
 * Class AdminTemplateBuilder
 *
 * @package App\Services\TemplateBuilders
 */
abstract class AdminTemplateBuilder extends AppTemplateBuilder
{
    /** @var string Default redirect route name */
    public const DEFAULT_REDIRECT_ROUTE_NAME = 'show';

    /** @var string Путь к общим шаблонам crud админки */
    public const DEFAULT_COMMON_TEMPLATE_PATH = 'admin/common_template/';

    /**
     * CountryTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     * @throws Exception
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct(
            $routeCollection,
            $className,
            self::DEFAULT_COMMON_TEMPLATE_PATH,
            self::DEFAULT_REDIRECT_ROUTE_NAME
        );
    }

    public function list(?FilterService $filterService = null, ?array $itemsWithRoutes = null): AppTemplateBuilder
    {
        parent::list($filterService);

        $this->setTemplateItems($this->templateItemsFactory->getListTemplateItems());

        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)
            ->addContentArray($this->commonContent)
            ->addContentArray($this->listContent)
            ->addContentArray($this->entityContent);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->addContentArray($this->filterContent);

        $defaultItemsWithRoutes = [
            (new TemplateRoutes())->addTemplateRoute(
                'show',
                ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME
            ),
            (new TemplateRoutes())->addTemplateRoute(
                'edit',
                EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME
            ),
            (new TemplateRoutes())->addTemplateRoute(
                'delete',
                DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME
            ),
        ];

        $arrObjItemsWithRoutes = $itemsWithRoutes ?? $defaultItemsWithRoutes;

        foreach ($arrObjItemsWithRoutes as $routeItem){
            if (array_key_exists($routeItem->getRouteKey(), $this->getRoutes())){
                $this->getItem($routeItem->getItemName())
                    ->getTemplateItemRoute()
                    ->setRouteName($this->getRoute($routeItem->getRouteKey()));
            } else {
                throw new RuntimeException(
                    'В контроллере не найден метод с именем "' .
                    $routeItem->getRouteKey() .
                    '" для элемента шаблона "' .
                    $routeItem->getItemName() .
                    '" из таблички'
                );
            }
        }

        return $this;
    }
}