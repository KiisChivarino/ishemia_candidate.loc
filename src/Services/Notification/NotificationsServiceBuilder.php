<?php

namespace App\Services\Notification;

use App\Entity\AuthUser;
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

    /** @var string */
    private $notificationReceiverType;

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
     * Creates notification with CustomMessage Template
     * @param NotificationData $notificationData
     * @param string $message
     * @return NotificationsServiceBuilder
     */
    public function makeCustomMessageNotification(
        NotificationData $notificationData,
        string $message
    ): NotificationsServiceBuilder
    {
        $this->notificationReceiverType = self::RECEIVER_TYPE_PATIENT;
        $this->setVariables([(new AuthUserInfoService())->getFIO($this->userSender), $message]);
        return $this->makeNotificationServices($notificationData, self::TEMPLATE_CUSTOM_MESSAGE);
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
     * Creates notification services for every type of notification channel
     * @param NotificationData $notificationData
     * @param string $notificationTemplate
     * @return NotificationsServiceBuilder
     */
    private function makeNotificationServices(
        NotificationData $notificationData,
        string $notificationTemplate
    ): NotificationsServiceBuilder
    {
        $this->webNotificationService
            ->setNotificationData($notificationData)
            ->setNotificationTemplate($notificationTemplate)
            ->setNotificationReceiverType($this->notificationReceiverType)
            ->setVariables($this->variablesForWeb)
            ->setNotificationConfirm($this->notificationConfirm ?? null);
        $this->smsNotificationService
            ->setNotificationData($notificationData)
            ->setNotificationTemplate($notificationTemplate)
            ->setNotificationReceiverType($this->notificationReceiverType)
            ->setVariables($this->variablesForSMS)
            ->setNotificationConfirm($this->notificationConfirm ?? null);
        $this->emailNotificationService
            ->setNotificationData($notificationData)
            ->setNotificationTemplate($notificationTemplate)
            ->setNotificationReceiverType($this->notificationReceiverType)
            ->setVariables($this->variablesForEmail)
            ->setNotificationConfirm($this->notificationConfirm ?? null);
        return $this;
    }

    /**
     * Creates notification with DoctorAppointment Template
     * @param NotificationData $notificationData
     * @param string $doctor
     * @param string $appointmentDateTime
     * @return NotificationsServiceBuilder
     */
    public function makeDoctorAppointmentNotification(
        NotificationData $notificationData,
        string $doctor,
        string $appointmentDateTime
    ): NotificationsServiceBuilder
    {
        $this->notificationReceiverType = self::RECEIVER_TYPE_PATIENT;
        $this->setVariables([$doctor, $appointmentDateTime]);
        return $this->makeNotificationServices($notificationData, self::TEMPLATE_DOCTOR_APPOINTMENT);
    }

    /**
     * Creates notification with ConfirmMedication Template
     * @param NotificationData $notificationData
     * @return NotificationsServiceBuilder
     */
    public function makeConfirmMedicationNotification(NotificationData $notificationData): NotificationsServiceBuilder
    {
        $this->notificationReceiverType = self::RECEIVER_TYPE_PATIENT;
        $this->createNotificationConfirm($notificationData->getPatientReceiver());
        $this->setVariables(
            [$this->notificationConfirm->getSmsCode()],
            ['http://shemia.test/confirmNotification/' . $this->notificationConfirm->getEmailCode()]
        );
        return $this->makeNotificationServices($notificationData, self::TEMPLATE_CONFIRM_MEDICATION);
    }

    /**
     * Creates new NotificationConfirm
     * @param Patient $patient
     * @return NotificationConfirm
     */
    private function createNotificationConfirm(Patient $patient): NotificationConfirm
    {
        $notificationConfirm = $this->notificationConfirm = (new NotificationConfirm())
            ->setEmailCode(md5($patient->getAuthUser()->getPhone() . rand(0, 99999)))
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
     * Creates notification with TestingAppointment Template
     * @param NotificationData $notificationData
     * @param string $testingAppointmentName
     * @param string $appointmentDateTime
     * @return NotificationsServiceBuilder
     */
    public function makeTestingAppointmentNotification(
        NotificationData $notificationData,
        string $testingAppointmentName,
        string $appointmentDateTime
    ): NotificationsServiceBuilder
    {
        $this->notificationReceiverType = self::RECEIVER_TYPE_PATIENT;
        $this->setVariables([$testingAppointmentName, $appointmentDateTime]);
        return $this->makeNotificationServices($notificationData, self::TEMPLATE_TESTING_APPOINTMENT);
    }

    /**
     * Creates notification with ConfirmAppointment Template
     * @param NotificationData $notificationData
     * @return NotificationsServiceBuilder
     */
    public function makeConfirmAppointmentNotification(NotificationData $notificationData): NotificationsServiceBuilder
    {
        $this->notificationReceiverType = self::RECEIVER_TYPE_PATIENT;
        $this->createNotificationConfirm($notificationData->getPatientReceiver());
        $this->setVariables(
            [$this->notificationConfirm->getSmsCode()],
            ['http://shemia.test/confirmNotification/' . $this->notificationConfirm->getEmailCode()]
        );
        return $this->makeNotificationServices($notificationData, self::TEMPLATE_CONFIRM_APPOINTMENT);
    }

    /**
     * Creates notification with SubmitAnalysisResults Template
     * @param NotificationData $notificationData
     * @param string $linkToSubmitAnalysisResults
     * @return NotificationsServiceBuilder
     */
    public function makeSubmitAnalysisResultsNotification(
        NotificationData $notificationData,
        string $linkToSubmitAnalysisResults
    ): NotificationsServiceBuilder
    {
        $this->notificationReceiverType = self::RECEIVER_TYPE_PATIENT;
        $this->setVariables([$linkToSubmitAnalysisResults]);
        return $this->makeNotificationServices(
            $notificationData, self::TEMPLATE_SUBMIT_ANALYSIS_RESULTS
        );
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