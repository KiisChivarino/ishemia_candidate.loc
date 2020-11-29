<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Hospital;
use App\Entity\Position;
use App\Entity\Staff;
use App\Repository\PositionRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Class StaffType
 *
 * @package App\Form\Admin
 */
class StaffType extends AbstractType
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
                'hospital', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('hospital'),
                    'method' => 'POST',
                    'multiple' => false,
                    'remote_route' => 'find_hospital_ajax',
                    'class' => Hospital::class,
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 0,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'delay' => 250,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('hospitalPlaceholder'),
                    'remote_params' => ['city' => '0'],
                    'required' => false
                ]
            )
            ->add(
                'position', EntityType::class, [
                    'label' => $templateItem->getContentValue('position'),
                    'class' => Position::class,
                    'choice_label' => 'name',
                    'query_builder' => function (PositionRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->where('p.enabled = true');
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
            ->setDefaults(['data_class' => Staff::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
