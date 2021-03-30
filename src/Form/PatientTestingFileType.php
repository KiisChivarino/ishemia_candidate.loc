<?php

namespace App\Form;

use App\Controller\AppAbstractController;
use App\Entity\PatientTestingFile;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PatientFileType
 *
 * @package App\Form
 */
class PatientTestingFileType extends AbstractType
{

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
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
                    'translation_domain' => 'messages',
                    'constraints' => [
                        new File(
                            [
                                'maxSize' => '5000ki',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/pjpeg',
                                    'image/png',
                                ],
                                'mimeTypesMessage' => $this->translator->trans('form.error.mime_type_message_error'),
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
            ->setDefaults(['data_class' => PatientTestingFile::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}