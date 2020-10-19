<?php

namespace App\Form\Admin\PatientAppointment;

use App\Controller\AppAbstractController;
use App\Entity\AppointmentType;
use App\Entity\PatientAppointment;
use App\Repository\AppointmentTypeRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AppointmentTypeType
 *
 * @package App\Form\Admin\PatientAppointment
 */
class AppointmentTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add(
                'appointmentType', EntityType::class, [
                    'label' => $templateItem->getContentValue('appointmentType'),
                    'class' => AppointmentType::class,
                    'required' => true,
                    'choice_label' => function (AppointmentType $appointmentType) {
                        return $appointmentType->getName();
                    },
                    'query_builder' => function (AppointmentTypeRepository $er) {
                        return $er->createQueryBuilder('at')
                            ->where('at.enabled = true');
                    },
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => PatientAppointment::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}