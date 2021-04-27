<?php

namespace App\Controller\Admin;

use App\Entity\Complaint;
use App\Form\Admin\ComplaintType;
use App\Services\DataTable\Admin\ComplaintDataTableService;
use App\Services\TemplateBuilders\Admin\ComplaintTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class ComplaintController
 * complaint pages
 * @Route("/admin/complaint")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class ComplaintController extends AdminAbstractController
{
    public const TEMPLATE_PATH = 'admin/complaint/';

    /**
     * ComplaintController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        parent::__construct($translator);
        $this->templateService = new ComplaintTemplate(
            $router->getRouteCollection(),
            get_class($this),
            $authorizationChecker
        );
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Complaint list
     * @Route("/", name="complaint_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param ComplaintDataTableService $dataTableService
     *
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, ComplaintDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New complaint
     * @Route("/new", name="complaint_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new Complaint()), ComplaintType::class);
    }

    /**
     * Show complaint
     * @Route("/{id}", name="complaint_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Complaint $complaint
     *
     * @return Response
     * @throws Exception
     */
    public function show(Complaint $complaint): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $complaint);
    }

    /**
     * Edit complaint
     * @Route("/{id}/edit", name="complaint_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Complaint $complaint
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Complaint $complaint): Response
    {
        return $this->responseEdit($request, $complaint, ComplaintType::class);
    }

    /**
     * Delete complaint
     * @Route("/{id}", name="complaint_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param Complaint $complaint
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, Complaint $complaint): Response
    {
        return $this->responseDelete($request, $complaint);
    }
}
