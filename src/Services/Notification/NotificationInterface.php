<?php

namespace App\Services\Notification;

/**
 * Интерфейс он и в арфике интерфейс, а этот для уведомлений
 * Interface NotificationInterface
 * @package App\Services\Notification
 */
interface NotificationInterface
{
    /**
     * Функция уведомления
     */
    public function notify();
}