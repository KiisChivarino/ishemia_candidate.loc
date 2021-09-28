<?php

namespace App\Form\Admin\AnalysisRate;

use App\Entity\AnalysisRate;
use App\Entity\Gender;
use App\Repository\AnalysisRateRepository;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AnalysisRateEnabledType
 * @package App\Form\Admin\AnalysisRate
 */
class AnalysisRateGenderAjaxType extends AbstractType
{
    /** @var AnalysisRateRepository $analysisRateRepository */
    private $analysisRateRepository;

    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * AnalysisRateGenderAjaxType constructor.
     * @param AnalysisRateRepository $analysisRateRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        AnalysisRateRepository $analysisRateRepository,
        TranslatorInterface $translator
    )
    {
        $this->analysisRateRepository = $analysisRateRepository;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'gender', EntityType::class, [
                    'label' => false,
                    'class' => Gender::class,
                    'choice_label' => 'title',
                    'required' => false,
                    'attr' => [
                        'class' => 'xEditableField'
                    ]
                ]
            );

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $check = $this->analysisRateRepository->countAnalysisRateForGender($data);
            if($check !== 0){
                $event->getForm()->addError(new FormError(
                    $this->translator->trans('app_controller.error.double_gender_in_analysis_rate')
                ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => AnalysisRate::class,]);
    }
}