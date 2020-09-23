<?php

namespace App\Repository;

use App\Entity\Oktmo;
use App\Repository\AppRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Oktmo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Oktmo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Oktmo[]    findAll()
 * @method Oktmo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OktmoRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Oktmo::class);
    }

    public function getKurskRegionDistricts(): array
    {
        return $this->createQueryBuilder('o')
            ->where(
                "
            substring(o.kod2, 1, 3) = '386'
            and o.kod2 <> '38600000'
            and length(o.kod2) = 8
            and substring(o.kod2, 6, 8)='000'
        "
            )
            ->getQuery()
            ->getResult();
    }

    public function getKurskRegionCities(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.subKod1 = 38 and (c.settlementTypeId = 1 or c.settlementTypeId = 3) and length(c.kod2)=11')
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Oktmo[] Returns an array of Oktmo objects
    //  */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('o')
    ->andWhere('o.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('o.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    /*
public function findOneBySomeField($value): ?Oktmo
{
return $this->createQueryBuilder('o')
->andWhere('o.exampleField = :val')
->setParameter('val', $value)
->getQuery()
->getOneOrNullResult()
;
}
 */
}
