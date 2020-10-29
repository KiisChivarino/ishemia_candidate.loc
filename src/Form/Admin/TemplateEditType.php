<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Template;
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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма параметра шаблона
 * Class TemplateParameterType
 *
 * @package App\Form\Admin
 */
class TemplateEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add('name', null, ['label' => $templateItem->getContentValue('name')])
            ->add(
                'enabled', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('enabled'),
                    'required' => false,
                ]
            );
        foreach ($options['parameters'] as $parameter) {
            foreach ($options['template']->getTemplateManyToManyTemplateParameterTexts() as $text) {
                if ($text->getTemplateParameterText()->getTemplateParameter()->getId() == $parameter->getId()) {
                    $defaultSelect = $text->getTemplateParameterText();
                    $textId = $text->getId();
                }
            }
            if (!isset($textId)) {
                $textId = 'new';
            }
            $builder
                ->add(
                    'parameter-'.$parameter->getId().'-'. $textId, EntityType::class, [
                    'label' => $parameter->getName(),
                        'class' => TemplateParameterText::class,
                        'mapped' => false,
                        'data' => $defaultSelect ?? null,
                        'choice_label' => 'text',
                        'query_builder' => function (TemplateParameterTextRepository $er) use ($parameter) {
                            return $er->createQueryBuilder('t')
                                ->andWhere('t.enabled = true')
                                ->leftJoin('t.templateParameter', 'templateParameter')
                                ->andWhere('templateParameter.id = :val')
                                ->setParameter('val', $parameter->getId());
                        },
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
            ->setDefaults(
                [
                    'data_class' => Template::class,
                    'parameters' => null,
                    'template' => null,
                ]
            )
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}