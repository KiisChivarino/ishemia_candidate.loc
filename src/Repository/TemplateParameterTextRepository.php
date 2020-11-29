<?php

namespace App\Repository;

use App\Entity\TemplateParameterText;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TemplateParameterText|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateParameterText|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateParameterText[]    findAll()
 * @method TemplateParameterText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateParameterTextRepository extends AppRepository
{
    /**
     * TemplateParameterTextRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateParameterText::class);
    }
}
