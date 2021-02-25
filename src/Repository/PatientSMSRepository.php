<?php

namespace App\Repository;

use App\Entity\PatientSMS;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PatientSMSRepository
 * @method PatientSMS|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientSMS|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientSMS[]    findAll()
 * @method PatientSMS[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class PatientSMSRepository extends AppRepository
{
    /**
     * PatientSMSRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientSMS::class);
    }

    /**
     * Gets overdue testings
     * @param $patientId
     * @return int|mixed|string
     */
    public function getPatientSMSMenu($patientId)
    {
        return sizeof($this->createQueryBuilder('pS')
            ->leftJoin('pS.patient', 'p')
            ->andWhere('p.id = :patientId')
            ->andWhere('pS.isProcessed = false')
            ->setParameter('patientId', $patientId)
            ->getQuery()
            ->getResult());
    }
}
