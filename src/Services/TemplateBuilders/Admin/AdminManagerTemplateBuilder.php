<?php

namespace App\Services\TemplateBuilders\Admin;

use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\ShowTemplateItem;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class AdminManagerTemplate
 * builder of templates AdminManager
 *
 * @package App\Services\TemplateBuilders
 */
class AdminManagerTemplateBuilder extends AdminTemplateBuilder
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
        'h1' => 'Менеджеры панели администрирования',
        'title' => 'Список менеджеров',
        'fio' => 'ФИО'
    ];

    /** @var string[] Common new content for staff templates */
    public const NEW_CONTENT = [
        'h1' => 'Новый менеджер панели администрирования',
        'title' => 'Новый менеджер панели администрирования',
    ];

    /** @var string[] Common show content for staff templates */
    public const SHOW_CONTENT = [
        'title' => 'Данные менеджера панели администрирования',
    ];

    /** @var string[] Common edit content for staff templates */
    public const EDIT_CONTENT = [
        'title' => 'Редактирование менеджера панели администрирования',
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
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        RouteCollection $routeCollection,
        string $className,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($routeCollection, $className, $authorizationChecker);
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
     * Builds show template
     *
     * @param object|null $entity
     *
     * @return AppTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show($entity);
        $authUserInfoService = new AuthUserInfoService();
        $this->getItem(ShowTemplateItem::TEMPLATE_ITEM_SHOW_NAME)
            ->setContent('h1', 'Просмотр данных менеджера: '.$authUserInfoService->getFIO($entity, true));
        return $this;
    }

    /**
     * Builds edit template
     *
     * @param object|null $entity
     *
     * @return AdminTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $authUserInfoService = new AuthUserInfoService();
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
            ->setContent('h1', 'Редактирование менджера: '.$authUserInfoService->getFIO($entity, true));
        return $this;
    }


}