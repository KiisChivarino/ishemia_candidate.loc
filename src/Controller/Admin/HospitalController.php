<?php

namespace App\Controller\Admin;

use App\Entity\Hospital;
use App\Form\Admin\Hospital\HospitalType;
use App\Services\DataTable\Admin\HospitalDataTableService;
use App\Services\EntityActions\Creator\HospitalCreatorService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\HospitalTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Services\InfoService\HospitalInfoService;
use App\Services\EntityActions\Core\Builder\CreatorEntityActionsBuilder;

/**
 * Class HospitalController
 * Обработка роутов сущности Hospital
 * @Route("/admin/hospital")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class HospitalController extends AdminAbstractController
{
    //relative path to twig templates
    public const TEMPLATE_PATH = 'admin/hospital/';

    /**
     * HospitalController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     *
     * @throws Exception
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new HospitalTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * Список больниц
     * @Route("/", name="hospital_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param HospitalDataTableService $dataTableService
     *
     * @return Response
     * @throws Exception
     */
    public function list(Request $request, HospitalDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Новая больница
     * @Route("/new", name="hospital_new", methods={"GET","POST"})
     *
     * @param Request $request
     * @param HospitalCreatorService $hospitalCreatorService
     *
     * @return Response
     * @throws Exception
     */
    public function new(
        Request $request,
        HospitalCreatorService $hospitalCreatorService
    ): Response
    {
        return $this->responseNewWithActions(
            $request,
            new CreatorEntityActionsBuilder(
                $hospitalCreatorService
            ),
            new FormData(
                HospitalType::class
            )
        );
    }

    /**
     * Информация о больнице
     * @Route("/{hospital}", name="hospital_show", methods={"GET"}, requirements={"hospital"="\d+"})
     *
     * @param Hospital $hospital
     *
     * @return Response
     * @throws Exception
     */
    public function show(Hospital $hospital): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $hospital);
    }

    /**
     * Редактирование больницы
     * @Route("/{hospital}/edit", name="hospital_edit", methods={"GET","POST"}, requirements={"hospital"="\d+"})
     *
     * @param Request $request
     * @param Hospital $hospital
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Hospital $hospital): Response
    {
        return $this->responseEdit($request, $hospital, HospitalType::class);
    }

    /**
     * Удаление больницы
     * @Route("/{id}", name="hospital_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Hospital $hospital
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, Hospital $hospital): Response
    {
        if (HospitalInfoService::isHospitalDeletable($hospital)) {
            return $this->responseDelete($request, $hospital);
        }

        $this->addFlash('error', $this->translator->trans('hospital_controller.error.delete'));

        return $this->redirectToRoute(
            'hospital_show',
            [
                'hospital' => $hospital->getId()
            ]
        );
    }
}
