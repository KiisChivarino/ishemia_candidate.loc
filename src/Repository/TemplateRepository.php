<?php

namespace App\Repository;

use App\Entity\Template;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TemplateRepository
 * @method Template|null find($id, $lockMode = null, $lockVersion = null)
 * @method Template|null findOneBy(array $criteria, array $orderBy = null)
 * @method Template[]    findAll()
 * @method Template[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class TemplateRepository extends AppRepository
{
    /**
     * TemplateRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Template::class);
    }
}
