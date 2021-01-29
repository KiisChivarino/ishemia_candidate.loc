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
    /** @var string[] Common content for analysis templates */
    protected const COMMON_CONTENT = [
        'notificationType' =>
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT['notificationType'],
        'staff' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT['staff'],
        'notificationTime' =>
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT['notificationTime'],
        'text' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT['text'],
        'patient' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT['patient'],
        'authUserSender' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT['authUserSender'],
        'smsNotification' =>
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT['smsNotification'],
        'notificationReceiverType' =>
            \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT['notificationReceiverType'],
        'receiver' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT['receiver'],
        'medicalHistory' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT['medicalHistory'],
        'medicalRecord' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT['medicalRecord'],
        'channelType' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::COMMON_CONTENT['channelType'],
    ];
    /** @var string[] Common FORM_SHOW_CONTENT */
    protected const FORM_SHOW_CONTENT = [
        'text' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::FORM_SHOW_CONTENT['text'],
    ];

    /** @var string[] Common LIST_CONTENT */
    protected const LIST_CONTENT = [
        'h1' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::LIST_CONTENT['h1'],
        'title' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::LIST_CONTENT['title'],
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::NEW_CONTENT['h1'],
        'title' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::NEW_CONTENT['title'],
    ];

    /** @var string[] Common SHOW_CONTENT */
    protected const SHOW_CONTENT = [
        'h1' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::SHOW_CONTENT['h1'],
        'title' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::SHOW_CONTENT['title'],
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::EDIT_CONTENT['h1'],
        'title' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::EDIT_CONTENT['title'],
    ];

    protected const FILTER_CONTENT = [
        'patientFilter' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::FILTER_CONTENT['patientFilter'],
    ];

    /** @var string[] Common ENTITY CONTENT */
    public const ENTITY_CONTENT = [
        'entity' => \App\Services\TemplateBuilders\Admin\NotificationTemplate::ENTITY_CONTENT['entity'],
    ];

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