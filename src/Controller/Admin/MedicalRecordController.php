<?php

namespace App\Controller\Admin;

use App\Entity\MedicalRecord;
use App\Form\Admin\MedicalRecordType;
use App\Repository\MedicalHistoryRepository;
use App\Services\ControllerGetters\EntityActions;
use App\Services\ControllerGetters\FilterLabels;
use App\Services\DataTable\Admin\MedicalRecordDataTableService;
use App\Services\FilterService\FilterService;
use App\Services\InfoService\MedicalHistoryInfoService;
use App\Services\TemplateBuilders\Admin\MedicalRecordTemplate;
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
 * Class MedicalRecordController
 * @Route("/admin/medical_record")
 * @IsGranted("ROLE_MANAGER")
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
        $this->templateService = new MedicalRecordTemplate(
            $router->getRouteCollection(),
            get_class($this),
            $authorizationChecker
        );
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of medical records
     * @Route("/", name="medical_record_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param MedicalRecordDataTableService $dataTableService
     *
     * @param FilterService $filterService
     * @return Response
     * @throws Exception
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
     * @param MedicalHistoryRepository $medicalHistoryRepository
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, MedicalHistoryRepository $medicalHistoryRepository): Response
    {
        return $this->responseNew(
            $request, (new MedicalRecord()), MedicalRecordType::class, null, [],
            function (EntityActions $actions) use ($medicalHistoryRepository) {
                $actions->getEntity()->setMedicalHistory(
                    $medicalHistoryRepository
                        ->find(
                            $actions->getRequest()->query->get(
                                MedicalHistoryController::MEDICAL_HISTORY_ID_PARAMETER_KEY
                            )
                        )
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
     * @throws Exception
     */
    public function show(MedicalRecord $medicalRecord): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $medicalRecord, [
                'medicalHistoryTitle' =>
                    MedicalHistoryInfoService::getMedicalHistoryTitle($medicalRecord->getMedicalHistory()),
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
     * @throws Exception
     */
    public function edit(Request $request, MedicalRecord $medicalRecord): Response
    {
        return $this->responseEdit($request, $medicalRecord, MedicalRecordType::class);
    }

    /**
     * Delete medical record
     * @Route("/{id}", name="medical_record_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param MedicalRecord $medicalRecord
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, MedicalRecord $medicalRecord): Response
    {
        return $this->responseDelete($request, $medicalRecord);
    }
}
