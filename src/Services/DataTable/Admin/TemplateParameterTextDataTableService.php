<?php

namespace App\Services\DataTable\Admin;

use App\Controller\AppAbstractController;
use App\Entity\TemplateParameter;
use App\Entity\TemplateParameterText;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Exception;
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
     * @param array $filters
     * @return DataTable
     * @throws Exception
     */
    public function getTable(
        Closure $renderOperationsFunction,
        ListTemplateItem $listTemplateItem,
        array $filters
    ): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'templateParameter', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('templateParameter'),
                    'render' => function (string $data, TemplateParameterText $templateParameterText) {
                        /** @var TemplateParameter $templateParameter */
                        $templateParameter = $templateParameterText->getTemplateParameter();
                        return
                            $templateParameter ?
                                $this->getLink(
                                    $templateParameter->getName(),
                                    $templateParameter->getId(),
                                    'template_parameter_show'
                                )
                                : '';
                    }
                ]
            )
            ->add(
                'text', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('text')
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        /** @var TemplateParameter $templateParameter */
        $templateParameter = $filters[AppAbstractController::FILTER_LABELS['TEMPLATE_PARAMETER']];
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => TemplateParameterText::class,
                    'query' => function (QueryBuilder $builder)
                    use ($templateParameter) {
                        $builder
                            ->select('tpt')
                            ->from(TemplateParameterText::class, 'tpt');
                        if ($templateParameter) {
                            $builder
                                ->andWhere('tpt.templateParameter = :valTemplateParameter')
                                ->setParameter('valTemplateParameter', $templateParameter);
                        }
                    },
                ]
            );
    }
}