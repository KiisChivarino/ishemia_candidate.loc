<?php

namespace App\Repository;

use App\Entity\Diagnosis;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Diagnosis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Diagnosis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Diagnosis[]    findAll()
 * @method Diagnosis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiagnosisRepository extends AppRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Diagnosis::class);
    }

    /**
     * Ищет диагнозы по части строки в полях код и имя диагноза
     *
     * @param string $value
     *
     * @return Diagnosis[] Returns an array of Diagnosis objects
     */
    public function findDiagnoses(string $value): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('LOWER(d.name) LIKE LOWER(:valName) OR LOWER(d.code) LIKE LOWER(:valCode)')
            ->andWhere('d.enabled = :valEnabled')
            ->setParameter('valName', '%' . $value . '%')
            ->setParameter('valCode', $value . '%')
            ->setParameter('valEnabled', true)
            ->orderBy('d.name', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find diagnosis by name and code (without register)
     * @param string $name
     * @param string $code
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findDiagnosisByNameAndCode(string $name, string $code)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('LOWER(d.name) = LOWER(:valName)')
            ->andWhere('LOWER(d.code) = LOWER(:valCode)')
            ->andWhere('d.enabled = :valEnabled')
            ->setParameter('valName', $name)
            ->setParameter('valCode', $code)
            ->setParameter('valEnabled', true)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
