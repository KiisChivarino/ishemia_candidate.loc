<?php

namespace App\Controller\Admin;

use App\Entity\PatientMedicine;
use App\Form\PatientMedicineType\PatientMedicineType;
use App\Form\PatientMedicineType\PatientMedicineTypeEnabled;
use App\Services\DataTable\Admin\PatientMedicineDataTableService;
use App\Services\MultiFormService\FormData;
use App\Services\TemplateBuilders\Admin\PatientMedicineTemplate;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PatientMedicineController
 * @Route("/admin/patient_medicine")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package App\Controller\Admin
 */
class PatientMedicineController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/patient_medicine/';

    /**
     * PatientMedicineController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new PatientMedicineTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * PatientMedicine list
     * @Route("/", name="patient_medicine_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PatientMedicineDataTableService $dataTableService
     * @return Response
     */
    public function list(
        Request $request,
        PatientMedicineDataTableService $dataTableService
    ): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * Show patient medicine info
     * @Route("/{id}", name="patient_medicine_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param PatientMedicine $patientMedicine
     * @return Response
     * @throws Exception
     */
    public function show(PatientMedicine $patientMedicine): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $patientMedicine);
    }

    /**
     * Edit patient medicine
     * @Route("/{id}/edit", name="patient_medicine_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientMedicine $patientMedicine
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, PatientMedicine $patientMedicine): Response
    {
        return $this->responseEditMultiForm(
            $request,
            $patientMedicine,
            [
                new FormData(PatientMedicineType::class, $patientMedicine),
                new FormData(PatientMedicineTypeEnabled::class, $patientMedicine),
            ]
        );
    }

    /**
     * Delete patient medicine
     * @Route("/{id}", name="patient_medicine_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param PatientMedicine $patientMedicine
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, PatientMedicine $patientMedicine): Response
    {
        return $this->responseDelete($request, $patientMedicine);
    }
}
