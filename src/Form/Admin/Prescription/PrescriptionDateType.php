<?php

namespace App\Form\Admin\Prescription;

use App\Controller\AppAbstractController;
use App\Entity\Prescription;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PrescriptionDateType
 *
 * @package App\Form\Admin
 */
class PrescriptionDateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];

        $builder
            ->add(
                'createdTime',
                DateTimeType::class,
                [
                    'date_widget' => 'single_text',
                    'date_label' => $templateItem->getContentValue('createdDateTime'),
                    'input_format' => 'Y-m-d H:i',
                ]
            )
            ->add(
                'completedTime',
                DateTimeType::class,
                [
                    'date_widget' => 'single_text',
                    'date_label' => $templateItem->getContentValue('completedDateTime'),
                    'input_format' => 'Y-m-d H:i',
                    'required' => false,
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['data_class' => Prescription::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
