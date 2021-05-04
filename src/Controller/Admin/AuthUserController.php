<?php

namespace App\Controller\Admin;

use App\Entity\AuthUser;
use App\Form\AuthUser\AuthUserEmailType;
use App\Form\AuthUser\AuthUserEnabledType;
use App\Form\AuthUser\AuthUserRequiredType;
use App\Form\AuthUser\AuthUserRoleType;
use App\Form\AuthUser\AuthUserPasswordType;
use App\Repository\UserRepository;
use App\Services\Creator\AuthUserCreatorService;
use App\Services\DataTable\Admin\AuthUserDataTableService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\AuthUserTemplate;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class AuthUserController
 * @Route("/admin/auth_user")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller
 */
class AuthUserController extends AdminAbstractController
{
    /** @var string путь шаблонам контроллера */
    public const TEMPLATE_PATH = 'admin/auth_user/';

    /**
     * AuthUserController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator
    )
    {
        parent::__construct($translator);
        $this->templateService = new AuthUserTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список пользователей
     * @Route("/", name="auth_user_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param AuthUserDataTableService $dataTableService
     *
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, AuthUserDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Show user info
     * @Route("/{id}", name="auth_user_show", methods={"GET"}, requirements={"id"="\d+"})
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
            self::TEMPLATE_PATH,
            $authUser,
            [
                'roles' => $userRepository->getRoles($authUser)
            ]
        );
    }

    /**
     * Edit user
     * @Route("/{id}/edit", name="auth_user_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
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
                new FormData(AuthUserRequiredType::class, $authUser),
                new FormData(AuthUserEmailType::class, $authUser),
                new FormData(AuthUserEnabledType::class, $authUser),
                new FormData(
                    AuthUserPasswordType::class,
                    $authUser,
                    [AuthUserPasswordType::IS_PASSWORD_REQUIRED_OPTION_LABEL => false]
                ),
                new FormData(AuthUserRoleType::class, $authUser, [], false),
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
}
