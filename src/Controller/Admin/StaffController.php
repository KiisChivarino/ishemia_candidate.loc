<?php

namespace App\Controller\Admin;

use App\Entity\AuthUser;
use App\Entity\Staff;
use App\Form\Admin\AuthUser\AuthUserType;
use App\Form\Admin\AuthUser\EditAuthUserType;
use App\Form\Admin\AuthUser\NewAuthUserType;
use App\Form\Admin\Staff\StaffRoleType;
use App\Form\Admin\StaffType;
use App\Services\DataTable\Admin\StaffDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\TemplateBuilders\StaffTemplate;
use App\Services\TemplateItems\FormTemplateItem;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;

/**
 * Class StaffController
 * @Route("/staff")
 *
 * @package App\Controller\Admin
 */
class StaffController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/staff/';

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * CountryController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(Environment $twig, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->templateService = new StaffTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of staffs
     * @Route("/", name="staff_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param StaffDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, StaffDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New staff
     * @Route("/new", name="staff_new", methods={"GET","POST"})
     *
     * @param Request $request
     * @param AuthUserInfoService $authUserInfoService
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, AuthUserInfoService $authUserInfoService): Response
    {
        $template = $this->templateService->new();
        $staff = new Staff();
        $user = new AuthUser();
        $staff->setAuthUser($user);
        $user->setEnabled(true);
        $form = $this->createFormBuilder()
            ->setData(
                [
                    'authUser' => $user,
                    'newAuthUser' => $user,
                    'staff' => $staff
                ]
            )
            ->add(
                'authUser', AuthUserType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'newAuthUser', NewAuthUserType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'onlyRole', StaffRoleType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'staff', StaffType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->getForm();
        try {
            $form->handleRequest($request);
        } catch (Exception $e) {
            $this->addFlash('error', 'Неизвестная ошибка в данных! Проверьте данные или обратитесь к администратору...');
            return $this->render(
                $this->templateService->getCommonTemplatePath().'new.html.twig', [
                    'staff' => $staff,
                    'form' => $form->createView(),
                ]
            );
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            /** @var AuthUser $authUser */
            $authUser = $data['authUser'];
            $staff = $data['staff'];
            try {
                /** @var AuthUser $role */
                $role = $data['onlyRole'];
                if (!isset($role->getRoles()[0])) {
                    throw new Exception('Ошибка добавления роли сотруднику!');
                }
                $authUser->setRoles($role->getRoles()[0]);
                $authUser->setPhone($authUserInfoService->clearUserPhone($authUser->getPhone()));
            } catch (Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->render(
                    $this->templateService->getCommonTemplatePath().'new.html.twig', [
                        'staff' => $staff,
                        'form' => $form->createView(),
                    ]
                );
            }
            $encodedPassword = $this->passwordEncoder->encodePassword($authUser, $authUser->getPassword());
            $authUser->setPassword($encodedPassword);
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try {
                $em->persist($authUser);
                $em->flush();
                $staff->setAuthUser($authUser);
                $em->persist($staff);
                $em->flush();
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }
            $this->addFlash('success', 'post.created_successfully');
            return $this->redirectToRoute($this->templateService->getRoute('list'));
        }
        return $this->render(
            $this->templateService->getCommonTemplatePath().'new.html.twig', [
                'staff' => $staff,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Show staff
     * @Route("/{id}", name="staff_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Staff $staff
     * @param FilterService $filterService
     *
     * @return Response
     */
    public function show(Staff $staff, FilterService $filterService): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $staff, [
                'prescriptionFilterName' => $filterService->generateFilterName('prescription_list', Staff::class),
            ]
        );
    }

    /**
     * Edit staff
     * @Route("/{id}/edit", name="staff_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Staff $staff
     * @param AuthUserInfoService $authUserInfoService
     *
     * @return Response
     */
    public function edit(Request $request, Staff $staff, AuthUserInfoService $authUserInfoService): Response
    {
        $template = $this->templateService->edit();
        $authUser = $staff->getAuthUser();
        $form = $this->createFormBuilder()
            ->setData(
                [
                    'authUser' => $authUser,
                    'editAuthUser' => $authUser,
                    'onlyRole' => $authUser,
                    'staff' => $staff,
                ]
            )
            ->add(
                'authUser', AuthUserType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'editAuthUser', EditAuthUserType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'onlyRole', StaffRoleType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->add(
                'staff', StaffType::class, [
                    'label' => false,
                    self::FORM_TEMPLATE_ITEM_OPTION_TITLE => $template->getItem(FormTemplateItem::TEMPLATE_ITEM_FORM_NAME),
                ]
            )
            ->getForm();
        $oldPassword = $authUser->getPassword();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editPassword($this->passwordEncoder, $authUser, $oldPassword);
            $authUser->setPhone($authUserInfoService->clearUserPhone($authUser->getPhone()));
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute($this->templateService->getRoute('list'));
        }
        return $this->render(
            $this->templateService->getCommonTemplatePath().'edit.html.twig', [
                'entity' => $staff,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Delete staff
     * @Route("/{id}", name="staff_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param Staff $staff
     *
     * @return Response
     */
    public function delete(Request $request, Staff $staff): Response
    {
        return $this->responseDelete($request, $staff);
    }
}
