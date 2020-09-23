<?php

namespace App\Controller\Admin;

use App\Entity\BlogRecord;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 *
 * @package App\Controller\Admin
 */
class AdminController extends AdminAbstractController
{

    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render(
            'admin/index.html.twig', [
                'controller_name' => 'AdminController',
                'blog' => $this->getDoctrine()->getRepository(BlogRecord::class)->getBlog()
            ]
        );
    }
}