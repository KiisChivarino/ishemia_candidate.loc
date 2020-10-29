<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\TemplateParameter;
use App\Entity\TemplateParameterText;
use App\Entity\TemplateType;
use App\Repository\TemplateParameterRepository;
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
class TemplateParameterTextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add('text', null, ['label' => $templateItem->getContentValue('text')])
            ->add(
                'enabled', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('enabled'),
                    'required' => false,
                ]
            )
            ->add(
                'templateParameter', EntityType::class, [
                    'label' => $templateItem->getContentValue('templateParameter'),
                    'class' => TemplateParameter::class,
                    'choice_label' => 'name',
                    'query_builder' => function (TemplateParameterRepository $er) {
                        return $er->createQueryBuilder('t')
                            ->where('t.enabled = true');
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
            ->setDefaults(['data_class' => TemplateParameterText::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
