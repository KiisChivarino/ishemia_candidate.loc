<?php

namespace App\Form;

use App\Controller\AppAbstractController;
use App\Entity\MedicalHistory;
use App\Entity\PatientMedicine;
use App\Repository\MedicalHistoryRepository;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PatientMedicineType
 * @package App\Form
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
            ->add('medicineName', TextType::class, [
                'label' => $templateItem->getContentValue('medicineName'),
                'required' => true,
            ])
            ->add('instruction', TextareaType::class, [
                'label' => $templateItem->getContentValue('instruction'),
                'required' => true,
                'attr' => ['class' => 'tinymce'],
            ])
            ->add(
                'dateBegin', DateType::class, [
                    'label' => $templateItem->getContentValue('dateBegin'),
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'required' => true
                ]
            )
            ->add(
                'medicalHistory', EntityType::class, [
                    'label' => $templateItem->getContentValue('medicalHistory'),
                    'class' => MedicalHistory::class,
                    'choice_label' => function ($medicalHistory) {
                        return
                            (new AuthUserInfoService())->getFIO($medicalHistory->getPatient()->getAuthUser(), true)
                            .': '.$medicalHistory->getDateBegin()->format('d.m.Y');
                    },
                    'query_builder' => function (MedicalHistoryRepository $er) {
                        return $er->createQueryBuilder('mh')
                            ->where('mh.enabled = true');
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
            ->setDefaults(['data_class' => PatientMedicine::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
