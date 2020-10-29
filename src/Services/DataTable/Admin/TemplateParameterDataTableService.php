<?php

namespace App\Services\DataTable\Admin;

use App\Entity\AnalysisGroup;
use App\Entity\TemplateParameter;
use App\Entity\TemplateType;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class DataTableService
 * methods for adding data tables
 *
 * @package App\DataTable
 */
class TemplateParameterDataTableService extends AdminDatatableService
{
    /**
     * Таблица типов параметров в админке
     *
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     *
     * @return DataTable
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'name', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('name')
                ]
            )
            ->add(
                'templateType', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('templateType'),
                    'render' => function (string $data, TemplateParameter $templateParameter) {
                        /** @var TemplateType $templateType */
                        $templateType = $templateParameter->getTemplateType();
                        return
                            $templateType ?
                                $this->getLink($templateType->getName(), $templateType->getId(), 'template_type_show')
                                : '';
                    }
                ]
            )
           ;
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => TemplateParameter::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('t')
                            ->from(TemplateParameter::class, 't');
                    },
                ]
            );
    }
}