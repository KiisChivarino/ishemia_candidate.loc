<?php

namespace App\Repository;

use App\Entity\PrescriptionTesting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrescriptionTesting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrescriptionTesting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrescriptionTesting[]    findAll()
 * @method PrescriptionTesting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrescriptionTestingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrescriptionTesting::class);
    }
}
