<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Security\Core\Security;

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
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, MedicalHistory::class);
        $this->security = $security;
    }

    /**
     * Returns current open medical history
     *
     * @param Patient $patient
     *
     * @return MedicalHistory
     * @throws Exception
     */
    public function getCurrentMedicalHistory(Patient $patient): MedicalHistory
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
            throw new Exception('История болезни не найдена!');
        }
        return $medicalHistory;
    }
}