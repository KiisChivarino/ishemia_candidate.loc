<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use DateTime;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
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
     * @return MedicalHistory|null
     */
    public function getCurrentMedicalHistory(Patient $patient)
    {
        return $this->findOneBy(
            [
                'patient' => $patient,
                'enabled' => true,
                'dateEnd' => null
            ]
        );
    }

    /**
     * @param MedicalHistory $medicalHistory
     *
     * @return void
     * @throws ORMException
     */
    public function persistMedicalHistory(MedicalHistory $medicalHistory): void
    {
        $medicalHistory
            ->setEnabled(true)
            ->setDateBegin(new DateTime());
        $this->_em->persist($medicalHistory);
    }
}