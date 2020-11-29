<?php

namespace App\Form\Admin;

use App\Entity\TemplateParameterText;
use App\Repository\TemplateParameterTextRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма параметра шаблона
 * Class TemplateParameterType
 *
 * @package App\Form\Admin
 */
class TemplateManyToManyTemplateParameterTextType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'templateParameterText', EntityType::class, [
                    'class' => TemplateParameterText::class,
                    'empty_data' => null,
                    'mapped' => false,
                    'choice_label' => 'name',
                    'query_builder' => function (TemplateParameterTextRepository $er) use ($options) {
                        return $er->createQueryBuilder('t')
                            ->andWhere('t.enabled = true')
                            ->leftJoin('t.templateParameter', 'templateParameter')
                            ->andWhere('templateParameter.id = :val')
                            ->setParameter('val', $options['data']);
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
            ->setDefaults(
                [
                    'data_class' => TemplateManyToManyTemplateParameterTextType::class,
               ]
            );
    }
}
