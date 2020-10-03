<?php

namespace App\Form\Admin\Hospital;

use App\Controller\AppAbstractController;
use App\Entity\City;
use App\Entity\Region;
use App\Entity\Hospital;
use App\Repository\CityRepository;
use App\Repository\RegionRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class HospitalType
 * форма добавления/редактирования больниц
 *
 * @package App\Form\Admin\Hospital
 */
class HospitalType extends AbstractType
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
            ->add('address', null, ['label' => $templateItem->getContentValue('address')])
            ->add('name', null, ['label' => $templateItem->getContentValue('name')])
            ->add('phone', null, ['label' => $templateItem->getContentValue('phone')])
            ->add(
                'description', null, [
                'label' => $templateItem->getContentValue('description'),
                'attr' => ['class' => 'tinymce'],
            ]
            )
            ->add(
                'region', EntityType::class, [
                    'label' => $templateItem->getContentValue('region'),
                    'class' => Region::class,
                    'choice_label' => 'name',
                    'query_builder' => function (RegionRepository $er) {
                        return $er->createQueryBuilder('r')
                            ->where('r.enabled = true');
                    },
                ]
            )
            ->add(
                'city', EntityType::class, [
                    'label' => $templateItem->getContentValue('city'),
                    'class' => City::class,
                    'choice_label' => 'name',
                    'query_builder' => function (CityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->where('c.enabled = true');
                    },
                ]
            )
            ->add('email', EmailType::class, ['label' => $templateItem->getContentValue('email')])
            ->add('code', NumberType::class, ['label' => $templateItem->getContentValue('code')])
            ->add(
                'enabled', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('enabled'),
                    'required' => false,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => Hospital::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
