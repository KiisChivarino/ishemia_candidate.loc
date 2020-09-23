<?php

namespace App\Repository;

use App\Entity\AppointmentType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class AppointmentTypeRepository
 * @method AppointmentType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppointmentType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppointmentType[]    findAll()
 * @method AppointmentType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class AppointmentTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppointmentType::class);
    }
}
