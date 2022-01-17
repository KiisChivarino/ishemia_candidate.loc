<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FormTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AuthUserTemplate
 * builder of templates AuthUser
 *
 * @package App\Services\TemplateBuilders
 */
class AuthUserTemplate extends AdminTemplateBuilder
{
    /** @var string[] Common content for AuthUser templates */
    public const COMMON_CONTENT = [
        'phone' => 'Телефон',
        'email' => 'Email',
        'role' => 'Роль',
    ];

    /** @var string[] Common content for view, edit templates */
    public const FORM_SHOW_CONTENT = [
        'lastName' => 'Фамилия',
        'firstName' => 'Имя',
        'patronymicName' => 'Отчество',
    ];

    /** @var string[] Common form content for staff templates */
    public const FORM_CONTENT = [
        'password' => 'Пароль',
        'passwordHelp' => 'Введите пароль не менее 6 знаков, включая английские символы, спецсимволы и цифры',
        'phoneHelp' => 'Введите телефон 10 цифр',
        'description' => 'Описание пользователя',
    ];

    /** @var string[] Common list content for staff templates */
    public const LIST_CONTENT = [
        'h1' => 'Пользователи',
        'title' => 'Список пользователей',
        'fio' => 'ФИО'
    ];

    /** @var string[] Common new content for staff templates */
    public const NEW_CONTENT = [
        'h1' => 'Новый пользователь',
        'title' => 'Новый пользователь',
    ];

    /** @var string[] Common show content for staff templates */
    public const SHOW_CONTENT = [
        'title' => 'Данные пользователя',
    ];

    /** @var string[] Common edit content for staff templates */
    public const EDIT_CONTENT = [
        'title' => 'Редактирование пользователя',
    ];

    /** @var string[] Common filter content for staff templates */
    public const FILTER_CONTENT = [
        'analysisGroupFilter' => 'Фильтр группы анализов',
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => 'Пользователь',
    ];

    /**
     * CountryTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     *
     * @throws Exception
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
            self::COMMON_CONTENT,
            self::FILTER_CONTENT,
            self::ENTITY_CONTENT
        );
    }

    /**
     * Builds list template
     *
     * @param FilterService|null $filterService
     * @param array|null $itemsWithRoutes
     *
     * @return AppTemplateBuilder
     * @throws Exception
     */
    public function list(?FilterService $filterService = null, ?array $itemsWithRoutes = null): AppTemplateBuilder
    {
        parent::list($filterService);
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
            ->setIsEnabled(false);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        return $this;
    }

    /**
     * Builds show template
     *
     * @param object|null $authUser
     *
     * @return AppTemplateBuilder
     */
    public function show(?object $authUser = null): AppTemplateBuilder
    {
        parent::show($authUser);
        $authUserInfoService = new AuthUserInfoService();
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->setContent('h1', 'Просмотр данных пользователя: ' . $authUserInfoService->getFIO($authUser, true));
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        return $this;
    }

    /**
     * Builds edit template
     *
     * @param object|null $entity
     *
     * @return AdminTemplateBuilder
     * @throws Exception
     */
    public function edit(?object $authUser = null): AppTemplateBuilder
    {
        parent::edit($authUser);
        $authUserInfoService = new AuthUserInfoService();
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
            ->setContent('h1', 'Редактирование пользователя: ' . $authUserInfoService->getFIO($authUser, true));
        $this->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME)
            ->setPath($this->getTemplatePath());
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        return $this;
    }
}