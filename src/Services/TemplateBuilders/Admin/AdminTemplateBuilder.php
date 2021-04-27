<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


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

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;



    /**
     * CountryTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        RouteCollection $routeCollection,
        string $className,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct(
            $routeCollection,
            $className,
            self::DEFAULT_COMMON_TEMPLATE_PATH,
            self::DEFAULT_REDIRECT_ROUTE_NAME
        );
        $this->authorizationChecker = $authorizationChecker;


    }

    protected function onlyAdminAccessDelete(){
        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
                ->setIsEnabled(false);
        }
    }

    protected function onlyAdminAccessEdit(){
        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
                ->setIsEnabled(false);
        }
    }

    protected function onlyAdminAccessAdded(){
        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
                ->setIsEnabled(false);
        }
    }

    /**
     * Builds list
     * @param FilterService|null $filterService
     * @return AppTemplateBuilder
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list($filterService);

        $this->onlyAdminAccessDelete();
        return $this;
    }

    /**
     * Builds show
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show();

        $this->onlyAdminAccessDelete();

        return $this;
    }

    /**
     * Builds eid
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();

        $this->onlyAdminAccessDelete();

        return $this;
    }

}