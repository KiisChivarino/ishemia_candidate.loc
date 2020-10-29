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
class TemplateNewType extends AbstractType
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
            $builder
                ->add(
                    'parameter-'.$parameter->getId(), EntityType::class, [
                    'label' => $parameter->getName(),
                        'class' => TemplateParameterText::class,
                        'mapped' => false,
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

//        foreach ($options['parameters'] as $parameter) {
//
//            $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($parameter) {
//                $data = $event->getData();
//                $form = $event->getForm();
//
//                if (!empty($data['itemNew']['name'])) {
//                    $form->remove('item');
//
//                    $form->add('enabled'.$parameter->getId(), EntityType::class, array(
//                        'label' => $parameter->getName(),
//                        'class' => TemplateParameterText::class,
//                        'mapped' => false,
//                        'choice_label' => 'text',
//                        'query_builder' => function (TemplateParameterTextRepository $er) use ($parameter) {
////                        dd($options);
//                            return $er->createQueryBuilder('t')
//                                ->andWhere('t.enabled = true')
//                                ->leftJoin('t.templateParameter', 'templateParameter')
//                                ->andWhere('templateParameter.id = :val')
//                                ->setParameter('val', $parameter->getId());
//                        },
//                    ));
//                }
//            });
//        }
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
                ]
            )
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}