<?php

namespace App\Repository;

use App\Entity\PatientTesting;
use App\Entity\PrescriptionTesting;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PrescriptionTestingRepository
 * @method PrescriptionTesting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrescriptionTesting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrescriptionTesting[]    findAll()
 * @method PrescriptionTesting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class PrescriptionTestingRepository extends AppRepository
{
    /**
     * PrescriptionTestingRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrescriptionTesting::class);
    }

    /**
     * Get prescription testing for patient testing
     * @param PatientTesting $patientTesting
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function getPrescriptionTestingForPatientTesting(PatientTesting $patientTesting){
        return $this->createQueryBuilder('pa')
            ->where('pa.patientTesting = :prescriptionTesting')
            ->setParameter('prescriptionTesting', $patientTesting)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
