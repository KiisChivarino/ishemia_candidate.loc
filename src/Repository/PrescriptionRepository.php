<?php

namespace App\Repository;

use App\Entity\Hospital;
use App\Entity\MedicalHistory;
use App\Entity\Prescription;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Prescription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prescription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prescription[]    findAll()
 * @method Prescription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrescriptionRepository extends AppRepository
{
    /**
     * PrescriptionRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prescription::class);
    }

    /**
     * Gets patients with opened prescriptions count
     * @param Hospital|null $hospital
     * @return int|mixed|string
     */
    public function getOpenedPrescriptionsCount(?Hospital $hospital)
    {
        $qb = $this->generateBuilder()
            ->andWhere('pr.isCompleted = false');
        if (!is_null($hospital)) {
            $qb->andWhere('p.hospital =:patientHospital')
                ->setParameter('patientHospital', $hospital);
        }
        return sizeof($qb->select('p.id')
            ->distinct()
            ->getQuery()
            ->getScalarResult());
    }

    /**
     * Gets Patients`ids array if Prescription is opened
     * @param $patient
     * @return int|mixed|string
     */
    public function getOpenedPrescriptionsForPatientList($patient)
    {
        return $this->generateBuilder()
            ->andWhere('p = :patient')
            ->andWhere('pr.isCompleted = false')
            ->setParameter('patient', $patient)
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets Patients`ids array if Prescription is opened
     * @return int|mixed|string
     */
    public function getOpenedPrescriptions()
    {
        return $this->generateBuilder()
            ->andWhere('pr.isCompleted = false')
            ->select('p.id')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    /**
     * Find not completed prescription
     *
     * @param MedicalHistory $medicalHistory
     *
     * @return Prescription|null
     */
    public function findNotCompletedPrescription(MedicalHistory $medicalHistory): ?Prescription
    {
        return $this->findOneBy(
            [
                'isCompleted' => false,
                'medicalHistory' => $medicalHistory
            ]
        );
    }

    /**
     * Generate Builder For Prescription With Current Medical History And Enabled User
     * @return QueryBuilder
     */
    public function generateBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('pr')
            ->leftJoin('pr.medicalHistory', 'mH')
            ->leftJoin('mH.patient', 'p')
            ->leftJoin('p.AuthUser', 'u')
            ->andWhere('mH.enabled = true')
            ->andWhere('mH.dateEnd IS NULL')
            ->andWhere('u.enabled = true');
    }
}
