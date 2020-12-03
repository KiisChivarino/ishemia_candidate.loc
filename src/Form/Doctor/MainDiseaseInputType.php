<?php

namespace App\Form\Doctor;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Services\Creator\DiagnosisCreatorService;
use App\Services\TemplateItems\FormTemplateItem;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MainDiseaseInputType
 * @package App\Form\Doctor
 */
class MainDiseaseInputType extends AbstractType
{
    /** @var DiagnosisCreatorService $diagnosisCreatorService */
    private $diagnosisCreatorService;

    /**
     * MainDiseaseInputType constructor.
     * @param DiagnosisCreatorService $diagnosisCreatorService
     */
    public function __construct(DiagnosisCreatorService $diagnosisCreatorService)
    {
        $this->diagnosisCreatorService = $diagnosisCreatorService;
    }

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
            ->add('mainDisease', TextType::class, [
                'label' => $templateItem->getContentValue('inputMainDisease'),
                'required' => false,
            ])
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                [$this, 'onPreSubmit']
            );
    }

    /**
     * Persist diagnosis with name string from form and add diagnosis entity to form instead name string
     * @param FormEvent $event
     * @throws NonUniqueResultException
     */
    public function onPreSubmit(FormEvent $event): void
    {
        $diagnosis = $this->diagnosisCreatorService->persistDiagnosis($event->getData()['mainDisease']);
        $event->setData(['mainDisease'=>$diagnosis]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => MedicalHistory::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}