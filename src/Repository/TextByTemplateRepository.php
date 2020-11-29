<?php

namespace App\Repository;

use App\Entity\TextByTemplate;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TextByTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method TextByTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method TextByTemplate[]    findAll()
 * @method TextByTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextByTemplateRepository extends AppRepository
{
    /**
     * TextByTemplateRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TextByTemplate::class);
    }
}
