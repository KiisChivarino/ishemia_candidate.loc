<?php

namespace App\Repository;

use App\Entity\TemplateManyToManyTemplateParameterText;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TemplateManyToManyTemplateParameterTextRepository
 * @method TemplateManyToManyTemplateParameterText|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateManyToManyTemplateParameterText|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateManyToManyTemplateParameterText[]    findAll()
 * @method TemplateManyToManyTemplateParameterText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class TemplateManyToManyTemplateParameterTextRepository extends ServiceEntityRepository
{
    /**
     * TemplateManyToManyTemplateParameterTextRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateManyToManyTemplateParameterText::class);
    }
}
