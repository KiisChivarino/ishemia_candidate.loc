<?php

namespace App\Services\DataTable\Admin;

use App\Services\DataTable\DataTableService;
use App\Services\Template\TemplateItem;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AdminDatatableService
 * содержит повторяющиеся методы для сервисов datatable админки
 *
 * @package App\Services\DataTable\Admin
 */
abstract class AdminDatatableService extends DataTableService
{
    /** @var UrlGeneratorInterface|null $router */
    protected $router;

    /** @var EntityManagerInterface|null $entityManager */
    protected $entityManager;

    /**
     * AdminDatatableService constructor.
     *
     * @param DataTableFactory $dataTableFactory
     * @param UrlGeneratorInterface|null $router
     * @param EntityManagerInterface|null $entityManager
     */
    public function __construct(DataTableFactory $dataTableFactory, UrlGeneratorInterface $router = null, EntityManagerInterface $entityManager = null)
    {
        parent::__construct($dataTableFactory);
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    /**
     * Добавляет поле с порядковым номером
     *
     * @return DataTable
     */
    protected function addSerialNumber()
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
     */
    protected function addEnabled(TemplateItem $templateItem, string $prefix = '')
    {
        $addParameters = [
            'trueValue' => $templateItem->getContentValue('trueValue'),
            'falseValue' => $templateItem->getContentValue('falseValue'),
            'label' => $templateItem->getContentValue('enabled'),
            'searchable' => false,
        ];
        if ($prefix) {
            $addParameters['field'] = $prefix.'.enabled';
        }
        return $this->dataTable
            ->add('enabled', BoolColumn::class, $addParameters);
    }

    /**
     * Добавляет поле с операциями
     *
     * @param Closure $renderOperationsFunction
     *
     * @return DataTable
     */
    protected function addOperations(Closure $renderOperationsFunction, TemplateItem $templateItem)
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
     * Get link
     *
     * @param string $value
     * @param int $id
     * @param string $route
     *
     * @return string
     */
    protected function getLink(string $value, int $id, string $route)
    {
        return '<a href="'.$this->router->generate($route, ['id' => $id]).'">'.$value.'</a>';
    }
}