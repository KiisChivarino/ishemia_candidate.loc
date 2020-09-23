<?php

namespace App\Form\Admin\PatientTesting;

use App\Controller\AppAbstractController;
use App\Entity\AnalysisGroup;
use App\Entity\PatientTesting;
use App\Repository\AnalysisGroupRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PatientTestingType
 * Форма Тестирования пациента
 *
 * @package App\Form\Admin
 */
class PatientTestingType extends AbstractType
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
            ->add(
                'analysisDate',
                DateType::class,
                [
                    'label' => $templateItem->getContentValue('analysisDate'),
                    'widget' => 'single_text',
                    'required' => false
                ]
            )
            ->add(
                'dateBegin', DateType::class,
                [
                    'label' => $templateItem->getContentValue('dateBegin'),
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'dateEnd', DateType::class,
                [
                    'label' => $templateItem->getContentValue('dateEnd'),
                    'widget' => 'single_text',
                    'required' => false
                ]
            )
            ->add(
                'processed', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('processed'),
                    'required' => false,
                ]
            )
            ->add(
                'analysisGroup', EntityType::class, [
                    'label' => $templateItem->getContentValue('analysisGroup'),
                    'class' => AnalysisGroup::class,
                    'choice_label' => 'name',
                    'query_builder' => function (AnalysisGroupRepository $er) {
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
            ->setDefaults(['data_class' => PatientTesting::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
