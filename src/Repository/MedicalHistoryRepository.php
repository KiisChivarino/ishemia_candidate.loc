<?php

namespace App\Repository;

use App\Entity\Diagnosis;
use App\Entity\MedicalHistory;
use App\Entity\Patient;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
class MedicalHistoryRepository extends ServiceEntityRepository
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
     * @param Patient $patient
     * @param Diagnosis $mainDisease
     *
     * @return bool|string[]
     * @throws ORMException
     */
    public function persistMedicalHistory(Patient $patient, Diagnosis $mainDisease)
    {
//        $gestationWeeks = (new PatientInfoService())->getGestationWeeks($patient->getDateStartOfTreatment());
//        if (!$gestationWeeks) {
//            return [
//                'error',
//                'Некорректное количество недель беременности!'
//            ];
//        }
//        $planTesting = $this->_em->getRepository(PlanTesting::class)->getStandardPlanTesting($gestationWeeks);
        $medicalHistory = (new MedicalHistory())->setMainDisease($mainDisease);
        $medicalHistory
            ->setPatient($patient)
            ->setEnabled(true)
            ->setDateBegin(new DateTime());
        $this->_em->persist($medicalHistory);
//        $this->_em->getRepository(PatientTesting::class)->persistPatientTests($medicalHistory, $planTesting);
        return true;
    }
}