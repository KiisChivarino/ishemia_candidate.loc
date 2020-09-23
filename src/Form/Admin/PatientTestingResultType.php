<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Analysis;
use App\Entity\AnalysisRate;
use App\Entity\PatientTesting;
use App\Entity\PatientTestingResult;
use App\Repository\AnalysisRateRepository;
use App\Services\InfoService\AnalysisRateInfoService;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PatientTestingResultType
 * Форма заполнения результатов анализа
 *
 * @package App\Form\Admin
 */
class PatientTestingResultType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Analysis $analysis */
        $analysis = $options['analysis'] ?? null;
        /** @var PatientTesting $patientTesting */
        $patientTesting = $options['patientTesting'] ?? null;
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add(
                'result', NumberType::class, [
                    'label' => $templateItem->getContentValue('result'),
                    'required' => false
                ]
            )
            ->add(
                'analysisRate', EntityType::class, [
                    'class' => AnalysisRate::class,
                    'label' => $templateItem->getContentValue('analysisRate'),
                    'choice_label' => function ($analysisRate) {
                        return (new AnalysisRateInfoService())->getAnalysisRateInfoString($analysisRate);
                    },
                    'query_builder' => function (AnalysisRateRepository $er) use ($analysis, $patientTesting) {
                        $qb = $er->createQueryBuilder('ar')
                            ->where(
                                'ar.enabled = true
                                and ar.analysis= :analysis'
                            )
                            ->setParameter('analysis', $analysis);
                        if ($analysis) {
                            $qb->andWhere('ar.analysis= :analysis')
                                ->setParameter('analysis', $analysis);
                        }
                        return $qb;
                    },
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => PatientTestingResult::class,])
            ->setDefined('patientTesting')
            ->setAllowedTypes('patientTesting', [PatientTesting::class, 'string'])
            ->setDefined('analysis')
            ->setAllowedTypes('analysis', [Analysis::class, 'string', 'null'])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
