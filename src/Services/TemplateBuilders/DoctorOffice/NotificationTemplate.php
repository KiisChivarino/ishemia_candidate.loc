<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Controller\AppAbstractController;
use App\Entity\Patient;
use App\Repository\PatientRepository;
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
                        AppAbstractController::FILTER_LABELS['PATIENT'],
                        Patient::class,
                        [
                            'label' => $this->getItem(FilterTemplateItem::TEMPLATE_ITEM_FILTER_NAME)
                                ->getContentValue('patientFilter'),
                            'class' => Patient::class,
                            'required' => false,
                            'choice_label' => function ($value) {
                                return $value->getAuthUser()->getLastName() . ' ' . $value->getAuthUser()->getFirstName();
                            },
                            'query_builder' => function (PatientRepository $er) {
                                return $er->createQueryBuilder('p')
                                    ->leftJoin('p.AuthUser', 'au')
                                    ->where('au.enabled = true');
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
        parent::show();
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)
            ->setIsEnabled(false);
        $this->getItem(EditTemplateItem::TEMPLATE_ITEM_EDIT_NAME)
            ->setIsEnabled(false);
        return $this;
    }
}