<?php

namespace App\Services\Notification;

use App\Entity\AuthUser;
use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Entity\NotificationConfirm;
use App\Entity\Patient;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\LoggerService\LogService;
use App\Services\Notification\Services\EmailNotificationService;
use App\Services\Notification\Services\SMSNotificationService;
use App\Services\Notification\Services\WebNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Билдер для классов уведомлений
 * Class NotificationsServiceBuilder
 * @package App\Services\Notification
 */
class NotificationsServiceBuilder
{
    /** Константы для типов получателей  */
    const
        RECEIVER_TYPE_PATIENT = 'patient';

    /** Константы для шаблонов уведомлений */
    const
        TEMPLATE_CUSTOM_MESSAGE = 'customMessage',
        TEMPLATE_DOCTOR_APPOINTMENT = 'doctorAppointment',
        TEMPLATE_CONFIRM_MEDICATION = 'confirmMedication',
        TEMPLATE_TESTING_APPOINTMENT = 'testingAppointment',
        TEMPLATE_CONFIRM_APPOINTMENT = 'confirmAppointment',
        TEMPLATE_SUBMIT_ANALYSIS_RESULTS = 'submitAnalysisResults';

    /** @var SMSNotificationService */
    private $smsNotificationService;

    /** @var EmailNotificationService */
    private $emailNotificationService;

    /** @var WebNotificationService */
    private $webNotificationService;

    /** @var Patient */
    private $patientReceiver;

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

    /** @var NotificationConfirm */
    private $notificationConfirm;

    /** @var array */
    private $variablesForEmail;

    /** @var array */
    private $variablesForSMS;

    /** @var array */
    private $variablesForWeb;

    /** @var LogService */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * NotificationsServiceBuilder constructor.
     * @param EntityManagerInterface $entityManager
     * @param WebNotificationService $webNotificationService
     * @param EmailNotificationService $emailNotificationService
     * @param SMSNotificationService $smsNotificationService
     * @param TokenStorageInterface $tokenStorage
     * @param LogService $logger
     * @param TranslatorInterface $translator
     * @param string $systemUserPhone
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        WebNotificationService $webNotificationService,
        EmailNotificationService $emailNotificationService,
        SMSNotificationService $smsNotificationService,
        TokenStorageInterface $tokenStorage,
        LogService $logger,
        TranslatorInterface $translator,
        string $systemUserPhone
    )
    {
        $this->em = $entityManager;
        $this->webNotificationService = $webNotificationService;
        $this->emailNotificationService = $emailNotificationService;
        $this->smsNotificationService = $smsNotificationService;
        $this->systemUserPhone = $systemUserPhone;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->userSender = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser()
            : $this->em->getRepository(AuthUser::class)->findOneBy(['phone' => $this->systemUserPhone]);
    }

    /**
     * @param string $message
     * @return NotificationsServiceBuilder
     */
    public function makeCustomMessageNotification(string $message): NotificationsServiceBuilder
    {
        $this->notificationReceiverType = self::RECEIVER_TYPE_PATIENT;
        $this->setNotificationTemplate(self::TEMPLATE_CUSTOM_MESSAGE);
        $this->setVariables([(new AuthUserInfoService())->getFIO($this->userSender), $message]);
        return $this->makeNotificationServices();
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

    /**
     * Variables setter
     * @param array $variablesForSMS
     * @param array|null $variablesForEmail
     * @param array|null $variablesForWeb
     * @return bool
     */
    private function setVariables(
        array $variablesForSMS,
        array $variablesForEmail = null,
        array $variablesForWeb = null
    ): bool
    {
        $this->setVariablesForSMS($variablesForSMS);
        $this->setVariablesForEmail($variablesForEmail ?? $variablesForSMS);
        $this->setVariablesForWeb($variablesForWeb ?? $variablesForEmail ?? $variablesForSMS);
        return true;
    }

    /**
     * @param array $variables
     * @return NotificationsServiceBuilder
     */
    private function setVariablesForSMS(array $variables): self
    {
        $this->variablesForSMS = $variables;
        return $this;
    }

    /**
     * @param array $variables
     * @return NotificationsServiceBuilder
     */
    private function setVariablesForEmail(array $variables): self
    {
        $this->variablesForEmail = $variables;
        return $this;
    }

    /**
     * @param array $variables
     * @return NotificationsServiceBuilder
     */
    private function setVariablesForWeb(array $variables): self
    {
        $this->variablesForWeb = $variables;
        return $this;
    }

    /**
     * @return NotificationsServiceBuilder
     */
    private function makeNotificationServices(): NotificationsServiceBuilder
    {
        $this->webNotificationService
            ->setPatient($this->patientReceiver)
            ->setNotificationTemplate($this->notificationTemplate)
            ->setNotificationReceiverType($this->notificationReceiverType)
            ->setMedicalHistory($this->medicalHistory)
            ->setMedicalRecord($this->medicalRecord)
            ->setVariables($this->variablesForWeb)
            ->setNotificationConfirm($this->notificationConfirm);
        $this->smsNotificationService
            ->setPatient($this->patientReceiver)
            ->setNotificationTemplate($this->notificationTemplate)
            ->setNotificationReceiverType($this->notificationReceiverType)
            ->setMedicalHistory($this->medicalHistory)
            ->setMedicalRecord($this->medicalRecord)
            ->setVariables($this->variablesForSMS)
            ->setNotificationConfirm($this->notificationConfirm);
        $this->emailNotificationService
            ->setPatient($this->patientReceiver)
            ->setNotificationTemplate($this->notificationTemplate)
            ->setNotificationReceiverType($this->notificationReceiverType)
            ->setMedicalHistory($this->medicalHistory)
            ->setMedicalRecord($this->medicalRecord)
            ->setVariables($this->variablesForEmail)
            ->setNotificationConfirm($this->notificationConfirm);
        return $this;
    }

    /**
     * @param string $doctor
     * @param string $appointmentDateTime
     * @return NotificationsServiceBuilder
     */
    public function makeDoctorAppointmentNotification(string $doctor, string $appointmentDateTime): NotificationsServiceBuilder
    {
        $this->notificationReceiverType = self::RECEIVER_TYPE_PATIENT;
        $this->setNotificationTemplate(self::TEMPLATE_DOCTOR_APPOINTMENT);
        $this->setVariables([$doctor, $appointmentDateTime]);
        return $this->makeNotificationServices();
    }

    /**
     * @return NotificationsServiceBuilder
     */
    public function makeConfirmMedicationNotification(): NotificationsServiceBuilder
    {
        $this->notificationReceiverType = self::RECEIVER_TYPE_PATIENT;
        $this->createNotificationConfirm();
        $this->setNotificationTemplate(self::TEMPLATE_CONFIRM_MEDICATION);
        $this->setVariables([$this->notificationConfirm->getSmsCode()], ['http://shemia.test/confirmNotification/' . $this->notificationConfirm->getEmailCode()]);
        return $this->makeNotificationServices();
    }

    //  ---------------------------------------- Сеттеры ----------------------------------------------------------

    /**
     * Creates new NotificationConfirm
     * @return NotificationConfirm
     */
    private function createNotificationConfirm(): NotificationConfirm
    {
        $notificationConfirm = $this->notificationConfirm = (new NotificationConfirm())
            ->setEmailCode(md5($this->patientReceiver->getAuthUser()->getPhone() . rand(0, 99999)))
            ->setSmsCode(str_pad(rand(0, pow(10, 4) - 1), 4, '0', STR_PAD_LEFT));
        $this->em->persist($notificationConfirm);

        $this->logger
            ->setUser($this->userSender)
            ->setDescription(
                $this->translator->trans(
                    'log.new.entity',
                    ['%entity%' => 'Подтверждение уведомления', '%id%' => $notificationConfirm->getId()]
                )
            )
            ->logCreateEvent();
        return $notificationConfirm;
    }

    /**
     * @param string $testingAppointmentName
     * @param string $appointmentDateTime
     * @return NotificationsServiceBuilder
     */
    public function makeTestingAppointmentNotification(
        string $testingAppointmentName,
        string $appointmentDateTime
    ): NotificationsServiceBuilder
    {
        $this->notificationReceiverType = self::RECEIVER_TYPE_PATIENT;
        $this->setNotificationTemplate(self::TEMPLATE_TESTING_APPOINTMENT);
        $this->setVariables([$testingAppointmentName, $appointmentDateTime]);
        return $this->makeNotificationServices();
    }

    /**
     * @return NotificationsServiceBuilder
     */
    public function makeConfirmAppointmentNotification(): NotificationsServiceBuilder
    {
        $this->notificationReceiverType = self::RECEIVER_TYPE_PATIENT;
        $this->createNotificationConfirm();
        $this->setNotificationTemplate(self::TEMPLATE_CONFIRM_APPOINTMENT);
        $this->setVariables([$this->notificationConfirm->getSmsCode()], ['http://shemia.test/confirmNotification/' . $this->notificationConfirm->getEmailCode()]);
        return $this->makeNotificationServices();
    }

    /**
     * @param string $linkToSubmitAnalysisResults
     * @return NotificationsServiceBuilder
     */
    public function makeSubmitAnalysisResultsNotification(string $linkToSubmitAnalysisResults): NotificationsServiceBuilder
    {
        $this->notificationReceiverType = self::RECEIVER_TYPE_PATIENT;
        $this->setNotificationTemplate(self::TEMPLATE_SUBMIT_ANALYSIS_RESULTS);
        $this->setVariables([$linkToSubmitAnalysisResults]);
        return $this->makeNotificationServices();
    }

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
     * @return WebNotificationService
     */
    public function getWebNotificationService(): WebNotificationService
    {
        return $this->webNotificationService;
    }

    /**
     * @return EmailNotificationService
     */
    public function getEmailNotificationService(): EmailNotificationService
    {
        return $this->emailNotificationService;
    }

    /**
     * @return SMSNotificationService
     */
    public function getSMSNotificationService(): SMSNotificationService
    {
        return $this->smsNotificationService;
    }
}