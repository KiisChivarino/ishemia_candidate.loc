<?php

namespace App\Form\Admin\Prescription;

use App\Controller\AppAbstractController;
use App\Entity\Prescription;
use App\Services\CompletePrescription\CompletePrescriptionService;
use App\Services\EntityActions\Creator\MedicalRecordCreatorService;
use App\Services\InfoService\PrescriptionInfoService;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PrescriptionEditType
 *
 * @package App\Form\Admin\Prescription
 */
class PrescriptionEditType extends AbstractType
{
    /**
     * @var CompletePrescriptionService
     */
    private $completePrescriptionService;

    /**
     * @var MedicalRecordCreatorService
     */
    private $medicalRecordCreatorService;

    public function __construct(
        CompletePrescriptionService $completePrescriptionService,
        MedicalRecordCreatorService $medicalRecordCreatorService
    )
    {
        $this->completePrescriptionService = $completePrescriptionService;
        $this->medicalRecordCreatorService = $medicalRecordCreatorService;
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
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($templateItem) {
                    /** @var Prescription $prescription */
                    $prescription = $event->getData();
                    if (PrescriptionInfoService::countChildren($prescription) && !$prescription->getIsCompleted()) {
                        $form = $event->getForm();
                        $form->add(
                            'isCompleted', CheckboxType::class, [
                                'label' => $templateItem->getContentValue('isCompleted'),
                                'required' => false,
                            ]
                        );
                    }
                }
            )
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Prescription $prescription */
                $prescription = $event->getData();
                if ($prescription->getIsCompleted() === true) {
                    $this->completePrescriptionService->completePrescription(
                        $prescription,
                        $this->medicalRecordCreatorService
                    );
                }
            })
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => Prescription::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}