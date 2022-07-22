<?php

namespace App\Services\DataTable;

use App\Services\Template\TemplateItem;
use Closure;
use Exception;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;

/**
 * Class DataTableService
 * глобальные свойства сервисов datatable
 *
 * @package App\Services\DataTable
 */
abstract class DataTableService
{
    /** @var DataTableFactory $dataTableFactory */
    protected $dataTableFactory;

    /** @var DataTable $dataTable */
    protected $dataTable;

    /**
     * Паттерн для вывода редактируемой ячейки дататейбла
     * @var
     */
    public const TABLE_EDIT_PATTERN = '<div id="entity%d" data-url="%s" class="xEditable">%s</div>';

    /**
     * DataTableService constructor.
     *
     * @param DataTableFactory $dataTableFactory
     */
    public function __construct(DataTableFactory $dataTableFactory)
    {
        $this->dataTableFactory = $dataTableFactory;
        $this->dataTable = $this->dataTableFactory->create();
    }

    /**
     * Добавляет поле с порядковым номером
     *
     * @return DataTable
     */
    protected function addSerialNumber(): DataTable
    {
        return $this->dataTable
            ->add(
                'serialNumber', TextColumn::class, [
                    'label' => '№',
                    'data' => '1'
                ]
            );
    }

    /**
     * Добавляет поле с флагом ограничения использования
     *
     * @param TemplateItem $templateItem
     * @param string $prefix
     *
     * @return DataTable
     * @throws Exception
     */
    protected function addEnabled(TemplateItem $templateItem, string $prefix = ''): DataTable
    {
        $addParameters = [
            'trueValue' => $templateItem->getContentValue('trueValue'),
            'falseValue' => $templateItem->getContentValue('falseValue'),
            'label' => $templateItem->getContentValue('enabled'),
            'searchable' => false,
        ];
        if ($prefix) {
            $addParameters['field'] = $prefix . '.enabled';
        }
        return $this->dataTable
            ->add('enabled', BoolColumn::class, $addParameters);
    }

    /**
     * Добавляет поле с операциями
     *
     * @param Closure $renderOperationsFunction
     *
     * @param TemplateItem $templateItem
     * @return DataTable
     * @throws Exception
     */
    protected function addOperations(Closure $renderOperationsFunction, TemplateItem $templateItem): DataTable
    {
        return $this->dataTable
            ->add(
                'operations', TextColumn::class, [
                    'label' => $templateItem->getContentValue('operations'),
                    'className' => 'dataTableOperations',
                    'render' => $renderOperationsFunction,
                    'field' => 'e.id',
                    'searchable' => false
                ]
            );
    }

    /**
     * Добавляет поле с операциями c возможностью добавления параметров
     * Клоушура используется для render, чтобы можно было задать сколь угодно много динамических параметров
     * Клоушура задается в DataTable сервисе
     *
     * @param TemplateItem $templateItem
     * @param Closure $renderFunction
     * @return DataTable
     * @throws Exception
     */
    protected function addOperationsWithParameters(
        TemplateItem $templateItem,
        Closure $renderFunction
    ): DataTable
    {
        return $this->dataTable
            ->add(
                'operations', TextColumn::class, [
                    'label' => $templateItem->getContentValue('operations'),
                    'className' => 'dataTableOperations',
                    'render' => $renderFunction,
                    'field' => 'e.id',
                    'searchable' => false
                ]
            );
    }

    /**
     * Get link
     *
     * @param string $value
     * @param int $id
     * @param string $route
     *
     * @return string
     */
    protected function getLink(string $value, int $id, string $route): string
    {
        return '<a href="' . $this->router->generate($route, ['id' => $id]) . '">' . $value . '</a>';
    }

    /**
     * Get link
     *
     * @param string $linkValue
     * @param array $parameters
     * @param string $route
     *
     * @return string
     */
    protected function getLinkMultiParam(string $linkValue, array $parameters, string $route): string
    {
        return '<a href="' . $this->router->generate($route, $parameters) . '">' . $linkValue . '</a>';
    }
}