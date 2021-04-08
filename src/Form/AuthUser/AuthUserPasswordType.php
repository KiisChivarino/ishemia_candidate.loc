<?php

namespace App\Form\AuthUser;

use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AuthUserPasswordType
 *
 * @package App\Form\Admin\AuthUser
 */
class AuthUserPasswordType extends AbstractType
{
    /** @var string Name of flag "Is password required" */
    public const IS_PASSWORD_REQUIRED_OPTION_LABEL = 'isPasswordRequired';

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
            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => $templateItem->getContentValue('password'),
                    'attr' => ['pattern' => '\S{6,}'],
                    'help' => $templateItem->getContentValue('passwordHelp'),
                    'always_empty' => false,
                    'required' => $options[self::IS_PASSWORD_REQUIRED_OPTION_LABEL],
                    'trim' => true,
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
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class])
            ->setDefined(self::IS_PASSWORD_REQUIRED_OPTION_LABEL)
            ->setAllowedTypes(self::IS_PASSWORD_REQUIRED_OPTION_LABEL, ['bool']);
    }
}