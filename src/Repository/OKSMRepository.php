<?php

namespace App\Repository;

use App\Entity\OKSM;
use App\Repository\AppRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OKSM|null find($id, $lockMode = null, $lockVersion = null)
 * @method OKSM|null findOneBy(array $criteria, array $orderBy = null)
 * @method OKSM[]    findAll()
 * @method OKSM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OKSMRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OKSM::class);
    }

    public function getRussiaCountry()
    {
        return $this->findBy(['A3' => 'RUS']);
    }
    // /**
    //  * @return OKSM[] Returns an array of OKSM objects
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
public function findOneBySomeField($value): ?OKSM
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
