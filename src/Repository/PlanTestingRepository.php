<?php

namespace App\Repository;

use App\Entity\PlanTesting;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlanTesting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanTesting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanTesting[]    findAll()
 * @method PlanTesting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanTestingRepository extends AppRepository
{
    /**
     * PlanTestingRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanTesting::class);
    }

    /**
     * Получить стандартный план тестирования пациента
     *
     * @param int $gestationWeeks Количество недель беременности
     *
     * @return int|mixed|string
     */
//    public function getStandardPlanTesting(int $gestationWeeks)
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.enabled = :enabledValue')
//            ->andWhere('p.gestationalMaxAge >= :gestationalMinAgeValue')
//            ->setParameter('enabledValue', true)
//            ->setParameter('gestationalMinAgeValue', $gestationWeeks)
//            ->orderBy('p.gestationalMinAge', 'ASC')
//            ->getQuery()
//            ->getResult();
//    }
}
