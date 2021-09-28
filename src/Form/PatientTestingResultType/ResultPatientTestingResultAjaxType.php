<?php

namespace App\Form\PatientTestingResultType;

use App\Entity\PatientTestingResult;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ResultPatientTestingResultType
 * @package App\Form\PatientTestingResultType
 */
class ResultPatientTestingResultAjaxType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'result', NumberType::class, [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'class' => 'xEditableField'
                    ]
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => PatientTestingResult::class,]);
    }
}
