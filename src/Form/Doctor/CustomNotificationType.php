<?php

namespace App\Form\Doctor;

use App\Controller\AppAbstractController;
use App\Entity\Notification;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PatientPersonalData
 *
 * @package App\Form\Doctor
 */
class CustomNotificationType extends AbstractType
{
    /** @var SessionInterface */
    private $session;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * CustomNotificationType constructor.
     * @param SessionInterface $session
     * @param TranslatorInterface $translator
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add('text', TextareaType::class, [
                    'mapped' => false,
                    'label' => $templateItem->getContentValue('text')
                ]
            )->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                if(trim($event->getData()['text']) == ''){
                    $errorMessage = $this->translator->trans('message.validate.message.error');
                    $this->session->getFlashBag()->add(
                        'error',
                        $errorMessage
                    );
                    $event->getForm()->addError((new FormError($errorMessage)));
                }
            });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => Notification::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}