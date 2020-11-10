<?php

namespace App\Form\Admin\Patient;

use App\Controller\AppAbstractController;
use App\Entity\City;
use App\Entity\Hospital;
use App\Entity\Patient;
use App\Entity\District;
use App\Repository\DistrictRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Class PatientType
 *
 * @package App\Form\Admin\Patient
 */
class PatientType extends AbstractType
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /**
     * PatientType constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
                'heartAttackDate',
                DateType::class,
                [
                    'label' => $templateItem->getContentValue('heartAttackDate'),
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                ]
            )
            ->add(
                'dateBirth',
                DateType::class,
                [
                    'label' => $templateItem->getContentValue('dateBirth'),
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                ]
            )
            ->add(
                'snils',
                TextType::class,
                [
                    'label' => $templateItem->getContentValue('snils'),
                    'attr' => ['class' => 'snils_us']
                        /*[
                            'pattern' => '^\d{3}-\d{3}-\d{3}-\d{2}$',
                            'placeholder' => '000-000-000-00',
                            'data-mask' => "000-000-000-00",
                            "data-mask-clearifnotmatch" => "true"
                        ]*/,
//                    'help' => '888-888-888-88',
                    'required' => false
                ]
            )
            ->add('insuranceNumber', null, ['label' => $templateItem->getContentValue('insuranceNumber')])
            ->add('address', null, ['label' => $templateItem->getContentValue('address')])
            ->add('smsInforming', null, ['label' => $templateItem->getContentValue('smsInforming')])
            ->add('emailInforming', null, ['label' => $templateItem->getContentValue('emailInforming')])
            ->add(
                'passport', null, [
                    'label' => $templateItem->getContentValue('passport'),
                    'attr' => [
                        'data-mask' => "0000 000000",
                        'placeholder' => '0000 000000',
                        "data-mask-clearifnotmatch" => "true"
                    ]

                ]
            )
            ->add('weight', null, ['label' => $templateItem->getContentValue('weight')])
            ->add('height', null, ['label' => $templateItem->getContentValue('height')])
            ->add(
                'city', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('city'),
                    'method' => 'POST',
                    'multiple' => false,
                    'remote_route' => 'find_city_ajax',
                    'class' => City::class,
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 2,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('cityPlaceholder'),
                ]
            )
            ->add(
                'district', EntityType::class, [
                    'label' => $templateItem->getContentValue('district'),
                    'class' => District::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'query_builder' => function (DistrictRepository $er) {
                        return $er->createQueryBuilder('d')
                            ->where('d.enabled = true');
                    },
                ]
            )
            ->add(
                'hospital', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('hospital'),
                    'method' => 'POST',
                    'multiple' => false,
                    'remote_route' => 'find_hospital_ajax',
                    'class' => Hospital::class,
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 0,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'delay' => 250,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('hospitalPlaceholder'),
                    'remote_params' => ['city' => '0'],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => Patient::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
