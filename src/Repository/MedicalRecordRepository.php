<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * Class MedicalRecordRepository
 * @method MedicalRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicalRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicalRecord[]    findAll()
 * @method MedicalRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class MedicalRecordRepository extends AppRepository
{
    /**
     * MedicalRecordRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalRecord::class);
    }

    /**
     * Find medical record for current date
     *
     * @param MedicalHistory $medicalHistory
     *
     * @return MedicalRecord
     * @throws Exception
     */
    public function getMedicalRecord(MedicalHistory $medicalHistory): ?MedicalRecord
    {
        return $this
            ->createQueryBuilder('mr')
            ->leftJoin('mr.medicalHistory', 'mh')
            ->where('mr.recordDate = :recordDate and mr.medicalHistory=:medicalHistory and mr.enabled = true and mh.enabled = true')
            ->setParameters(
                [
                    'recordDate' => new DateTime(),
                    'medicalHistory' => $medicalHistory
                ]
            )->getQuery()->getOneOrNullResult();
    }
}
