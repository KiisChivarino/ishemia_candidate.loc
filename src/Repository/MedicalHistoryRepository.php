<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * Class MedicalHistoryRepository
 * @method MedicalHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicalHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicalHistory[]    findAll()
 * @method MedicalHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class MedicalHistoryRepository extends AppRepository
{
    /**
     * MedicalHistoryRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalHistory::class);
    }

    /**
     * Returns current open medical history
     *
     * @param Patient $patient
     *
     * @return MedicalHistory|false
     * @throws Exception
     */
    public function getCurrentMedicalHistory(Patient $patient)
    {
        /** @var MedicalHistory $medicalHistory */
        $medicalHistory = $this->findOneBy(
            [
                'patient' => $patient,
                'enabled' => true,
                'dateEnd' => null
            ]
        );
        if (!$medicalHistory) {
            return false;
        }
        return $medicalHistory;
    }
}