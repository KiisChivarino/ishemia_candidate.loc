<?php

namespace App\Services\TemplateBuilders\DoctorOffice;

use App\Services\FilterService\FilterService;
use App\Services\TemplateBuilders\AppTemplateBuilder;
use App\Services\TemplateItems\DeleteTemplateItem;
use App\Services\TemplateItems\ListTemplateItem;
use App\Services\TemplateItems\NewTemplateItem;
use Exception;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PatientTestingTemplate
 * prepare template data for patient testing list
 *
 * @package App\Services\TemplateBuilders
 */
class PatientMedicineTemplate extends DoctorOfficeTemplateBuilder
{

    /** @var string[] Common content for PatientTesting templates */
    public const COMMON_CONTENT = [
        'medicineName' => 'Название лекарства',
        'instruction' => 'Инструкция по применению',
        'dateBegin' => 'Дата начала приема',
        'startingMedicationDate' => 'Дата начала приема лекарства',
        'endMedicationDate' => 'Дата окончания приема лекарства',
        'inclusion_time' => 'Дата и время включения в назначение',
        'staff' => 'Врач создавший назначение'
    ];

    /** @var string[] Common NEW_CONTENT */
    protected const NEW_CONTENT = [
        'h1' => 'Добавление лекарства',
        'title' => 'Добавление лекарства',
    ];

    /** @var string[] Common EDIT_CONTENT */
    protected const EDIT_CONTENT = [
        'h1' => 'Редактирование лекарства',
        'title' => 'Редактирование лекарства',
    ];

    /**
     * PatientTestingTemplate constructor.
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
     * @return $this|DoctorOfficeTemplateBuilder
     */
    public function new(?FilterService $filterService = null): AppTemplateBuilder
    {
        parent::new($filterService);
        $this->getItem(ListTemplateItem::TEMPLATE_ITEM_LIST_NAME)->setIsEnabled(false);
        return $this;
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
        $this->getItem(NewTemplateItem::TEMPLATE_ITEM_NEW_NAME)->setIsEnabled(false);
        return $this;
    }


    /**
     * @param object|null $entity
     * @return $this|AppTemplateBuilder
     */
    public function edit(?object $entity = null): AppTemplateBuilder
    {
        parent::edit();
        $this->getItem(DeleteTemplateItem::TEMPLATE_ITEM_DELETE_NAME)->setIsEnabled(false);
        return $this;
    }
}