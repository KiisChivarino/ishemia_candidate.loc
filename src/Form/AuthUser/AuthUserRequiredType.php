<?php

namespace App\Form\AuthUser;

use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

    /**
     * AuthUserRequiredType constructor.
     * @param $caseType
     */
    public function __construct($caseType)
    {
        $this->CASE_TYPE = $caseType;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
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
                ])
                ->add(
                    'phone',
                    TelType::class,
                    [
                        'label' => $templateItem->getContentValue('phone'),
                        'attr' => ['class' => 'phone_us'],
                        'help' => $templateItem->getContentValue('phoneHelp'),
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
