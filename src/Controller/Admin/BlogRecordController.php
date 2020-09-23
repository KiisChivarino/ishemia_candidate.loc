<?php

namespace App\Controller\Admin;

use App\Entity\BlogRecord;
use App\Form\Admin\BlogRecordType;
use App\Services\ControllerGetters\EntityActions;
use App\Services\DataTable\Admin\BlogRecordDataTableService;
use App\Services\TemplateBuilders\BlogRecordTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class BlogRecordController
 * @Route("/admin/blog_record")
 * @IsGranted("ROLE_DEVELOPER")
 *
 * @package App\Controller\Admin
 */
class BlogRecordController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/blog_record/';

    /**
     * BlogRecordController constructor.
     *
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new BlogRecordTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of blog records
     * @Route("/", name="blog_record_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param BlogRecordDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, BlogRecordDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New blog record
     * @Route("/new", name="blog_record_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew(
            $request, (new BlogRecord()), BlogRecordType::class, null, [],
            function (EntityActions $actions) {
                $this->getDoctrine()->getRepository(BlogRecord::class)->moveOldOutstandingItems($actions->getEntity());
            }
        );
    }

    /**
     * Show blog record info
     * @Route("/{id}", name="blog_record_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param BlogRecord $blogRecord
     *
     * @return Response
     */
    public function show(BlogRecord $blogRecord): Response
    {
        return $this->responseShow(self::TEMPLATE_PATH, $blogRecord);
    }

    /**
     * Edit blog record
     * @Route("/{id}/edit", name="blog_record_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param BlogRecord $blogRecord
     *
     * @return Response
     */
    public function edit(Request $request, BlogRecord $blogRecord): Response
    {
        return $this->responseEdit($request, $blogRecord, BlogRecordType::class);
    }

    /**
     * Delete blog record
     * @Route("/{id}", name="blog_record_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param BlogRecord $blogRecord
     *
     * @return Response
     */
    public function delete(Request $request, BlogRecord $blogRecord): Response
    {
        return $this->responseDelete($request, $blogRecord);
    }
}
