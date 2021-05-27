<?php

namespace App\Form\Admin\Prescription;

use App\Controller\AppAbstractController;
use App\Entity\Prescription;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PrescriptionEditType
 *
 * @package App\Form\Admin\Prescription
 */
class PrescriptionEditType extends AbstractType
{
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
            ->add(
                'isPatientConfirmed', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('isPatientConfirmed'),
                    'required' => false,
                ]
            );
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($templateItem) {
                /** @var Prescription $prescription */
                $prescription = $event->getData();
                if (PrescriptionInfoService::countChildren($prescription)) {
                    $form = $event->getForm();
                    $form->add(
                        'isCompleted', CheckboxType::class, [
                            'label' => $templateItem->getContentValue('isCompleted'),
                            'required' => false,
                        ]
                    );
                }
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => Prescription::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}