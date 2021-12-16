<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\ChannelType;
use App\Entity\Hospital;
use App\Repository\ChannelTypeRepository;
use App\Repository\HospitalRepository;
use App\Services\FilterService\FilterService;
use App\Services\Template\TemplateFilter;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\EditTemplateItem;
use App\Services\TemplateItems\FilterTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use App\Services\TemplateItems\TableActionsTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class NotificationTemplate
 *
 * @package App\Services\TemplateBuilders
 */
class NotificationTemplate extends DoctorOfficeTemplateBuilder
{
    /**
     * NotificationTemplate constructor.
     *
     * @param RouteCollection $routeCollection
     * @param string $className
     */
    public function __construct(RouteCollection $routeCollection, string $className)
    {
        parent::__construct($routeCollection, $className);
        $this->addContent(
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::LIST_CONTENT,
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::NEW_CONTENT,
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::SHOW_CONTENT,
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::EDIT_CONTENT,
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::FORM_CONTENT,
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::FORM_SHOW_CONTENT,
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT,
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::FILTER_CONTENT,
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::ENTITY_CONTENT
        );
    }

    /**
     * @param FilterService|null $filterService
     *
     * @return AppTemplateBuilder
     * @throws Exception
     */
    public function list(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::list();
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)
            ->setIsEnabled(false);
        $this->getItem(TableActionsTemplateItem::TEMPLATE_ITEM_SHOW_ACTIONS_NAME)
            ->setIsEnabled(false);
        $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
            ->setFilters(
                $filterService,
                [
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['CHANNEL_TYPE'],
                        ChannelType::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                                ->getContentValue('channelTypeFilter'),
                            'class' => ChannelType::class,
                            'required' => false,
                            'choice_label' => function ($value) {
                                return $value->getName();
                            },
                            'query_builder' => function (ChannelTypeRepository $er) {
                                return $er->createQueryBuilder('cT');
                            },
                        ]
                    ),
                    new TemplateFilter(
                        AppAbstractController::FILTER_LABELS['HOSPITAL'],
                        Hospital::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                                ->getContentValue('hospitalFilter'),
                            'class' => Hospital::class,
                            'required' => false,
                            'choice_label' => function ($value) {
                                return $value->getName();
                            },
                            'query_builder' => function (HospitalRepository $er) {
                                return $er->createQueryBuilder('h')
                                    ->andWhere('h.enabled = true');
                            },
                        ]
                    ),
                ]
            );
        return $this;
    }
    /**
     * Builds edit template settings of Patient controller
     *
     * @param object|null $entity
     *
     * @return $this|DoctorOfficeTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        return $this;
    }

    /**
     * Builds show template settings of Patient controller
     *
     * @param object|null $entity
     *
     * @return $this|DoctorOfficeTemplateBuilder
     */
    public function show(?object $entity = null): AppTemplateBuilder
    {
        parent::show($entity);
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
            ->setIsEnabled(false);
        return $this;
    }
}