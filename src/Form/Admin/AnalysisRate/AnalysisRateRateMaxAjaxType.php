<?php

namespace App\Form\Admin\AnalysisRate;

use App\Entity\AnalysisRate;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AnalysisRateRateMaxType
 * @package App\Form\Admin\AnalysisRate
 */
class AnalysisRateRateMaxAjaxType extends AbstractType
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
                'rateMax', NumberType::class,
                [
                    'label' => false,
                    'attr' => [
                        'class' => 'xEditableField'
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => AnalysisRate::class,]);
    }
}