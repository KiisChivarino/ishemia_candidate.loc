<?php


namespace App\Form;


use App\Controller\AppAbstractController;
use App\Entity\AppointmentType;
use App\Entity\PatientAppointment;
use App\Repository\AppointmentTypeRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PatientAppointmentType extends AbstractType
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
                'appointmentType', EntityType::class, [
                    'label' => $templateItem->getContentValue('appointmentType'),
                    'class' => AppointmentType::class,
                    'choice_label' => 'name',
                    'query_builder' => function (AppointmentTypeRepository $er) {
                        return $er->createQueryBuilder('d')
                            ->where('d.enabled = true');
                    },
                ]
            );
        ;
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