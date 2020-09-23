<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\City;
use App\Entity\District;
use App\Entity\Region;
use App\Repository\DistrictRepository;
use App\Repository\RegionRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class CityType
 *
 * @package App\Form\Admin
 */
class CityType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options['formTemplateItem'];
        $builder
            ->add('name', null, ['label' => $templateItem->getContentValue('name')])
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
                'district', EntityType::class, [
                    'label' => $templateItem->getContentValue('district'),
                    'class' => District::class,
                    'choice_label' => 'name',
                    'query_builder' => function (DistrictRepository $er) {
                        return $er->createQueryBuilder('d')
                            ->where('d.enabled = true');
                    },
                ]
            )
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
            ->setDefaults(['data_class' => City::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
