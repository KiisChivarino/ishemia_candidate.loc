<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class PatientChangeEventListener implements EventSubscriberInterface
{
    /** @var TranslatorInterface */
    private $translator;

    /**
     * PatientOptionalType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'submit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event): void
    {
        $data = $event->getData();
        if ($data->getEmailInforming() && $data->getAuthUser()->getEmail() === null) {
            $errorMessage = $this->translator->trans('form.error.email_notification_without_email');
            $event->getForm()->addError((new FormError($errorMessage)));
        }
    }
}