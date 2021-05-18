<?php

namespace App\Form\Patient;

use App\Controller\AppAbstractController;
use App\Entity\Patient;
use App\Entity\District;
use App\Repository\DistrictRepository;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class PatientType
 *
 * @package App\Form\Admin\Patient
 */
class PatientOptionalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        try {
            $builder
                ->add(
                    'snils',
                    TextType::class,
                    [
                        'label' => $templateItem->getContentValue('snils'),
                        'attr' => ['class' => 'snils_us'],
                        'required' => false
                    ]
                )
                ->add(
                    'insuranceNumber',
                    null,
                    [
                        'label' => $templateItem->getContentValue('insuranceNumber'),
                    ]
                )
                ->add('smsInforming', null, ['label' => $templateItem->getContentValue('smsInforming')])
                ->add('emailInforming', null, ['label' => $templateItem->getContentValue('emailInforming')])
                ->add(
                    'passport', null, [
                        'label' => $templateItem->getContentValue('passport'),
                        'attr' => [
                            'data-mask' => "0000 000000",
                            'placeholder' => '0000 000000',
                            "data-mask-clearifnotmatch" => "true"
                        ],
                        'required' => false,
                    ]
                )
                ->add(
                    'passportIssuingAuthority', TextType::class, [
                        'label' => $templateItem->getContentValue('passportIssuingAuthority'),
                        'required' => false,
                    ]
                )
                ->add(
                    'passportIssuingAuthorityCode', TextType::class, [
                        'label' => $templateItem->getContentValue('passportIssuingAuthorityCode'),
                        'required' => false,
                    ]
                )
                ->add(
                    'passportIssueDate', DateType::class, [
                        'label' => $templateItem->getContentValue('passportIssueDate'),
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd',
                        'required' => false,
                    ]
                )
                ->add('weight', null, [
                        'label' => $templateItem->getContentValue('weight'),
                        'attr' => ['min' => '29']
                    ]
                )
                ->add('height', null, [
                        'label' => $templateItem->getContentValue('height'),
                        'attr' => ['min' => '49']
                    ]
                )
                ->add(
                    'district', EntityType::class, [
                        'label' => $templateItem->getContentValue('district'),
                        'class' => District::class,
                        'choice_label' => 'name',
                        'required' => false,
                        'query_builder' => function (DistrictRepository $er) {
                            return $er->createQueryBuilder('d')
                                ->where('d.enabled = true');
                        },
                    ]
                );
        } catch (Exception $e) {
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => Patient::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
