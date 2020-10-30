<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\Patient;
use App\Entity\PatientAppointment;
use App\Entity\PlanAppointment;
use App\Services\InfoService\MedicalHistoryInfoService;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * Class PatientAppointmentRepository
 * @method PatientAppointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientAppointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientAppointment[]    findAll()
 * @method PatientAppointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class PatientAppointmentRepository extends AppRepository
{
    /** @var MedicalHistoryInfoService $medicalHistoryInfoService */
    private $medicalHistoryInfoService;

    /**
     * PatientAppointmentRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param MedicalHistoryInfoService $medicalHistoryInfoService
     */
    public function __construct(ManagerRegistry $registry, MedicalHistoryInfoService $medicalHistoryInfoService)
    {
        parent::__construct($registry, PatientAppointment::class);
        $this->medicalHistoryInfoService = $medicalHistoryInfoService;
    }

    /**
     * Получение первого приема пациента
     *
     * @param MedicalHistory $medicalHistory
     *
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getFirstAppointment(MedicalHistory $medicalHistory)
    {
        return $this->createQueryBuilder('a')
            ->where('a.enabled = true and a.medicalHistory = :medicalHistory')
            ->orderBy('a.plannedTime', 'ASC')
            ->setMaxResults(1)
            ->setParameter('medicalHistory', $medicalHistory)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Добавление приема пациента
     *
     * @param PatientAppointment $patientAppointment
     *
     * @throws ORMException
     */
    public function persistPatientAppointment(PatientAppointment $patientAppointment): void
    {
        $patientAppointment
            ->setEnabled(true)
            ->setMedicalRecord(
                $this->_em->getRepository(MedicalRecord::class)->getMedicalRecord($patientAppointment->getMedicalHistory())
            )
            ->setIsConfirmed(false)
            ->setPlannedTime(new DateTime());
        $this->_em->persist($patientAppointment);
    }

    /**
     * @param $medicalHistory
     *
     * @return array
     * @throws ORMException
     */
    public function persistPatientAppointmentsByPlan($medicalHistory): array
    {
        $patientAppointments = [];
        /** @var PlanAppointment $appointment */
        foreach ($this->_em->getRepository(PlanAppointment::class)->getStandardPlanAppointment() as $appointment) {
            $patientAppointment = (new PatientAppointment())
                ->setMedicalHistory($medicalHistory)
                ->setEnabled(true)
                ->setPlannedTime($this->getPlannedDate($appointment))
                ->setIsConfirmed(false);
            $this->_em->persist($patientAppointment);
            $patientAppointments[] = $patientAppointment;
        }
        return $patientAppointments;
    }

    /**
     * @param PlanAppointment $planAppointment
     *
     * @return DateTime
     * @throws Exception
     */
    protected function getPlannedDate(PlanAppointment $planAppointment)
    {
        return $this->medicalHistoryInfoService->getPlannedDate(
            new DateTime(),
            (int)$planAppointment->getTimeRangeCount(),
            (int)$planAppointment->getTimeRange()->getMultiplier(),
            $planAppointment->getTimeRange()->getDateInterval()->getFormat()
        );
    }
}