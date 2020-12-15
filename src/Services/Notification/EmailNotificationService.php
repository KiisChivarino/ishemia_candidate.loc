<?php


namespace App\Services\Notification;

use App\Entity\Patient;
use App\Services\InfoService\AuthUserInfoService;
use ErrorException;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class EmailNotificationService
 * @package App\Services\Notification
 */
class EmailNotificationService
{
    /** list of email templates */
    const
        DEFAULT_EMAIL_TEMPLATE = '/email/default.html.twig'
    ;

    /** Константы для email */
    const
        GREETINGS = 'Уважаемый, %s'
    ;

    /** @var string  */
    private $subject;

    /** @var string  */
    private $recipient = null;

    /** @var string  */
    private $sender;

    /** @var string  */
    private $mailBody;

    /** @var string  */
    private $serverHost;

    /** @var Environment  */
    private $twig;

    /** @var array  */
    private $recipientList = [];

    /** @var Patient $patient  */
    private $patient;

    /** @var string  */
    private $header;

    /** @var string  */
    private $content;

    /** @var string  */
    private $buttonLink;

    /** @var string  */
    private $buttonText;

    /** @var array  */
    private $projectInfo;

    /** @var array  */
    private $emailParameters;

    /**
     * EmailNotification constructor.
     * @param Environment $twig
     * @param $projectInfo
     * @param $emailParameters
     */
    public function __construct(
        Environment $twig,
        $projectInfo,
        $emailParameters
    )
    {
        $this->projectInfo = $projectInfo;
        $this->emailParameters = $emailParameters;
        $this->twig = $twig;
        $this->serverHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
            . "://"
            . $_SERVER['HTTP_HOST']
        ;
    }

    /**
     * Sends Email
     * @throws ErrorException
     */
    private function sendEmail() :void
    {
        $transport = new Swift_SmtpTransport(
            $this->emailParameters['smtp_host'],
            $this->emailParameters['smtp_port'],
            $this->emailParameters['smtp_encryption']
        );

        $transport
            ->setUsername($this->emailParameters['account_name'])
            ->setPassword($this->emailParameters['account_password']);

        if (is_null($this->recipient)) {
            throw new ErrorException('Recipient email is null. Please provide correct email',
                500,
                1,
                'emailNotification.php'
            );
        }

        if (!filter_var($this->recipient, FILTER_VALIDATE_EMAIL)) {
            throw new ErrorException('Invalid recipient email. Please provide correct email',
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
                if (filter_var($this->recipient, FILTER_VALIDATE_EMAIL)) {
                    $message->addTo($recipient);
                }
            }
        }
        $sm = new Swift_Mailer($transport);
        $sm->send($message);
    }

    /**
     * Sends Default Email
     * @throws ErrorException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendDefaultEmail() :void
    {
        $params = [
            'siteName' => $this->projectInfo['site_name'],
            'greetings' => sprintf(self::GREETINGS, AuthUserInfoService::getFIO($this->patient->getAuthUser())),
            'header' => $this->header,
            'content' => $this->content,
            'buttonLink' => $this->buttonLink,
            'buttonText' => $this->buttonText,
            'addressLine1' => $this->projectInfo['address_line_1'],
            'addressLine2' => $this->projectInfo['address_line_2']
        ];

        $this->subject = 'Дефолтная тема письма';
        $this->sender = $this->emailParameters['account_name'];

        $this->mailBody = $this->twig->render(
            self::DEFAULT_EMAIL_TEMPLATE,
            $params
        );
        try {
            $this->sendEmail();
        } catch (ErrorException $e) {
            throw new ErrorException('Email message sending error');
        }
    }

    /**
     * @param array $list
     * @return $this
     */
    public function addRecipientsArray(array $list=[])
    {
        $this->recipientList = $list;
        return $this;
    }

    /**
     * @param Patient $patient
     * @return $this
     */
    public function setPatient(Patient $patient): EmailNotificationService
    {
        $this->patient = $patient;
        $this->recipient = $patient->getAuthUser()->getEmail();
        return $this;
    }

    /**
     * @param string $header
     * @return $this
     */
    public function setHeader(string $header): EmailNotificationService
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): EmailNotificationService
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param string $buttonLink
     * @return $this
     */
    public function setButtonLink(string $buttonLink): EmailNotificationService
    {
        $this->buttonLink = $buttonLink;
        return $this;
    }

    /**
     * @param string $buttonText
     * @return $this
     */
    public function setButtonText(string $buttonText): EmailNotificationService
    {
        $this->buttonText = $buttonText;
        return $this;
    }
}