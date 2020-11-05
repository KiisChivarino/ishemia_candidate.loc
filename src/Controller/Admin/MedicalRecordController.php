<?php

namespace App\Controller\Admin;

use App\Entity\MedicalHistory;
use App\Entity\MedicalRecord;
use App\Form\Admin\MedicalRecordType;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\MedicalRecordDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\MedicalHistoryInfoService;
use App\Services\TemplateBuilders\Admin\MedicalRecordTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class MedicalRecordController
 * @Route("/medical/record")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class MedicalRecordController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/medical_record/';

    /**
     * CountryController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new MedicalRecordTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of medical records
     * @Route("/", name="medical_record_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param MedicalRecordDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, MedicalRecordDataTableService $dataTableService, FilterService $filterService): Response
    {
        return $this->responseList(
            $request, $dataTableService,
            (new FilterLabels($filterService))->setFilterLabelsArray(
                [self::FILTER_LABELS['MEDICAL_HISTORY'],]
            )
        );
    }

    /**
     * Creating new medical record
     * @Route("/new", name="medical_record_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew(
            $request, (new MedicalRecord()), MedicalRecordType::class, null, [],
            function (EntityActions $actions) {
                $actions->getEntity()->setMedicalHistory(
                    $this->getDoctrine()->getManager()->getRepository(MedicalHistory::class)
                        ->find($actions->getRequest()->query->get('medical_history_id'))
                );
            }
        );
    }

    /**
     * Show medical record
     * @Route("/{id}", name="medical_record_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param MedicalRecord $medicalRecord
     *
     * @return Response
     */
    public function show(MedicalRecord $medicalRecord): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $medicalRecord, [
                'medicalHistoryTitle' => (new MedicalHistoryInfoService())
                    ->getMedicalHistoryTitle($medicalRecord->getMedicalHistory()),
            ]
        );
    }

    /**
     * Edit medical record
     * @Route("/{id}/edit", name="medical_record_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param MedicalRecord $medicalRecord
     *
     * @return Response
     */
    public function edit(Request $request, MedicalRecord $medicalRecord): Response
    {
        return $this->responseEdit($request, $medicalRecord, MedicalRecordType::class);
    }

    /**
     * Delete medical record
     * @Route("/{id}", name="medical_record_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param MedicalRecord $medicalRecord
     *
     * @return Response
     */
    public function delete(Request $request, MedicalRecord $medicalRecord): Response
    {
        return $this->responseDelete($request, $medicalRecord);
    }
}
