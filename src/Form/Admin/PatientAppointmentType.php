<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\AppointmentType;
use App\Entity\PatientAppointment;
use App\Entity\Staff;
use App\Repository\AppointmentTypeRepository;
use App\Repository\StaffRepository;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PatientAppointmentType
 *
 * @package App\Form
 */
class PatientAppointmentType extends AbstractType
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
                'staff', EntityType::class, [
                    'label' => $templateItem->getContentValue('staff'),
                    'class' => Staff::class,
                    'choice_label' => function ($staff) {
                        return (new AuthUserInfoService())->getFIO($staff->getAuthUser(), true);
                    },
                    'query_builder' => function (StaffRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.AuthUser', 'a')
                            ->where('a.enabled = true');
                    },
                ]
            )
            ->add(
                'appointmentTime', DateTimeType::class, [
                    'label' => $templateItem->getContentValue('appointmentTime'),
                    'date_widget' => 'single_text',
                    'date_label' => $templateItem->getContentValue('appointmentTimeDateLabel'),
                    'time_widget' => 'single_text',
                    'time_label' => $templateItem->getContentValue('appointmentTimeTimeLabel'),
                ]
            )
            ->add('description', null, ['label' => $templateItem->getContentValue('description')])
            ->add(
                'appointmentType', EntityType::class, [
                    'label' => $templateItem->getContentValue('appointmentType'),
                    'class' => AppointmentType::class,
                    'choice_label' => function (AppointmentType $appointmentType) {
                        return $appointmentType->getName();
                    },
                    'query_builder' => function (AppointmentTypeRepository $er) {
                        return $er->createQueryBuilder('at')
                            ->where('at.enabled = true');
                    },
                ]
            )
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => $templateItem->getContentValue('enabled'),
                    'required' => false,
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
