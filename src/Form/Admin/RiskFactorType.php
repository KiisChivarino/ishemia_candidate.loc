<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\RiskFactor;
use App\Repository\RiskFactorTypeRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RiskFactorType
 * форма факторов риска
 *
 * @package App\Form\Admin
 */
class RiskFactorType extends AbstractType
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
            ->add('name', null, ['label' => $templateItem->getContentValue('name')])
            ->add(
                'scores', IntegerType::class,
                [
                    'label' => $templateItem->getContentValue('scores'),
                    'attr' => ['min' => '0']
                ]
            )
            ->add(
                'enabled', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('enabled'),
                    'required' => false,
                ]
            )
            ->add(
                'riskFactorType', EntityType::class, [
                    'label' => $templateItem->getContentValue('riskFactorType'),
                    'class' => \App\Entity\RiskFactorType::class,
                    'choice_label' => 'name',
                    'query_builder' => function (RiskFactorTypeRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->where('p.enabled = true');
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
            ->setDefaults(['data_class' => RiskFactor::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
