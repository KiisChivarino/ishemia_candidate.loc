<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Prescription;
use App\Entity\Staff;
use App\Repository\StaffRepository;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PrescriptionType
 *
 * @package App\Form\Admin
 */
class PrescriptionType extends AbstractType
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
            ->add(
                'staff', EntityType::class, [
                    'label' => $templateItem->getContentValue('staffFio'),
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
                'createdTime',
                DateTimeType::class,
                [
                    'date_widget' => 'single_text',
                    'date_label' => $templateItem->getContentValue('createdDateTime'),
                    'input_format' => 'Y-m-d H:i',
                ]
            )
            ->add(
                'completedTime',
                DateTimeType::class,
                [
                    'date_widget' => 'single_text',
                    'date_label' => $templateItem->getContentValue('completedDateTime'),
                    'input_format' => 'Y-m-d H:i',
                ]
            )
            ->add(
                'description', null, [
                    'label' => $templateItem->getContentValue('description'),
                    'attr' => ['class' => 'tinymce'],
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
            ->setDefaults(['data_class' => Prescription::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
