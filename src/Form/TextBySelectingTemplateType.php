<?php

namespace App\Form;

use App\Controller\AppAbstractController;
use App\Entity\Template;
use App\Entity\TextByTemplate;
use App\Repository\TemplateRepository;
use App\Services\TemplateItems\FormTemplateItem;
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
class TextBySelectingTemplateType extends AbstractType
{
    /** @var string Template type option key */
    public const TYPE_OPTION_NAME = 'type';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                            ->setParameter('val', $options[self::TYPE_OPTION_NAME]);
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
                    self::TYPE_OPTION_NAME => null,
                ]
            )
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}