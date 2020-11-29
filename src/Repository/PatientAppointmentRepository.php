<?php

namespace App\Repository;

use App\Entity\MedicalHistory;
use App\Entity\PatientAppointment;
use App\Services\InfoService\MedicalHistoryInfoService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

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
    public function getFirstAppointment(MedicalHistory $medicalHistory): ?PatientAppointment
    {
        return $this->createQueryBuilder('a')
            ->where('a.enabled = true and a.medicalHistory = :medicalHistory and a.isFirst = true')
            ->setMaxResults(1)
            ->setParameter('medicalHistory', $medicalHistory)
            ->getQuery()
            ->getOneOrNullResult();
    }
}