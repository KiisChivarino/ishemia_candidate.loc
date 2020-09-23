<?php

namespace App\Form\Admin\AuthUser;

use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AuthUserType
 * edit/create form of AuthUser controller
 *
 * @package App\Form\Admin\AuthUser
 */
class AuthUserType extends AbstractType
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
            ->add('firstName', null, ['label' => $templateItem->getContentValue('firstName')])
            ->add('lastName', null, ['label' => $templateItem->getContentValue('lastName')])
            ->add('patronymicName', null, ['label' => $templateItem->getContentValue('patronymicName')])
            ->add(
                'phone',
                TelType::class,
                [
                    'label' => $templateItem->getContentValue('phone'),
                    'attr' => ['class'=>'phone_us'],
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
            )
            ->add('description', null, ['label' => $templateItem->getContentValue('description')])
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
            ->setDefaults(['data_class' => AuthUser::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
