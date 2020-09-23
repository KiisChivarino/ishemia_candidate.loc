<?php

namespace App\Repository;

use App\Entity\ReceptionMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReceptionMethod|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceptionMethod|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceptionMethod[]    findAll()
 * @method ReceptionMethod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceptionMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceptionMethod::class);
    }
}
