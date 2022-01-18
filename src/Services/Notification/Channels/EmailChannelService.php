<?php

namespace App\Services\Notification\Channels;

use App\Entity\Patient;
use App\Services\InfoService\AuthUserInfoService;
use ErrorException;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Отправка email уведомлений
 * Class EmailChannelService
 * @package App\Services\Notification
 */
class EmailChannelService
{
    /** list of email templates */
    const DEFAULT_EMAIL_TEMPLATE = '/email/default.html.twig';

    const DEFAULT_EMAIL_SUBJECT = 'Доктор ИБС';

    /** Constants for email */
    const GREETINGS = 'Уважаемый, %s';

    /** @var string */
    private $subject;

    /** @var string */
    private $recipient = null;

    /** @var string */
    private $sender;

    /** @var string */
    private $mailBody;

    /** @var Environment */
    private $twig;

    /** @var array Массив получателей для множественной отправки email */
    private $recipientList = [];

    /** @var Patient $patient */
    private $patient;

    /** @var string */
    private $header;

    /** @var string */
    private $content;

    /** @var string Ссылка, по которой будет совершен переход при нажатии на кнопку */
    private $buttonLink;

    /** @var string Текст кнопки для отображения в письме */
    private $buttonText;

    /**
     * @var array
     * yaml:config/globals.yml
     */
    private $PROJECT_INFO;

    /**
     * @var array
     * yaml:config/services/notifications/email_notification_service.yml
     */
    private $EMAIL_PARAMETERS;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * EmailNotificationService constructor.
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param array $projectInfo
     * @param array $emailParameters
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        array $projectInfo,
        array $emailParameters
    )
    {
        $this->PROJECT_INFO = $projectInfo;
        $this->EMAIL_PARAMETERS = $emailParameters;
        $this->twig = $twig;
        $this->translator = $translator;
    }

    /**
     * Sends Default Email
     * @throws ErrorException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendDefaultEmail(): void
    {
        $params = [
            'siteName' => $this->PROJECT_INFO['site_name'],
            'greetings' => sprintf(self::GREETINGS, AuthUserInfoService::getFIO($this->patient->getAuthUser())),
            'header' => $this->header,
            'content' => $this->content,
            'buttonLink' => $this->buttonLink,
            'buttonText' => $this->buttonText,
            'addressLine1' => $this->PROJECT_INFO['address_line_1'],
            'addressLine2' => $this->PROJECT_INFO['address_line_2']
        ];

        $this->subject = self::DEFAULT_EMAIL_SUBJECT;
        $this->sender = $this->EMAIL_PARAMETERS['account_name'];
        $this->mailBody = $this->twig->render(
            self::DEFAULT_EMAIL_TEMPLATE,
            $params
        );
        try {
            $this->sendEmail();
        } catch (ErrorException $e) {
            throw new ErrorException($this->translator->trans('email.error.sending'));
        }
    }

    /**
     * Sends Email
     * @throws ErrorException
     */
    private function sendEmail(): void
    {
        $transport = new Swift_SmtpTransport(
            $this->EMAIL_PARAMETERS['smtp_host'],
            $this->EMAIL_PARAMETERS['smtp_port'],
            $this->EMAIL_PARAMETERS['smtp_encryption']
        );

        $transport
            ->setUsername($this->EMAIL_PARAMETERS['account_name'])
            ->setPassword($this->EMAIL_PARAMETERS['account_password']);

        if (is_null($this->recipient)) {
            throw new ErrorException(
                $this->translator->trans('email.error.email.is.null'),
                500,
                1,
                'emailNotification.php'
            );
        }

        if (!filter_var($this->recipient, FILTER_VALIDATE_EMAIL)) {
            throw new ErrorException(
                $this->translator->trans('email.error.email.is.invalid'),
                500,
                1,
                'emailNotification.php'
            );
        }

        $message = (new Swift_Message($this->subject))
            ->setFrom($this->sender)
            ->setTo($this->recipient)
            ->setBody($this->mailBody, 'text/html');

        if (!empty($this->recipientList)) {
            foreach ($this->recipientList as $recipient) {
                if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    $message->addTo($recipient);
                }
            }
        }
        $sm = new Swift_Mailer($transport);
        $sm->send($message);
    }

    /**
     * @param array $list
     * @return $this
     */
    public function addRecipientsArray(array $list = []): self
    {
        $this->recipientList = $list;
        return $this;
    }

    /**
     * @param Patient $patient
     * @return $this
     */
    public function setPatient(Patient $patient): self
    {
        $this->patient = $patient;
        $this->recipient = $patient->getAuthUser()->getEmail();
        return $this;
    }

    /**
     * @param string $header
     * @return $this
     */
    public function setHeader(string $header): self
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param string $buttonLink
     * @return $this
     */
    public function setButtonLink(string $buttonLink): self
    {
        $this->buttonLink = $buttonLink;
        return $this;
    }

    /**
     * @param string $buttonText
     * @return $this
     */
    public function setButtonText(string $buttonText): self
    {
        $this->buttonText = $buttonText;
        return $this;
    }
}