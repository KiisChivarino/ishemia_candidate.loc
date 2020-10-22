<?php

namespace App\Form;

use App\Controller\AppAbstractController;
use App\Entity\DischargeEpicrisisFile;
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
class DischargeEpicrisisFileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'file', FileType::class, [
                    'mapped' => false,
                    'required' => true,
                    'constraints' => [
                        new File(
                            [
                                'maxSize' => '5000ki',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/pjpeg',
                                ],
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
            ->setDefaults(['data_class' => DischargeEpicrisisFile::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}