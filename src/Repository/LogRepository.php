<?php

namespace App\Repository;

use App\Entity\Logger\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function getRequiredDTData($start, $length, $orders, $otherConditions, $filters)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('log');

        // Create Count Query
        $countQuery = $this->createQueryBuilder('log');
        $countQuery->select('COUNT(log)');

        // Create inner joins
        $query
            ->join('log.action', 'action');

        $countQuery
            ->join('log.action', 'action');

        // Other conditions than the ones sent by the Ajax call ?
//        if ($otherConditions === null)
//        {
//            // No
//            // However, add a "always true" condition to keep an uniform treatment in all cases
//            $query->where("1=1");
//            $countQuery->where("1=1");
//        }
//        else
//        {
//            // Add condition
//            $query->where($otherConditions);
//            $countQuery->where($otherConditions);
//        }

        //  Search
        foreach ($filters as $key => $filter)
        {
            if ($filter!= '')
            {
                $searchItem = $filter;
                $searchQuery = null;

                switch($key)
                {
                    case 'action':
                    {
                        $query->andWhere('action.name LIKE :action')
                            ->setParameter('action', '%'.$searchItem.'%');
                        $countQuery->andWhere('action.name LIKE :action')
                            ->setParameter('action', '%'.$searchItem.'%');
                        break;
                    }
                    case 'generalSearch':
                    {
                        $query->andWhere('
                                log.id LIKE :searchGlobal 
                                OR log.userId LIKE :searchGlobal 
                                OR log.userEmail LIKE :searchGlobal 
                                OR log.created_at LIKE :searchGlobal    
                                OR log.description LIKE :searchGlobal                            
                            ')
                            ->setParameter('searchGlobal', '%'.$searchItem.'%');
                        $countQuery->andWhere('
                                log.id LIKE :searchGlobal 
                                OR log.userId LIKE :searchGlobal 
                                OR log.userEmail LIKE :searchGlobal 
                                OR log.created_at LIKE :searchGlobal    
                                OR log.description LIKE :searchGlobal                    
                            ')
                            ->setParameter('searchGlobal', '%'.$searchItem.'%');
                        break;
                    }
                }
            }
        }

        // Limit
        $query->setFirstResult($start)->setMaxResults($length);

        // Order
        if ($orders['field'] != '')
        {
            $orderColumn = null;

            switch($orders['field'])
            {
                case 'id':
                {
                    $orderColumn = 'log.id';
                    break;
                }
                case 'action':
                {
                    $orderColumn = 'action.name';
                    break;
                }
                case 'user_id':
                {
                    $orderColumn = 'log.userId';
                    break;
                }
                case 'description':
                {
                    $orderColumn = 'log.description';
                    break;
                }
                case 'user_email':
                {
                    $orderColumn = 'log.userEmail';
                    break;
                }
                case 'created_at':
                {
                    $orderColumn = 'log.createdAt';
                    break;
                }
            }

            if ($orderColumn !== null)
            {
                $query->orderBy($orderColumn, $orders['sort']);
            }
        }

        // Execute
        $results = $query->getQuery()->getResult();
        $countResult = $countQuery->getQuery()->getSingleScalarResult();

        return array(
            "results" 		=> $results,
            "countResult"	=> $countResult
        );
    }

    // /**
    //  * @return Log[] Returns an array of Log objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Log
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
