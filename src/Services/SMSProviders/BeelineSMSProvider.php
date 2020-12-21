<?php

namespace App\Services\SMSProviders;

use App\API\BEESMS;

class BeelineSMSProvider
{
    /** @var array */
    private $smsParameters;

    /** @var BEESMS */
    private $sms;

    /** @var string */
    private $text;

    /** @var string */

    private $target;

    /** @var string */
    private $dateFrom;

    /** @var string */
    private $dateTo;

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
        return $this->sms->status_sms_date($this->dateFrom, $this->dateTo);
    }

    /**
     * Get SMS form inbox
     * @return string
     */
    public function getMessages(): string
    {
        return $this->sms->status_inbox(false,0, $this->dateFrom, $this->dateTo);
    }

    /**
     * @param string $text
     * @return BeelineSMSProvider
     */
    public function setText(string $text): BeelineSMSProvider
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param string $target
     * @return BeelineSMSProvider
     */
    public function setTarget(string $target): BeelineSMSProvider
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @param string $dateFrom
     * @return BeelineSMSProvider
     */
    public function setDateFrom(string $dateFrom): BeelineSMSProvider
    {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    /**
     * @param string $dateTo
     * @return BeelineSMSProvider
     */
    public function setDateTo(string $dateTo): BeelineSMSProvider
    {
        $this->dateTo = $dateTo;
        return $this;
    }

}