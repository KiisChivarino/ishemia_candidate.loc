<?php

namespace App\Form\Admin\AnalysisRate;

use App\Entity\AnalysisRate;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AnalysisRateRateMinType
 * @package App\Form\Admin\AnalysisRate
 */
class AnalysisRateRateEnabledAjaxType extends AbstractType
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
                'enabled', CheckboxType::class, [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'class' => 'xEditableField'
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => AnalysisRate::class,]);
    }
}