<?php

namespace App\Form\AuthUser;

use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Entity\Role;
use App\Services\TemplateItems\FormTemplateItem;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AuthUserRoleType
 *
 * @package App\Form\Admin\AuthUser
 */
class AuthUserRoleType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AuthUserRoleType constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $roles = $this->entityManager->getRepository(Role::class)->findAll();
        $rolesArray = [];
        /** @var Role $role */
        foreach ($roles as $role) {
            $rolesArray[$role->getName()] = $role->getTechName();
        }
        $builder
            ->add(
                'roles', ChoiceType::class, [
                    'choices' => $rolesArray,
                    'label' => $templateItem->getContentValue('role')
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