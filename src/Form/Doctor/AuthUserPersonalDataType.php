<?php

namespace App\Form\Doctor;

use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PatientPersonalData
 *
 * @package App\Form\Doctor
 */
class AuthUserPersonalDataType extends AbstractType
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
            ->add('lastName', null, ['label' => $templateItem->getContentValue('lastName')])
            ->add('firstName', null, ['label' => $templateItem->getContentValue('firstName')])
            ->add('patronymicName', null, ['label' => $templateItem->getContentValue('patronymicName')])
            ->add(
                'phone',
                TelType::class,
                [
                    'label' => $templateItem->getContentValue('phone'),
                    'attr' => ['class' => 'phone_us'],
                    'help' => $templateItem->getContentValue('phoneHelp'),
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => $templateItem->getContentValue('email'),
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
            ->setDefaults(['data_class' => AuthUser::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}