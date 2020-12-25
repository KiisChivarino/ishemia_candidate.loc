<?php

namespace App\Services\Notification;

use App\Entity\AuthUser;
use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\Patient;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\Notification\Services\EmailNotificationService;
use App\Services\Notification\Services\SMSNotificationService;
use App\Services\Notification\Services\WebNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Билдер для классов уведомлений
 * Class NotificationsServiceBuilder
 * @package App\Services\Notification
 */
class NotificationsServiceBuilder
{
    /** Константы для шаблонов уведомлений */
    const
        TEMPLATE_CUSTOM_MESSAGE = 'customMessage',
        TEMPLATE_DOCTOR_APPOINTMENT = 'doctorAppointment',
        TEMPLATE_CONFIRM_MEDICATION = 'confirmMedication',
        TEMPLATE_TESTING_APPOINTMENT = 'testingAppointment',
        TEMPLATE_CONFIRM_APPOINTMENT = 'confirmAppointment',
        TEMPLATE_SUBMIT_ANALYSIS_RESULTS = 'submitAnalysisResults'
    ;
    /** @var SMSNotificationService */
    private $smsNotificationService;

    /** @var EmailNotificationService */
    private $emailNotificationService;

    /** @var WebNotificationService */
    private $webNotificationService;

    /** @var Patient */
    private $patientReceiver;

    /** @var array */
    private $variables;

    /** @var MedicalHistory */
    private $medicalHistory;

    /** @var MedicalRecord */
    private $medicalRecord;

    /** @var string */
    private $notificationReceiverType;

    /** @var string */
    private $notificationTemplate;

    /** @var EntityManagerInterface */
    private $em;

    /** @var string */
    private $systemUserPhone;

    /** @var AuthUser */
    private $userSender;

    /**
     * NotificationsServiceBuilder constructor.
     * @param EntityManagerInterface $entityManager
     * @param WebNotificationService $webNotificationService
     * @param EmailNotificationService $emailNotificationService
     * @param SMSNotificationService $smsNotificationService
     * @param TokenStorageInterface $tokenStorage
     * @param string $systemUserPhone
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        WebNotificationService $webNotificationService,
        EmailNotificationService $emailNotificationService,
        SMSNotificationService $smsNotificationService,
        TokenStorageInterface $tokenStorage,
        string $systemUserPhone
    ) {
        $this->em = $entityManager;
        $this->webNotificationService = $webNotificationService;
        $this->emailNotificationService = $emailNotificationService;
        $this->smsNotificationService = $smsNotificationService;
        $this->systemUserPhone = $systemUserPhone;
        $this->userSender = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser()
            : $this->em->getRepository(AuthUser::class)->findOneBy(['phone'=>$this->systemUserPhone]);
    }

    /**
     * @return array
     */
    private function makeNotificationServices(): array
    {
        return [
            $this->webNotificationService
                ->setPatient($this->patientReceiver)
                ->setNotificationTemplate($this->notificationTemplate)
                ->setNotificationReceiverType($this->notificationReceiverType)
                ->setMedicalHistory($this->medicalHistory)
                ->setMedicalRecord($this->medicalRecord)
                ->setVariables($this->variables)
            ,
            $this->smsNotificationService
                ->setPatient($this->patientReceiver)
                ->setNotificationTemplate($this->notificationTemplate)
                ->setNotificationReceiverType($this->notificationReceiverType)
                ->setMedicalHistory($this->medicalHistory)
                ->setMedicalRecord($this->medicalRecord)
                ->setVariables($this->variables)
            ,
            $this->emailNotificationService
                ->setPatient($this->patientReceiver)
                ->setNotificationTemplate($this->notificationTemplate)
                ->setNotificationReceiverType($this->notificationReceiverType)
                ->setMedicalHistory($this->medicalHistory)
                ->setMedicalRecord($this->medicalRecord)
                ->setVariables($this->variables)
        ];
    }

    /**
     * @param string $message
     * @return array
     */
    public function makeCustomMessageNotification(string $message): array
    {
        $this->setNotificationTemplate(self::TEMPLATE_CUSTOM_MESSAGE);
        $this->setVariables([(new AuthUserInfoService())->getFIO($this->userSender), $message]);
        return $this->makeNotificationServices();
    }

    /**
     * @param string $doctor
     * @param string $appointmentDateTime
     * @return array
     */
    public function makeDoctorAppointmentNotification(string $doctor, string $appointmentDateTime): array
    {
        $this->setNotificationTemplate(self::TEMPLATE_DOCTOR_APPOINTMENT);
        $this->setVariables([$doctor, $appointmentDateTime]);
        return $this->makeNotificationServices();
    }

    /**
     * @param string $linkToConfirmMedication
     * @return array
     */
    public function makeConfirmMedicationNotification(string $linkToConfirmMedication): array
    {
        $this->setNotificationTemplate(self::TEMPLATE_CONFIRM_MEDICATION);
        $this->setVariables([$linkToConfirmMedication]);
        return $this->makeNotificationServices();
    }

    /**
     * @param string $testingAppointmentName
     * @param string $appointmentDateTime
     * @return array
     */
    public function makeTestingAppointmentNotification(
        string $testingAppointmentName,
        string $appointmentDateTime
    ): array {
        $this->setNotificationTemplate(self::TEMPLATE_TESTING_APPOINTMENT);
        $this->setVariables([$testingAppointmentName, $appointmentDateTime]);
        return $this->makeNotificationServices();
    }

    /**
     * @param string $linkToConfirmAppointment
     * @return array
     */
    public function makeConfirmAppointmentNotification(string $linkToConfirmAppointment): array
    {
        $this->setNotificationTemplate(self::TEMPLATE_CONFIRM_APPOINTMENT);
        $this->setVariables([$linkToConfirmAppointment]);
        return $this->makeNotificationServices();
    }

    /**
     * @param string $linkToSubmitAnalysisResults
     * @return array
     */
    public function makeSubmitAnalysisResultsNotification(string $linkToSubmitAnalysisResults): array
    {
        $this->setNotificationTemplate(self::TEMPLATE_SUBMIT_ANALYSIS_RESULTS);
        $this->setVariables([$linkToSubmitAnalysisResults]);
        return $this->makeNotificationServices();
    }

    //  ---------------------------------------- Сеттеры ----------------------------------------------------------

    /**
     * @param Patient $patient
     * @return NotificationsServiceBuilder
     */
    public function setPatient(Patient $patient): self
    {
        $this->patientReceiver = $patient;
        return $this;
    }

    /**
     * @param array $variables
     * @return NotificationsServiceBuilder
     */
    private function setVariables(array $variables): self
    {
        $this->variables = $variables;
        return $this;
    }

    /**
     * @param MedicalHistory $medicalHistory
     * @return NotificationsServiceBuilder
     */
    public function setMedicalHistory(MedicalHistory $medicalHistory): self
    {
        $this->medicalHistory = $medicalHistory;
        return $this;
    }

    /**
     * @param MedicalRecord $medicalRecord
     * @return NotificationsServiceBuilder
     */
    public function setMedicalRecord(MedicalRecord $medicalRecord): self
    {
        $this->medicalRecord = $medicalRecord;
        return $this;
    }

    /**
     * @param string $notificationReceiverType
     * @return NotificationsServiceBuilder
     */
    public function setNotificationReceiverType(string $notificationReceiverType): self
    {
        $this->notificationReceiverType = $notificationReceiverType;
        return $this;
    }

    /**
     * @param string $notificationTemplate
     * @return NotificationsServiceBuilder
     */
    private function setNotificationTemplate(string $notificationTemplate): self
    {
        $this->notificationTemplate = $notificationTemplate;
        return $this;
    }
}