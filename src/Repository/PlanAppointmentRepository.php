<?php

namespace App\Repository;

use App\Entity\PlanAppointment;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PlanAppointmentRepository
 * @method PlanAppointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanAppointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanAppointment[]    findAll()
 * @method PlanAppointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PlanAppointmentRepository extends AppRepository
{
    /**
     * PlanAppointmentRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanAppointment::class);
    }

    /**
     * Get standard plan appointment
     *
     * @return int|mixed|string
     */
    public function getStandardPlanAppointment()
    {
        return $this->createQueryBuilder('pa')
            ->andWhere('pa.enabled = :enabledValue')
            ->andWhere('pa.timeRangeCount > :timeRangeCount')
            ->setParameter('enabledValue', true)
            ->setParameter('timeRangeCount', 0)
            ->getQuery()
            ->getResult();
    }
}
