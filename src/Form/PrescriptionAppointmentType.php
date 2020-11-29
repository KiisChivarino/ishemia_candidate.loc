<?php

namespace App\Form;

use App\Controller\AppAbstractController;
use App\Entity\PrescriptionAppointment;
use App\Entity\Staff;
use App\Repository\StaffRepository;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PrescriptionAppointmentType
 * @package App\Form
 */
class PrescriptionAppointmentType extends AbstractType
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
            ->add('confirmedByStaff', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('confirmedByStaff'),
                    'required' => false,
                ]
            )
            ->add(
                'staff', EntityType::class, [
                    'label' => $templateItem->getContentValue('staff'),
                    'class' => Staff::class,
                    'choice_label' => function ($staff) {
                        return AuthUserInfoService::getFIO($staff->getAuthUser(), true);
                    },
                    'query_builder' => function (StaffRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.AuthUser', 'a')
                            ->where('a.enabled = true');
                    },
                    'required' => false,
                ]
            )
            ->add(
                'plannedDateTime', DateTimeType::class, [
                    'label' => $templateItem->getContentValue('plannedDateTime'),
                    'date_widget' => 'single_text',
                    'date_label' => $templateItem->getContentValue('plannedTimeDateLabel'),
                    'time_widget' => 'text',
                    'time_label' => $templateItem->getContentValue('plannedTimeTimeLabel'),
                    'input_format' => 'Y-m-d H:i',
                ]
            )
            ->add(
                'enabled', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('enabled'),
                    'required' => false,
                ]
            )
            ->add(
                'confirmedByStaff',
                CheckboxType::class,
                [
                    'label' => $templateItem->getContentValue('confirmedByStaff'),
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
            ->setDefaults(['data_class' => PrescriptionAppointment::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
