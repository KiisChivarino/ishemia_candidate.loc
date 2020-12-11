<?php


namespace App\Services\Notification;

use App\API\BEESMS;
use App\Entity\AuthUser;
use App\Entity\Patient;
use App\Entity\SMSNotification;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\InfoService\PatientInfoService;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use SimpleXMLElement;
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
    /** main smtp configuration */
    const
        ACCOUNT_NAME = 'lpvs@kvokka.com',
        ACCOUNT_PASSWORD = 'ipkh159fpos6',
        SMTP_HOST = 'smtp.yandex.ru',
        SMTP_PORT = 465,
        SMTP_ENCRYPTION = 'ssl'
    ;

    /** list of email templates */
    const
        DEFAULT_EMAIL_TEMPLATE = '/email/default.html.twig'
    ;

    /** Константы для email */
    const
        SITE_NAME = 'Медицина',
        ADDRESS_LINE_1 = 'ул. Ленина д.30',
        ADDRESS_LINE_2 = 'Курск, Россия',
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
    private $siteName;

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

    /** @var string  */
    private $addressLine2;

    /** @var string  */
    private $addressLine1;

    /**
     * EmailNotification constructor.
     * @param Environment $twig
     */
    public function __construct(
        Environment $twig
    )
    {
        $this->siteName = self::SITE_NAME;
        $this->addressLine1 = self::ADDRESS_LINE_1;
        $this->addressLine2 = self::ADDRESS_LINE_2;
        $this->twig = $twig;
        $this->serverHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
            . "://"
            . $_SERVER['HTTP_HOST']
        ;
    }

    /**
     * @throws ErrorException
     */
    private function sendEmail()
    {
        $transport = new Swift_SmtpTransport(
            self::SMTP_HOST,
            self::SMTP_PORT,
            self::SMTP_ENCRYPTION
        );

        $transport
            ->setUsername(self::ACCOUNT_NAME)
            ->setPassword(self::ACCOUNT_PASSWORD);

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
     * @throws ErrorException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendDefaultEmail()
    {
        $params = [
            'siteName' => $this->siteName,
            'greetings' => sprintf(self::GREETINGS, AuthUserInfoService::getFIO($this->patient->getAuthUser())),
            'header' => $this->header,
            'content' => $this->content,
            'buttonLink' => $this->buttonLink,
            'buttonText' => $this->buttonText,
            'addressLine1' => self::ADDRESS_LINE_1,
            'addressLine2' => self::ADDRESS_LINE_2
        ];

        $this->subject = 'Дефолтная тема письма';
        $this->sender = self::ACCOUNT_NAME;

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
     */
    public function addRecipentsArray(array $list=[])
    {
        $this->recipientList = $list;
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