<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Region;
use App\Entity\Country;
use App\Repository\CountryRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class RegionType
 * form to create/edit Region
 *
 * @package App\Form\Admin
 */
class RegionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add('name', null, ['label' => $templateItem->getContentValue('name')])
            ->add('region_number', null, ['label' => $templateItem->getContentValue('region_number')])
            ->add(
                'enabled', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('enabled'),
                    'required' => false,
                ]
            )
            ->add(
                'country', EntityType::class, [
                    'label' => $templateItem->getContentValue('country'),
                    'class' => Country::class,
                    'choice_label' => 'name',
                    'query_builder' => function (CountryRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->where('c.enabled = true');
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
            ->setDefaults(['data_class' => Region::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
