<?php

namespace App\Repository;

use App\Entity\PlanTesting;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PlanTestingRepository
 * @method PlanTesting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanTesting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanTesting[]    findAll()
 * @method PlanTesting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PlanTestingRepository extends AppRepository
{
    //time range count for first testings
    private const FIRST_TESTINGS_TIME_RANGE_COUNT = 0;

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
     * @return int|mixed|string
     */
    public function getStandardPlanTesting()
    {
        return $this->createQueryBuilder('pt')
            ->andWhere('pt.enabled = :enabledValue')
            ->andWhere('pt.timeRangeCount != :timeRangeCount')
            ->setParameter('enabledValue', true)
            ->setParameter('timeRangeCount', self::FIRST_TESTINGS_TIME_RANGE_COUNT)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get plan of first testings
     * Получить план начальных обследований
     * @return int|mixed|string
     */
    public function getPlanOfFirstTestings()
    {
        return $this->createQueryBuilder('pt')
            ->andWhere('pt.enabled = :enabledValue')
            ->andWhere('pt.timeRangeCount = :timeRangeCount')
            ->setParameter('enabledValue', true)
            ->setParameter('timeRangeCount', self::FIRST_TESTINGS_TIME_RANGE_COUNT)
            ->getQuery()
            ->getResult();
    }
}
