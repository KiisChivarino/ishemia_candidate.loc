<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\PrescriptionTesting;
use App\Entity\Staff;
use App\Repository\StaffRepository;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PrescriptionTestingType
 *
 * @package App\Form\Admin
 */
class PrescriptionTestingType extends AbstractType
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
            ->add(
                'staff', EntityType::class, [
                    'label' => $templateItem->getContentValue('staff'),
                    'class' => Staff::class,
                    'choice_label' => function ($staff) {
                        return (new AuthUserInfoService())->getFIO($staff->getAuthUser(), true);
                    },
                    'query_builder' => function (StaffRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.AuthUser', 'a')
                            ->where('a.enabled = true');
                    },
                ]
            )
            ->add(
                'enabled',
                CheckboxType::class,
                [
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
            ->setDefaults(['data_class' => PrescriptionTesting::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
