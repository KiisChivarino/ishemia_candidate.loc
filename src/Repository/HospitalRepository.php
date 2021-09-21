<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Hospital;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class HospitalRepository
 * * @method Hospital|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hospital|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hospital[]    findAll()
 * @method Hospital[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class HospitalRepository extends AppRepository
{
    /**
     * HospitalRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hospital::class);
    }

    /**
     * Ищет больницы по части строки в поле name
     *
     * @param string|null $value
     * @param string $city_id
     *
     * @return Hospital[] Returns an array of Diagnosis objects
     */
    public function findHospitals(?string $value, string $city_id): array
    {
        /** @var City $city */
        $city = $this->_em->getRepository(City::class)->find((int)$city_id);
        $qb = $this->createQueryBuilder('h')
            ->where('h.enabled = :valEnabled')
            ->andWhere('LOWER(h.name) LIKE LOWER(:hospital)')
            ->setParameter('valEnabled', true)
            ->setParameter('hospital', '%'.$value.'%');


        if (is_a($city, City::class)) {
            $qb->andWhere('h.city = :valCity')
                ->setParameter('valCity', $city);
        }
        $qb
            ->orderBy('h.name', 'ASC')
            ->setMaxResults(10);
        return $qb->getQuery()->getResult();
    }
}