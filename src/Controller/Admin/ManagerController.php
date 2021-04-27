<?php


namespace App\Controller\Admin;


use App\Entity\AuthUser;
use App\Form\Admin\AuthUser\AuthUserOptionalType;
use App\Form\Admin\AuthUser\AuthUserPasswordType;
use App\Form\Admin\AuthUser\AuthUserRequiredType;
use App\Repository\UserRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\Creator\AuthUserCreatorService;
use App\Services\DataTable\Admin\AdminManagerDataTableService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\AdminManagerTemplateBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Exception;
use ReflectionException;

/**
 * Class AuthUserController
 * @Route("/admin/admin_manager")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller
 */

class ManagerController extends AdminAbstractController
{
    /** @var string путь шаблонам контроллера */
    public const TEMPLATE_PATH = '/admin/admin_manager/';

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * admin_manager constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TranslatorInterface $translator
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($translator);
        $this->passwordEncoder = $passwordEncoder;
        $this->templateService = new AdminManagerTemplateBuilder(
            $router->getRouteCollection(),
            get_class($this),
            $authorizationChecker
        );
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список менеджеров панели администратора
     * @Route("/", name="admin_manager_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param AdminManagerDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, AdminManagerDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Edit manager
     * @Route("/{id}/edit", name="admin_manager_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param AuthUser $authUser
     * @param AuthUserCreatorService $authUserCreatorService
     * @return Response
     * @throws ReflectionException
     */
    public function edit(
        Request $request,
        AuthUser $authUser,
        AuthUserCreatorService $authUserCreatorService
    ): Response
    {
        $oldPassword = $authUser->getPassword();
        return $this->responseEditMultiForm(
            $request,
            $authUser,
            [
                new FormData($authUser, AuthUserRequiredType::class),
                new FormData($authUser, AuthUserOptionalType::class),
                new FormData(
                    $authUser,
                    AuthUserPasswordType::class,
                    [AuthUserPasswordType::IS_PASSWORD_REQUIRED_OPTION_LABEL => false]
                )
            ],
            function ()
            use ($oldPassword, $authUser, $authUserCreatorService) {
                try {
                    $authUserCreatorService->updateAuthUser($authUser, $oldPassword);
                } catch (Exception $e) {
                    $this->addFlash(
                        'error',
                        'Пользователь не добавлен!'
                    );
                }
            }
        );
    }

    /**
     * Show user info
     * @Route("/{id}", name="admin_manager_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param AuthUser $authUser
     *
     * @param UserRepository $userRepository
     * @return Response
     * @throws Exception
     */
    public function show(AuthUser $authUser, UserRepository $userRepository): Response
    {
        return $this->responseShow(
            AuthUserController::TEMPLATE_PATH,
            $authUser,
            [
                'roles' => $userRepository->getRoles($authUser)
            ]
        );
    }


    /**
     * Новый менеджер панели администратора
     * @Route("/new", name="admin_manager_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @param AuthUserCreatorService $authUserCreatorService
     * @return Response
     * @throws Exception
     */
    public function new(
        Request $request,
        AuthUserCreatorService $authUserCreatorService
    ): Response
    {
        $authUser = $authUserCreatorService->createAuthUser();
        return $this->responseNewMultiForm(
            $request,
            $authUser,
            [
                new FormData($authUser, AuthUserRequiredType::class),
                new FormData($authUser, AuthUserOptionalType::class),
                new FormData(
                    $authUser,
                    AuthUserPasswordType::class,
                    [AuthUserPasswordType::IS_PASSWORD_REQUIRED_OPTION_LABEL => false]
                ),
            ],
            function (EntityActions $actions)
            use (
                $authUser,
                $authUserCreatorService
            ) {
                $em = $actions->getEntityManager();
                $em->getConnection()->beginTransaction();
                try {
                    $authUserCreatorService->persistNewManagerAuthUser($authUser);
                    $em->flush();
                    $em->getConnection()->commit();
                } catch (Exception $e) {
                    $em->getConnection()->rollBack();
                    throw $e;
                }
            }
        );
    }

    /**
     * Delete user
     * @Route("/{id}", name="admin_manager_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param AuthUser $authUser
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, AuthUser $authUser): Response
    {
        return $this->responseDelete($request, $authUser);
    }


}