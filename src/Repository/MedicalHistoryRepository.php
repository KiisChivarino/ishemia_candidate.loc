<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\Patient;
use App\Entity\PatientTesting;
use App\Entity\PlanTesting;
use App\Entity\Staff;
use App\Services\InfoService\PatientInfoService;
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
     * @param Patient $patient
     * @param Staff $staff
     *
     * @return bool|string[]
     * @throws ORMException
     */
//    public function persistMedicalHistory(Patient $patient, Staff $staff)
//    {
//        $gestationWeeks = (new PatientInfoService())->getGestationWeeks($patient->getDateStartOfTreatment());
//        if (!$gestationWeeks) {
//            return [
//                'error',
//                'Некорректное количество недель беременности!'
//            ];
//        }
//        $planTesting = $this->_em->getRepository(PlanTesting::class)->getStandardPlanTesting($gestationWeeks);
//        $medicalHistory = new MedicalHistory();
//        $medicalHistory
//            ->setPatient($patient)
//            ->setStaff($staff)
//            ->setEnabled(true)
//            ->setDateBegin(new DateTime());
//        $this->_em->persist($medicalHistory);
//        $this->_em->getRepository(PatientTesting::class)->persistPatientTests($medicalHistory, $planTesting);
//        return true;
//    }
}