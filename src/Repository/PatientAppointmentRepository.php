<?php

namespace App\Repository;

use App\Entity\PatientAppointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PatientAppointmentRepository
 * @method PatientAppointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientAppointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientAppointment[]    findAll()
 * @method PatientAppointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PatientAppointmentRepository extends ServiceEntityRepository
{
    /**
     * PatientAppointmentRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientAppointment::class);
    }
}
