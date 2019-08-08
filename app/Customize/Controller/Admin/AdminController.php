<?php
namespace Customize\Controller\Admin;

use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdminController extends \Eccube\Controller\Admin\AdminController
{
    /**
     * See \Eccube\Controller\Admin\AdminController::index
     *
     * @param Request $request
     *
     * @return array
     *
     * @throws NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Route("/%eccube_admin_route%/", name="admin_homepage")
     * @Template("@Customize/Admin/index.twig")
     */
    public function index(Request $request)
    {
        return parent::index($request);
    }
}
