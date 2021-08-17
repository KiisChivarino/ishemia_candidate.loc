<?php

namespace App\Controller\PatientOffice;

use App\Services\TemplateBuilders\PatientOffice\ArticleTemplate;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class ArticleController
 * @IsGranted("ROLE_PATIENT")
 *
 * @package App\Controller\PatientOffice
 * @Route("/patient_office/article")
 */
class ArticleController extends PatientOfficeAbstractController
{
    //relative path to twig templates
    public const TEMPLATE_PATH = 'patientOffice/article/';

    /**
     * @param Environment $twig
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @throws Exception
     */
    public function __construct(Environment $twig, RouterInterface $router, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->templateService = new ArticleTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List article
     * @Route("/", name="patient_office_article")
     * @return Response
     */
    public function list(): Response
    {
        $this->templateService->list();

        return $this->render(self::TEMPLATE_PATH.'list.html.twig', []);
    }

    /**
     * Show article
     * @Route("/{article}/", name="patient_office_article_show", methods={"GET"}, requirements={"article"="\d+"})
     */
    public function show(): Response
    {
        $this->templateService->show();

        return $this->render(self::TEMPLATE_PATH.'show.html.twig', []);
    }

}