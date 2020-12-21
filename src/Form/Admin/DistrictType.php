<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Region;
use App\Entity\District;
use App\Repository\RegionRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class DistrictType
 * форма добавления/редактирования района
 *
 * @package App\Form\Admin
 */
class DistrictType extends AbstractType
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
            ->add(
                'name', null, [
                    'label' => $templateItem->getContentValue('name')
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
            ->setDefaults(['data_class' => District::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
