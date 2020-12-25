<?php

namespace App\Services\SMSProviders;

use App\API\BEESMS;

/**
 * Class BeelineSMSProvider
 * Сервис для работы с Beeline SMS
 * @package App\Services\SMSProviders
 */
class BeelineSMSProvider
{
    /** @var array Параметры СМС */
    private $smsParameters;

    /** @var BEESMS Класс для работы с API Beeline SMS */
    private $sms;

    /** @var string Текст сообщения для отправки */
    private $text;

    /** @var string Получатель сообщения */
    private $target;

    /** @var string Дата и время начала выборки в стандартизированном формате */
    private $dateTimeStart;

    /** @var string Дата и время окончания выборки в стандартизированном формате */
    private $dateTimeEnd;

    /**
     * SMS notification constructor.
     * @param array $smsParameters
     */
    public function __construct(array $smsParameters) {
        $this->smsParameters = $smsParameters;
        $this->sms = new BEESMS($this->smsParameters['user'], $this->smsParameters['password']);
    }

    /**
     * Send SMS
     * @return false|string
     */
    public function send(): string
    {
        return $this->sms->post_message($this->text, $this->target, $this->smsParameters['sender']);
    }

    /**
     * Check SMS form server
     * @return string
     */
    public function check(): string
    {
        return $this->sms->status_sms_date($this->dateTimeStart, $this->dateTimeEnd);
    }

    /**
     * Get SMS form inbox
     * @return string
     */
    public function getMessages(): string
    {
        return $this->sms->status_inbox(false,0, $this->dateTimeStart, $this->dateTimeEnd);
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param string $target
     * @return $this
     */
    public function setTarget(string $target): self
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @param string $dateTimeStart
     * @return $this
     */
    public function setDateTimeStart(string $dateTimeStart): self
    {
        $this->dateTimeStart = $dateTimeStart;
        return $this;
    }

    /**
     * @param string $dateTimeEnd
     * @return $this
     */
    public function setDateTimeEnd(string $dateTimeEnd): self
    {
        $this->dateTimeEnd = $dateTimeEnd;
        return $this;
    }

}