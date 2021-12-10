<?php

namespace App\Services\CompletePrescription;
use App\Entity\Prescription;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
use App\Services\EntityActions\Editor\PrescriptionEditorService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PrescriptionInfoService;
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
     * CompletePrescriptionService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param NotificationsServiceBuilder $notificationServiceBuilder
     * @param NotifierService $notifier
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationsServiceBuilder $notificationServiceBuilder,
        NotifierService $notifier
    )
    {
        $this->entityManager = $entityManager;
        $this->notificationServiceBuilder = $notificationServiceBuilder;
        $this->notifier = $notifier;
    }

    /**
     * @param Prescription $prescription
     * @param MedicalRecordCreatorService $medicalRecordCreatorService
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function completePrescription(
        Prescription $prescription,
        MedicalRecordCreatorService $medicalRecordCreatorService
    ): void
    {
        if (PrescriptionInfoService::isSpecialPrescriptionsExists($prescription) && !$prescription->getIsCompleted()) {
            (new PrescriptionEditorService($this->entityManager, $prescription))->before()->after(
                [
                    PrescriptionEditorService::MEDICAL_RECORD_CREATOR_OPTION_NAME => $medicalRecordCreatorService,
                ]
            );
            $medicalHistory = $prescription->getMedicalHistory();
            $notificationData = new NotificationData(
                $this->entityManager,
                $medicalHistory->getPatient(),
                $medicalHistory,
                $prescription->getMedicalRecord()
            );
            foreach ($prescription->getPrescriptionTestings() as $prescriptionTesting) {
                $notificationServiceBuilder = $this->notificationServiceBuilder
                    ->makeTestingAppointmentNotification(
                        $notificationData,
                        $prescriptionTesting->getPatientTesting()->getAnalysisGroup()->getName(),
                        $prescriptionTesting->getPlannedDate()->format('d.m.Y')
                    );
                $this->notifier->notifyPatient(
                    $notificationServiceBuilder->getWebNotificationService(),
                    $notificationServiceBuilder->getSMSNotificationService(),
                    $notificationServiceBuilder->getEmailNotificationService()
                );
            }
            foreach ($prescription->getPrescriptionAppointments() as $prescriptionAppointment) {
                $notificationServiceBuilder = $this->notificationServiceBuilder
                    ->makeDoctorAppointmentNotification(
                        $notificationData,
                        AuthUserInfoService::getFIO(
                            $prescriptionAppointment->getPatientAppointment()->getStaff()->getAuthUser(),
                            true
                        ),
                        $prescriptionAppointment->getPlannedDateTime()->format('Y-m-d H:i:s')
                    );
                $this->notifier->notifyPatient(
                    $notificationServiceBuilder->getWebNotificationService(),
                    $notificationServiceBuilder->getSMSNotificationService(),
                    $notificationServiceBuilder->getEmailNotificationService()
                );
            }
            foreach ($prescription->getPrescriptionMedicines() as $prescriptionMedicine){
                $endMedicationDate = $prescriptionMedicine->getEndMedicationDate();
                $notificationServiceBuilder = $this->notificationServiceBuilder->makePrescriptionMedicineNotification(
                    $notificationData,
                    $prescriptionMedicine->getPatientMedicine()->getMedicineName(),
                    $prescriptionMedicine->getStartingMedicationDate()->format('Y-m-d'),
                    $endMedicationDate !== null ?
                        $endMedicationDate->format('Y-m-d') :
                        self::NO_END_DATE_MEDICATION_CONFIRM_MESSAGE,
                    $prescriptionMedicine->getPatientMedicine()->getInstruction()
                );
                $this->notifier->notifyPatient(
                    $notificationServiceBuilder->getWebNotificationService(),
                    $notificationServiceBuilder->getSMSNotificationService(),
                    $notificationServiceBuilder->getEmailNotificationService()
                );
            }
            $prescription->setIsCompleted(true);
        }
        $this->entityManager->flush();
    }
}