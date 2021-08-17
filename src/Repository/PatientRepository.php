<?php

namespace App\Repository;

use App\Entity\AuthUser;
use App\Entity\Patient;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PatientRepository
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Patient[]    findAll()
 * @method Patient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class PatientRepository extends AppRepository
{
    /**
     * PatientRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }

    /**
     * Gets patient by auth user
     *
     * @param AuthUser $authUser
     * @return Patient|null
     * @throws NonUniqueResultException
     */
    public function getPatientByAuthUser(AuthUser $authUser): ?Patient
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.AuthUser = :authUser')
            ->setParameter('authUser', $authUser)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
