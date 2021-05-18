<?php

namespace App\Form\PrescriptionMedicineType;

use App\Controller\AppAbstractController;
use App\Entity\PrescriptionMedicine;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PrescriptionMedicineType
 *
 * @package App\Form\Admin
 */
class PrescriptionMedicineType extends AbstractType
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
                'startingMedicationDate', DateType::class, [
                    'label' => $templateItem->getContentValue('startingMedicationDate'),
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'endMedicationDate', DateType::class, [
                    'label' => $templateItem->getContentValue('endMedicationDate'),
                    'widget' => 'single_text',
                    'required' => false
                ]
            )
           ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => PrescriptionMedicine::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
