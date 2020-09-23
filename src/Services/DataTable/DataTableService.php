<?php

namespace App\Services\DataTable;

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
     * DataTableService constructor.
     *
     * @param DataTableFactory $dataTableFactory
     */
    public function __construct(DataTableFactory $dataTableFactory)
    {
        $this->dataTableFactory = $dataTableFactory;
        $this->dataTable = $this->dataTableFactory->create();
    }
}