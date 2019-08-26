<?php
namespace Customize\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class OsController extends \Eccube\Controller\AbstractController
{
    /**
     * @var \Eccube\Entity\BaseInfo
     */
    protected $baseInfo;

    /**
     * @var \Customize\Service\HttpClient
     */
    protected $httpClient;

    /**
     * OsController constructor.
     * @param \Eccube\Repository\BaseInfoRepository $baseInfoRepository
     * @param \Customize\Service\HttpClient $httpClient
     * @throws \Exception
     */
    public function __construct(
        \Eccube\Repository\BaseInfoRepository $baseInfoRepository,
        \Customize\Service\HttpClient $httpClient
    ) {
        $this->baseInfo = $baseInfoRepository->get();
        $this->httpClient = $httpClient;
    }

    /**
     *
     * @Route("/%eccube_admin_route%/os/ajax", name="customize_admin_os_ajax")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return JsonResponse
     */
    public function ajax(\Symfony\Component\HttpFoundation\Request $request)
    {
        $endpoint = $this->baseInfo->getOsStoreApiEndpoint() . '/api/v1/me';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '. $this->baseInfo->getOsStoreAuthToken()
        ];

        $result = $this->httpClient->request($endpoint, $headers);
        $result = json_decode($result, true);
        $result['currentVersion'] = env('OS_VERSION') ?: \Eccube\Common\Constant::VERSION;

        return new JsonResponse($result);
    }
}
