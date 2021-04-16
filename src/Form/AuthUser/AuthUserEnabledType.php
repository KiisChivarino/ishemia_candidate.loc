<?php


namespace App\Form\AuthUser;


use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthUserEnabledType extends AbstractType
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
                    'enabled', CheckboxType::class, [
                        'label' => $templateItem->getContentValue('enabled'),
                        'required' => false,
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
            ->setDefaults(['data_class' => AuthUser::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
