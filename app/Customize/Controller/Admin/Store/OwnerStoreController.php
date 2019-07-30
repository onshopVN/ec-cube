<?php
namespace Customize\Controller\Admin\Store;

use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Eccube\Controller\AbstractController;
use Eccube\Repository\BaseInfoRepository;
use Customize\Service\HttpClient;

class OwnerStoreController extends AbstractController
{
    /**
     * @var BaseInfoRepository
     */
    protected $baseInfo;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * OwnerStoreController constructor.
     * @param HttpClient $httpClient
     * @param BaseInfoRepository $baseInfoRepository
     * @throws \Exception
     */
    public function __construct(
        HttpClient $httpClient,
        BaseInfoRepository $baseInfoRepository
    ) {
        $this->httpClient = $httpClient;
        $this->baseInfo = $baseInfoRepository->get();
    }

    /**
     * This action is replace default See \Eccube\Controller\Admin\Store\OwnerStoreController::search
     *
     * @Route("/%eccube_admin_route%/store/plugin/api/search", name="admin_store_plugin_owners_search")
     * @Route("/%eccube_admin_route%/store/plugin/api/search/page/{page_no}", name="admin_store_plugin_owners_search_page", requirements={"page_no" = "\d+"})
     * @Template("@Customize/Admin/Store/OwnerStore/searchPlugin.twig")
     *
     * @return array
     */
    public function searchPlugin(Request $request, $page_no = null, Paginator $paginator)
    {
        $endpoint = $this->baseInfo->getOwnerStoreApiEndpoint();
        $categoriesResult = $this->httpClient->request($endpoint. '/api/v1/plugins/categories');
        $pluginsResult = $this->httpClient->request($endpoint . '/api/v1/plugins?pageSize=2');

        return [
            'categoriesAsJson' => $categoriesResult,
            'pluginsAsJson' => $pluginsResult
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/store/plugin/api/ajax", name="admin_store_plugin_owners_ajax")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxPlugin(Request $request)
    {
        $endpoint = $this->baseInfo->getOwnerStoreApiEndpoint() . '/api/v1/plugins';
        $queryParams = [];

        $categories = $request->get('categories', '');
        if ($categories) {
            $queryParams['categoryIds'] = implode('|', $categories);
        }

        $keyword = $request->get('keyword', '');
        if ($keyword) {
            $queryParams['keyword'] = $keyword;
        }

        $pageNum = $request->get('pageNum', 0);
        if ($pageNum) {
            $queryParams['pageNum'] = $pageNum;
        }

        $pageSize = $request->get('pageSize', 10);
        if ($pageSize) {
            $queryParams['pageSize'] = $pageSize;
        }

        if ($queryParams) {
            $endpoint .= '?' . http_build_query($queryParams);
        }

        $result = $this->httpClient->request($endpoint);

        return new JsonResponse($result, 200, [], true);
    }

    /**
     *
     * @Route("/%eccube_admin_route%/store/theme/api/search", name="customize_store_theme_owners_search")
     * @Template("@Customize/Admin/Store/OwnerStore/searchTheme.twig")
     *
     * @return array
     */
    public function searchTheme()
    {
        $endpoint = $this->baseInfo->getOwnerStoreApiEndpoint();
        $categoriesResult = $this->httpClient->request($endpoint . '/api/v1/themes/categories');
        $themesResult = $this->httpClient->request($endpoint . '/api/v1/themes?pageSize=2');

        return [
            'categoriesAsJson' => $categoriesResult,
            'themesAsJson' => $themesResult
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/store/theme/api/ajax", name="customize_store_theme_owners_ajax")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxTheme(Request $request)
    {
        $endpoint = $this->baseInfo->getOwnerStoreApiEndpoint() . '/api/v1/themes';
        $queryParams = [];

        $categories = $request->get('categories', '');
        if ($categories) {
            $queryParams['categoryIds'] = implode('|', $categories);
        }

        $keyword = $request->get('keyword', '');
        if ($keyword) {
            $queryParams['keyword'] = $keyword;
        }

        $pageNum = $request->get('pageNum', 0);
        if ($pageNum) {
            $queryParams['pageNum'] = $pageNum;
        }

        $pageSize = $request->get('pageSize', 10);
        if ($pageSize) {
            $queryParams['pageSize'] = $pageSize;
        }

        if ($queryParams) {
            $endpoint .= '?' . http_build_query($queryParams);
        }

        $result = $this->httpClient->request($endpoint);

        return new JsonResponse($result, 200, [], true);
    }
}
