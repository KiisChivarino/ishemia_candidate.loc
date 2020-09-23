<?php

namespace App\Controller\Admin;

use App\Entity\BlogItem;
use App\Form\Admin\BlogItemType;
use App\Services\DataTable\Admin\BlogItemDataTableService;
use App\Services\InfoService\BlogRecordInfoService;
use App\Services\TemplateBuilders\BlogItemTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class BlogItemController
 * @Route("/admin/blog_item")
 * @IsGranted("ROLE_DEVELOPER")
 *
 * @package App\Controller\Admin
 */
class BlogItemController extends AdminAbstractController
{
    //путь к twig шаблонам
    public const TEMPLATE_PATH = 'admin/blog_item/';

    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->templateService = new BlogItemTemplate($router->getRouteCollection(), get_class($this));
        $this->setTemplateTwigGlobal($twig);
    }

    /**
     * List of blog items
     * @Route("/", name="blog_item_list", methods={"GET","POST"})
     *
     * @param Request $request
     * @param BlogItemDataTableService $dataTableService
     *
     * @return Response
     */
    public function list(Request $request, BlogItemDataTableService $dataTableService): Response
    {
        return $this->responseList($request, $dataTableService);
    }

    /**
     * New blog item
     * @Route("/new", name="blog_item_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        return $this->responseNew($request, (new BlogItem())->setCompleted(false), BlogItemType::class);
    }

    /**
     * Show blog item
     * @Route("/{id}", name="blog_item_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param BlogItem $blogItem
     *
     * @return Response
     */
    public function show(BlogItem $blogItem): Response
    {
        return $this->responseShow(
            self::TEMPLATE_PATH, $blogItem, [
                'blogRecordTitle'=> (new BlogRecordInfoService())->getBlogRecordTitle($blogItem->getBlogRecord()),
            ]
        );
    }

    /**
     * Edit blog item
     * @Route("/{id}/edit", name="blog_item_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param BlogItem $blogItem
     *
     * @return Response
     */
    public function edit(Request $request, BlogItem $blogItem): Response
    {
        return $this->responseEdit($request, $blogItem, BlogItemType::class);
    }

    /**
     * Delete blog item
     * @Route("/{id}", name="blog_item_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param BlogItem $blogItem
     *
     * @return Response
     */
    public function delete(Request $request, BlogItem $blogItem): Response
    {
        return $this->responseDelete($request, $blogItem);
    }
}
