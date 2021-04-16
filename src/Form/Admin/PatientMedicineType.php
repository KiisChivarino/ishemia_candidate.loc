<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\PatientMedicine;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PatientMedicineType
 *
 * @package App\Form\Admin
 */
class PatientMedicineType extends AbstractType
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
                'medicineName', null, [
                    'label' => $templateItem->getContentValue('medicineName'),
                ]
            )
            ->add(
                'instruction', null, [
                    'label' => $templateItem->getContentValue('instruction'),
                    'attr' => ['class' => 'tinymce'],
                    'required' => true
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => PatientMedicine::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
