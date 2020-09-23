<?php

namespace App\Controller\Admin;

use App\Entity\Hospital;
use App\Form\Admin\Hospital\HospitalType;
use App\Services\DataTable\Admin\HospitalDataTableService;
use App\Services\TemplateBuilders\HospitalTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class HospitalController
 * Обработка роутов сущности Hospital
 * @Route("/admin/hospital")
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
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
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
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new Hospital()), HospitalType::class);
    }

    /**
     * Ищет аяксом больницы
     * @Route("/find_hospital_ajax", name="find_hospital_ajax", methods={"GET"})
     *
     * @param Request $request
     *
     * @return false|string
     */
    public function findHospitalAjax(Request $request)
    {
        $city = $request->get('city');
        $string = $request->query->get('q');
        $entityManager = $this->getDoctrine()->getManager();
        $hospitals = $entityManager->getRepository(Hospital::class)->findHospitals($string, $city);
        $resultArray = [];
        /** @var Hospital $hospital */
        foreach ($hospitals as $hospital) {
            $resultArray[] = [
                'id' => $hospital->getId(),
                'text' => $hospital->getName()
            ];
        }
        return new Response(
            json_encode(
                $resultArray
            )
        );
    }

    /**
     * Информация о больнице
     * @Route("/{id}", name="hospital_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Hospital $hospital
     *
     * @return Response
     */
    public function show(Hospital $hospital): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $hospital);
    }

    /**
     * Редактирование больницы
     * @Route("/{id}/edit", name="hospital_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Hospital $hospital
     *
     * @return Response
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
     */
    public function delete(Request $request, Hospital $hospital): Response
    {
        return $this->responseDelete($request, $hospital);
    }
}
