<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\Medicine;
use App\Entity\PrescriptionMedicine;
use App\Entity\ReceptionMethod;
use App\Entity\Staff;
use App\Repository\ReceptionMethodRepository;
use App\Repository\StaffRepository;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Class PrescriptionMedicineType
 *
 * @package App\Form\Admin
 */
class PrescriptionMedicineType extends AbstractType
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
                'medicine', Select2EntityType::class, [
                    'label' => $templateItem->getContentValue('medicine'),
                    'method' => 'POST',
                    'multiple' => false,
                    'remote_route' => 'find_medicine_ajax',
                    'class' => Medicine::class,
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 0,
                    'page_limit' => 1,
                    'allow_clear' => true,
                    'delay' => 250,
                    'language' => 'ru',
                    'placeholder' => $templateItem->getContentValue('medicinePlaceholder'),
                ]
            )
            ->add('instruction', null, ['label' => $templateItem->getContentValue('instruction')])
            ->add(
                'receptionMethod', EntityType::class, [
                'label' => $templateItem->getContentValue('receptionMethod'),
                'class' => ReceptionMethod::class,
                'choice_label' => 'name',
                'required' => false,
                'query_builder' => function (ReceptionMethodRepository $er) {
                    return $er->createQueryBuilder('rm')
                        ->where('rm.enabled = true');
                },
            ]
            )
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
            ->setDefaults(['data_class' => PrescriptionMedicine::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
