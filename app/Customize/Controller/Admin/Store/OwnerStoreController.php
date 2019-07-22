<?php
namespace Customize\Controller\Admin\Store;

use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Eccube\Controller\AbstractController;

/**
 * @Route("/%eccube_admin_route%/store/plugin/api")
 */
class OwnerStoreController extends AbstractController
{
    /**
     * This action is replace default See \Eccube\Controller\Admin\Store\OwnerStoreController::search
     *
     * @Route("/search", name="admin_store_plugin_owners_search")
     * @Route("/search/page/{page_no}", name="admin_store_plugin_owners_search_page", requirements={"page_no" = "\d+"})
     * @Template("@Customize/Admin/Store/OwnerStore/search.twig")
     *
     * @param Request     $request
     * @param int $page_no
     * @param Paginator $paginator
     *
     * @return array
     */
    public function search(Request $request, $page_no = null, Paginator $paginator)
    {
        return [
//            'pagination' => $pagination,
//            'total' => $total,
//            'searchForm' => $searchForm->createView(),
//            'page_no' => $page_no,
//            'Categories' => $category,
        ];
    }
}
