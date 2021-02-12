<?php

namespace App\Form;

use App\Controller\AppAbstractController;
use App\Entity\AppointmentType;
use App\Entity\PatientAppointment;
use App\Entity\Staff;
use App\Repository\AppointmentTypeRepository;
use App\Repository\StaffRepository;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PatientAppointmentType
 *
 * @package App\Form
 */
class PrescriptionPatientAppointmentType extends AbstractType
{
    /** @var string Name of objective status text option */
    public const OBJECTIVE_STATUS_TEXT_OPTION_NAME = 'objectiveStatusText';

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
                'staff', EntityType::class, [
                    'label' => $templateItem->getContentValue('staff'),
                    'class' => Staff::class,
                    'required' => true,
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
            )
            ->add(
                'complaintsComment', null, [
                    'label' => $templateItem->getContentValue('complaintsComment'),
                    'attr' => ['class' => 'tinymce'],
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => PatientAppointment::class,
                self::OBJECTIVE_STATUS_TEXT_OPTION_NAME => null
            ])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}