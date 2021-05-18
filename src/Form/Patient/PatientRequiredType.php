<?php

namespace App\Form\Patient;

use App\Controller\AppAbstractController;
use App\Entity\Patient;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

/**
 * Class PatientType
 *
 * @package App\Form\Admin\Patient
 */
class PatientRequiredType extends AbstractType
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
                'dateBirth',
                DateType::class,
                [
                    'label' => $templateItem->getContentValue('dateBirth'),
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => true,
                ]
            )
            ->add('address', null, ['label' => $templateItem->getContentValue('address')])
            ->add(
                'heartAttackDate',
                DateType::class,
                [
                    'label' => $templateItem->getContentValue('heartAttackDate'),
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => true,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => Patient::class])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
