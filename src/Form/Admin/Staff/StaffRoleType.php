<?php

namespace App\Form\Admin\Staff;

use App\Controller\AppAbstractController;
use App\Entity\AuthUser;
use App\Entity\Role;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateItems\FormTemplateItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AuthUserRoleType
 *
 * @package App\Form\Admin\AuthUser
 */
class StaffRoleType extends AbstractType
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
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FormTemplateItem $templateItem */
        $templateItem = $options[AppAbstractController::FORM_TEMPLATE_ITEM_OPTION_TITLE];
        $roles = $this->entityManager->getRepository(Role::class)->findAll();
        $rolesArray = [];
        /** @var Role $role */
        foreach ($roles as $role) {
            if (strpos($role->getTechName(), 'ROLE_DOCTOR') !== false) {
                $rolesArray[$role->getName()] = $role->getTechName();
            }
        }
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($templateItem, $rolesArray) {
                /** @var AuthUser $authUser */
                $authUser = $event->getData();
                $form = $event->getForm();
                if ($authUser) {
                    $roleName = (new AuthUserInfoService())->getRoleNames($this->entityManager->getRepository(AuthUser::class)->getRoles($authUser), true);
                    $form
                        ->add(
                            'roles', ChoiceType::class, [
                                'choices' => $rolesArray,
                                'label' => $templateItem->getContentValue('role'),
                                'data' => $roleName,
                            ]
                        );
                } else {
                    $form
                        ->add(
                            'roles', ChoiceType::class, [
                                'choices' => $rolesArray,
                                'label' => $templateItem->getContentValue('role'),
                            ]
                        );
                }
            }
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