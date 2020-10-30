<?php

namespace App\Form;

use App\Controller\AppAbstractController;
use App\Entity\Template;
use App\Entity\TemplateManyToManyTemplateParameterText;
use App\Entity\TemplateParameter;
use App\Entity\TemplateParameterText;
use App\Entity\TemplateType;
use App\Entity\TextByTemplate;
use App\Repository\TemplateParameterTextRepository;
use App\Repository\TemplateRepository;
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
class TextBySelectingTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
//        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
            $builder
                ->add(
                    'template', EntityType::class, [
                        'label' => 'Шаблон',
                        'class' => Template::class,
                        'mapped' => false,
                        'choice_label' => 'name',
                        'query_builder' => function (TemplateRepository $er) use ($options) {
                            return $er->createQueryBuilder('t')
                                ->andWhere('t.enabled = true')
                                ->leftJoin('t.templateType', 'templateType')
                                ->andWhere('templateType.id = :val')
                                ->setParameter('val', $options['type']);
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
                    'data_class' => TextByTemplate::class,
                    'type' => null,
                ]
            )
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}