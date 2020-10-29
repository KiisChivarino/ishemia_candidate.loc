<?php

namespace App\Services\DataTable\Admin;

use App\Entity\AnalysisGroup;
use App\Entity\TemplateParameter;
use App\Entity\TemplateParameterText;
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
class TemplateParameterTextDataTableService extends AdminDatatableService
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
                'text', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('text')
                ]
            )
            ->add(
                'templateParameter', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('templateParameter'),
                    'render' => function (string $data, TemplateParameterText $templateParameterText) {
                        /** @var TemplateParameter $templateParameter */
                        $templateParameter = $templateParameterText->getTemplateParameter();
                        return
                            $templateParameter ?
                                $this->getLink($templateParameter->getName(), $templateParameter->getId(), 'template_parameter_show')
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
                    'entity' => TemplateParameterText::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('t')
                            ->from(TemplateParameterText::class, 't');
                    },
                ]
            );
    }
}