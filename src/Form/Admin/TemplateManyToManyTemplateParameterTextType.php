<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\TemplateManyToManyTemplateParameterText;
use App\Entity\TemplateParameter;
use App\Entity\TemplateParameterText;
use App\Entity\TemplateType;
use App\Repository\TemplateParameterTextRepository;
use App\Repository\TemplateTypeRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dd($options['data']);
        /** @var FormTemplateItem $templateItem */
//        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];

        $builder
            ->add(
                'templateParameterText', EntityType::class, [
//                    'label' => $templateItem->getContentValue('text'),
                    'class' => TemplateParameterText::class,
                    'empty_data' => null,
                    'mapped' => false,
                    'choice_label' => 'name',
                    'query_builder' => function (TemplateParameterTextRepository $er) use ($options) {
//                        dd($options);
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
            ->setDefaults(['data_class' => TemplateManyToManyTemplateParameterTextType::class,
               ])
//            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
//            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class])
        ;
    }
}
