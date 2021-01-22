<?php

namespace App\Form\Admin\Patient;

use App\Controller\AppAbstractController;
use App\Entity\City;
use App\Entity\Hospital;
use App\Entity\Patient;
use App\Services\TemplateItems\FormTemplateItem;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Class PatientType
 *
 * @package App\Form\Admin\Patient
 */
class PatientRequiredType extends AbstractType
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
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add(
                'dateBirth',
                DateType::class,
                [
                    'label' => $templateItem->getContentValue('dateBirth'),
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => true,
                ]
            )
            ->add('address', null, ['label' => $templateItem->getContentValue('address')])
            ->add(
                'heartAttackDate',
                DateType::class,
                [
                    'label' => $templateItem->getContentValue('heartAttackDate'),
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => true,
                ]
            );
        if (!$options['isDoctorLPU']) {
            $builder
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
                        'required' => true
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
                        'required' => true,
                    ]
                );
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => Patient::class, 'isDoctorLPU' => null])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
