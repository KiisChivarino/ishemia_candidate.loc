<?php

namespace App\Services\DataTable\Admin;

use App\Entity\BlogItem;
use App\Entity\BlogRecord;
use App\Services\InfoService\BlogRecordInfoService;
use App\Services\TemplateItems\ListTemplateItem;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;

/**
 * Class BlogItemDataTableService
 *
 * @package App\Services\DataTable\Admin
 */
class BlogItemDataTableService extends AdminDatatableService
{
    /**
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
                'blogRecord', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('blogRecord'),
                    'render' => function ($dataString, BlogItem $blogItem) {
                        /** @var BlogRecord $blogRecord */
                        $blogRecord = $blogItem->getBlogRecord();
                        return $blogRecord ? $this->getLink(
                            (new BlogRecordInfoService())->getBlogRecordTitle($blogRecord),
                            $blogRecord->getId(),
                            'blog_record_show'
                        ) : '';
                    }
                ]
            )
            ->add(
                'project', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('project'),
                ]
            )
            ->add(
                'title', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('itemTitle'),
                ]
            )
            ->add(
                'duration', NumberColumn::class, [
                    'label' => $listTemplateItem->getContentValue('duration'),
                ]
            )
            ->add(
                'completed', BoolColumn::class, [
                    'label' => $listTemplateItem->getContentValue('completed'),
                    'trueValue' => $listTemplateItem->getContentValue('trueValue'),
                    'falseValue' => $listTemplateItem->getContentValue('falseValue'),
                    'searchable' => false,
                ]
            );
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => BlogItem::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('bi')
                            ->from(BlogItem::class, 'bi');
                    },
                ]
            );
    }
}