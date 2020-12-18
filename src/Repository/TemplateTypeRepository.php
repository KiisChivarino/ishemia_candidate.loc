<?php

namespace App\Repository;

use App\Entity\TemplateType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TemplateTypeRepository
 * @method TemplateType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateType[]    findAll()
 * @method TemplateType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class TemplateTypeRepository extends AppRepository
{
    /**
     * TemplateTypeRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateType::class);
    }
}
