<?php

namespace App\Controller\Admin;

use App\Entity\Medicine;
use App\Form\Admin\MedicineType;
use App\Services\DataTable\Admin\MedicineDataTableService;
use App\Services\TemplateBuilders\Admin\MedicineTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class MedicineController
 * @Route("/admin/medicine")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class MedicineController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/medicine/';

    /**
     * CountryController constructor.
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
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($translator);
        $this->templateService = new MedicineTemplate(
            $router->getRouteCollection(),
            get_class($this),
            $authorizationChecker
        );
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of medicines
     * @Route("/", name="medicine_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param MedicineDataTableService $dataTableService
     *
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, MedicineDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New medicine
     * @Route("/new", name="medicine_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new Medicine()), MedicineType::class);
    }

    /**
     * Show medicine info
     * @Route("/{id}", name="medicine_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Medicine $medicine
     *
     * @return Response
     * @throws Exception
     */
    public function show(Medicine $medicine): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $medicine);
    }

    /**
     * Edit medicine
     * @Route("/{id}/edit", name="medicine_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Medicine $medicine
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Medicine $medicine): Response
    {
        return $this->responseEdit($request, $medicine, MedicineType::class);
    }

    /**
     * Delete medicine
     * @Route("/{id}", name="medicine_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param Medicine $medicine
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, Medicine $medicine): Response
    {
        return $this->responseDelete($request, $medicine);
    }
}
