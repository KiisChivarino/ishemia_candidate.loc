<?php

namespace App\Form;

use App\Controller\AppAbstractController;
use App\Entity\PatientFile;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

/**
 * Class PatientFileType
 *
 * @package App\Form
 */
class PatientFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        /** @var FormTemplateItem $templateItem */
//        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $builder
            ->add(
                'file', FileType::class, [
//                    'label' => $templateItem->getContentValue('fileName'),
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File(
                            [
                                'maxSize' => '1024k',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/pjpeg',
                                ],
//                                'mimeTypesMessage' => $templateItem->getContentValue('notValidMimeType'),
                            ]
                        )
                    ],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => PatientFile::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}