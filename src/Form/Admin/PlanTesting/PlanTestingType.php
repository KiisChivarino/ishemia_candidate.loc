<?php

namespace App\Form\Admin\PlanTesting;

use App\Controller\AppAbstractController;
use App\Entity\AnalysisGroup;
use App\Entity\PlanTesting;
use App\Repository\AnalysisGroupRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PlanTestingType
 * form of creating/editing plan testing
 *
 * @package App\Form\Admin\PlanTesting
 */
class PlanTestingType extends AbstractType
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
            //todo изменить периоды на timeRanges
//            ->add(
//                'period', TextType::class,
//                [
//                    'label' => $templateItem->getContentValue('period'),
//                ]
//            )
//            ->add(
//                'periodCount', IntegerType::class,
//                [
//                    'label' => $templateItem->getContentValue('periodCount'),
//                    'attr' => ['min' => '1']
//                ]
//            )
            ->add(
                'enabled', CheckboxType::class, [
                    'label' => $templateItem->getContentValue('enabled'),
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
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => PlanTesting::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
