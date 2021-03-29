<?php

namespace App\Services\DataTable\Admin;

use App\Entity\AuthUser;
use App\Entity\Role;
use App\Services\InfoService\AuthUserInfoService;
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
class AuthUserDataTableService extends AdminDatatableService
{

    /**
     * Таблица диагнозов в админке
     *
     * @param Closure $renderOperationsFunction
     * @param ListTemplateItem $listTemplateItem
     *
     * @return DataTable
     * @throws Exception
     */
    public function getTable(Closure $renderOperationsFunction, ListTemplateItem $listTemplateItem): DataTable
    {
        $this->addSerialNumber();
        $this->dataTable
            ->add(
                'email', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('email'),
                    'data' => function ($value) {
                        return 
$value->getEmail() 
? $value->getEmail() 
: $listTemplateItem->getContentValue('empty');
                    },
                ]
            )
            ->add(
                'phone', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('phone'),
                ]
            )
            ->add(
                'roles', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('role'),
                    'data' => function ($value) {
                        return $this->entityManager->getRepository(Role::class)
                            ->findOneBy(['tech_name' => $value->getRoles()[0]])->getId();
                    },
                    'render' => function ($dataString) {
                        /** @var Role $role */
                        $role = $this->entityManager->getRepository(Role::class)
                            ->find($dataString);
                        return $role ? $this->getLink($role->getName(), $dataString, 'role_show') : '';
                    }
                ]
            )
            ->add(
                'lastName', TextColumn::class, [
                    'visible' => false
                ]
            )
            ->add(
                'firstName', TextColumn::class, [
                    'visible' => false
                ]
            )
            ->add(
                'patronymicName', TextColumn::class, [
                    'visible' => false
                ]
            )
            ->add(
                'fio', TextColumn::class, [
                    'label' => $listTemplateItem->getContentValue('fio'),
                    'data' => function ($value) {
                        return (new AuthUserInfoService())->getFIO($value);
                    }
                ]
            );
        $this->addEnabled($listTemplateItem);
        $this->addOperations($renderOperationsFunction, $listTemplateItem);
        return $this->dataTable
            ->createAdapter(
                ORMAdapter::class, [
                    'entity' => AuthUser::class,
                    'query' => function (QueryBuilder $builder) {
                        $builder
                            ->select('au')
                            ->from(AuthUser::class, 'au');
                    },
                ]
            );
    }
}