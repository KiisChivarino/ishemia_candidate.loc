<?php

namespace App\Services\CompletePrescription;

use App\Entity\Prescription;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
use App\Services\EntityActions\Editor\CompletePrescriptionEditorService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\Notification\NotificationData;
use App\Services\Notification\NotificationsServiceBuilder;
use App\Services\Notification\NotifierService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Class CompletePrescriptionService
 *
 * @package App\Services\CompletePrescription
 */
class CompletePrescriptionService
{
    /** @var string Text in the absence of a medication end date */
    protected const NO_END_DATE_MEDICATION_CONFIRM_MESSAGE = '(без ограничения)';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var NotificationsServiceBuilder
     */
    private $notificationServiceBuilder;

    /**
     * @var NotifierService
     */
    private $notifier;

    /**
     * @var MedicalRecordCreatorService
     */
    private $medicalRecordCreatorService;

    /**
     * CompletePrescriptionService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param NotificationsServiceBuilder $notificationServiceBuilder
     * @param NotifierService $notifier
     * @param MedicalRecordCreatorService $medicalRecordCreatorService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationsServiceBuilder $notificationServiceBuilder,
        NotifierService $notifier,
        MedicalRecordCreatorService $medicalRecordCreatorService
    )
    {
        $this->entityManager = $entityManager;
        $this->notificationServiceBuilder = $notificationServiceBuilder;
        $this->notifier = $notifier;
        $this->medicalRecordCreatorService = $medicalRecordCreatorService;
    }

    /**
     * @param Prescription $prescription
     *
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function completePrescription(
        Prescription $prescription
    ): void
    {
        $prescription->setIsCompleted(true);
        (new CompletePrescriptionEditorService($this->entityManager, $prescription))->before()->after(
            [
                CompletePrescriptionEditorService::MEDICAL_RECORD_CREATOR_OPTION_NAME =>
                    $this->medicalRecordCreatorService,
            ]
        );
        $medicalHistory = $prescription->getMedicalHistory();
        $notificationData = new NotificationData(
            $this->entityManager,
            $medicalHistory->getPatient(),
            $medicalHistory,
            $prescription->getMedicalRecord()
        );
        $notificationServiceBuilder = clone $this->notificationServiceBuilder;
        foreach ($prescription->getPrescriptionTestings() as $prescriptionTesting) {
            $notificationServiceBuilder->makeTestingAppointmentNotification(
                $notificationData,
                $prescriptionTesting->getPatientTesting()->getAnalysisGroup()->getName(),
                $prescriptionTesting->getPlannedDate()->format('d.m.Y')
            );
            $this->senderNotifyForPatient($notificationServiceBuilder);
        }
        $notificationServiceBuilder = clone $this->notificationServiceBuilder;
        foreach ($prescription->getPrescriptionAppointments() as $prescriptionAppointment) {
            $notificationServiceBuilder->makeDoctorAppointmentNotification(
                $notificationData,
                AuthUserInfoService::getFIO(
                    $prescriptionAppointment->getPatientAppointment()->getStaff()->getAuthUser(),
                    true
                ),
                $prescriptionAppointment->getPlannedDateTime()->format('Y-m-d H:i:s')
            );
            $this->senderNotifyForPatient($notificationServiceBuilder);
        }
        $notificationServiceBuilder = clone $this->notificationServiceBuilder;
        foreach ($prescription->getPrescriptionMedicines() as $prescriptionMedicine) {
            $endMedicationDate = $prescriptionMedicine->getEndMedicationDate();
            $notificationServiceBuilder = $notificationServiceBuilder->makePrescriptionMedicineNotification(
                $notificationData,
                $prescriptionMedicine->getPatientMedicine()->getMedicineName(),
                $prescriptionMedicine->getStartingMedicationDate()->format('Y-m-d'),
                $endMedicationDate !== null ?
                    $endMedicationDate->format('Y-m-d') :
                    self::NO_END_DATE_MEDICATION_CONFIRM_MESSAGE,
                $prescriptionMedicine->getPatientMedicine()->getInstruction()
            );
            $this->senderNotifyForPatient($notificationServiceBuilder);
        }
        $prescription->setIsCompleted(true);
    }


    /**
     * Sending notifications to the patient
     *
     * @throws Exception
     */
    protected function senderNotifyForPatient(
        NotificationsServiceBuilder $notificationServiceBuilder
    ): void
    {
        $this->notifier->notifyPatient(
            $notificationServiceBuilder->getWebNotificationService(),
            $notificationServiceBuilder->getSMSNotificationService(),
            $notificationServiceBuilder->getEmailNotificationService()
        );
    }
}