<?php

namespace App\Controller\Admin;

use App\Entity\AuthUser;
use App\Entity\Staff;
use App\Form\AuthUser\AuthUserEmailType;
use App\Form\AuthUser\AuthUserEnabledType;
use App\Form\AuthUser\AuthUserPasswordType;
use App\Form\AuthUser\AuthUserRequiredType;
use App\Form\Admin\Staff\StaffRoleType;
use App\Form\Admin\StaffType;
use App\Services\ControllerGetters\EntityActions;
use App\Services\Creator\AuthUserCreatorService;
use App\Services\DataTable\Admin\StaffDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\AuthUserInfoService;
use App\Services\MultiFormService\FormData;
use App\Services\MultiFormService\MultiFormService;
use App\Services\TemplateBuilders\Admin\StaffTemplate;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class StaffController
 * @Route("admin/staff")
 * @IsGranted("ROLE_ADMIN")
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
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        parent::__construct($translator);
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
     * @throws Exception
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
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $staff = new Staff();
        $user = new AuthUser();
        $staff->setAuthUser($user);
        $user->setEnabled(true);
        return $this->responseNewMultiForm(
            $request,
            $staff,
            [
                new FormData(AuthUserRequiredType::class, $user),
                new FormData(AuthUserEmailType::class, $user),
                new FormData(AuthUserEnabledType::class, $user),
                new FormData(
                    AuthUserPasswordType::class,
                    $user,
                    [AuthUserPasswordType::IS_PASSWORD_REQUIRED_OPTION_LABEL => true]
                ),
                new FormData(StaffRoleType::class, $user, [], false),
                new FormData(StaffType::class, $staff),
            ],
            function (EntityActions $actions) use ($user, $staff): ?Response {
                try {
                    /** @var AuthUser $role */
                    $role = $actions->getForm()->getData()[MultiFormService::getFormName(StaffRoleType::class)];
                    if (!isset($role->getRoles()[0])) {
                        throw new Exception('Ошибка добавления роли сотруднику!');
                    }
                    $user->setRoles($role->getRoles()[0]);
                    $user->setPhone(AuthUserInfoService::clearUserPhone($user->getPhone()));
                } catch (Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                    return $this->render(
                        $this->templateService->getCommonTemplatePath() . 'new.html.twig', [
                            'staff' => $staff,
                            'form' => $actions->getForm()->createView(),
                        ]
                    );
                }
                $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($encodedPassword);
                $em = $this->getDoctrine()->getManager();
                $em->getConnection()->beginTransaction();
                try {
                    $em->persist($user);
                    $em->flush();
                    $staff->setAuthUser($user);
                    $em->persist($staff);
                    $em->flush();
                    $em->getConnection()->commit();
                } catch (Exception $e) {
                    $em->getConnection()->rollBack();
                    throw $e;
                }
                return null;
            },
            null,
            'newStaff'

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
     * @throws Exception
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
     *
     * @return Response
     * @throws ReflectionException
     * @throws Exception
     */
    public function edit(Request $request, Staff $staff): Response
    {
        $authUser = $staff->getAuthUser();
        $oldPassword = $authUser->getPassword();
        return $this->responseEditMultiForm(
            $request,
            $staff,
            [
                new FormData(AuthUserRequiredType::class, $authUser),
                new FormData(AuthUserEmailType::class, $authUser),
                new FormData(AuthUserEnabledType::class, $authUser),
                new FormData(
                    AuthUserPasswordType::class,
                    $authUser,
                    [AuthUserPasswordType::IS_PASSWORD_REQUIRED_OPTION_LABEL => false]
                ),
                new FormData(StaffRoleType::class, $authUser, []),
                new FormData(StaffType::class, $staff),
            ],
            function () use ($authUser, $oldPassword, $staff) {
                AuthUserCreatorService::updatePassword($this->passwordEncoder, $authUser, $oldPassword);
                $authUser->setPhone(AuthUserInfoService::clearUserPhone($authUser->getPhone()));
            }
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
     * @throws Exception
     */
    public function delete(Request $request, Staff $staff): Response
    {
        return $this->responseDelete($request, $staff);
    }
}
