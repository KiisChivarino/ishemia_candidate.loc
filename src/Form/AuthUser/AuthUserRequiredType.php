<?php

namespace App\Form\AuthUser;

use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

/**
 * Class AuthUserType
 * edit/create form of AuthUser controller
 *
 * @package App\Form\Admin\AuthUser
 */
class AuthUserRequiredType extends AbstractType
{

    /**
     * @var array
     * yaml:config/constant/case.yaml
     */
    private $CASE_TYPE;

    /** @var AuthUser */
    private $userData;

    /**
     * AuthUserRequiredType constructor.
     * @param $caseType
     * @param Security $security
     */
    public function __construct($caseType, Security $security)
    {
        $this->CASE_TYPE = $caseType;
        $this->userData = $security->getUser();
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($templateItem) {
            /**
             * Данная функция не позволяет авторизованному пользователю редактировать СВОЙ ЖЕ НОМЕР ТЕЛЕФОНА
             * Т.К. это уникальный идентификатор. Ему поле с телефоном доступно ТОЛЬКО ДЛЯ ЧТЕНИЯ
             */
            $form = $event->getForm();
            $isMyPhone = $this->userData->getPhone() === $event->getData()->getPhone();
            $form->add(
                'phone',
                TelType::class,
                [
                    'disabled' => $isMyPhone,
                    'label' => $templateItem->getContentValue('phone'),
                    'attr' => [
                        'readonly' => $isMyPhone,
                        'class' => 'phone_us'
                    ],
                    'help' => $templateItem->getContentValue('phoneHelp'),
                ]
            );
        });
        $builder
            ->add('lastName', TextType::class, [
                'label' => $templateItem->getContentValue('lastName'),
                'attr' => [
                    'data-case' => $this->CASE_TYPE['firstUpper']
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => $templateItem->getContentValue('firstName'),
                'attr' => [
                    'data-case' => $this->CASE_TYPE['firstUpper']
                ]
            ])
            ->add('patronymicName', TextType::class, [
                'label' => $templateItem->getContentValue('patronymicName'),
                'required' => false,
                'attr' => [
                    'data-case' => $this->CASE_TYPE['firstUpper'],
                ]
            ]);
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
