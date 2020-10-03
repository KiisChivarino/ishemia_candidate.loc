<?php

namespace App\Form\Admin;

use App\Controller\AppAbstractController;
use App\Entity\BlogItem;
use App\Entity\BlogRecord;
use App\Services\InfoService\BlogRecordInfoService;
use App\Services\TemplateItems\FormTemplateItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BlogItemType
 *
 * @package App\Form\Admin
 */
class BlogItemType extends AbstractType
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
            ->add('title', null, ['label' => $templateItem->getContentValue('itemTitle')])
            ->add(
                'description', null, [
                    'label' => $templateItem->getContentValue('description'),
                    'attr' => ['class' => 'tinymce'],
                ]
            )
            ->add('duration', null, ['label' => $templateItem->getContentValue('duration')])
            ->add('completed', null, ['label' => $templateItem->getContentValue('completed')])
            ->add(
                'project', ChoiceType::class, [
                    'label' => $templateItem->getContentValue('project'),
                    'choices' => [
                        'Общее' => 'Общее',
                        'Админка' => 'Админка',
                        'Кабинет врача' => 'Кабинет врача',
                        'Кабинет пациента' => 'Кабинет пациента',
                        'Страница входа' => 'Страница входа',
                    ],
                    'required' => true
                ]
            )
            ->add(
                'blogRecord', EntityType::class, [
                    'label' => $templateItem->getContentValue('blogRecord'),
                    'class' => BlogRecord::class,
                    'choice_label' => function (BlogRecord $blogRecord) {
                        return (new BlogRecordInfoService())->getBlogRecordTitle($blogRecord);
                    },
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => BlogItem::class,])
            ->setDefined(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE)
            ->setAllowedTypes(AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE, [FormTemplateItem::class]);
    }
}
