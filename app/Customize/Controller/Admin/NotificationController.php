<?php
namespace Customize\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Customize\Service\HttpClient;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Entity\BaseInfo;

class NotificationController extends \Eccube\Controller\AbstractController
{
    /**
     * @var BaseInfo
     */
    protected $baseInfo;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * NotificationController constructor.
     * @param BaseInfoRepository $baseInfoRepository
     * @param HttpClient $httpClient
     * @throws \Exception
     */
    public function __construct(
        BaseInfoRepository $baseInfoRepository,
        HttpClient $httpClient
    ) {
        $this->baseInfo = $baseInfoRepository->get();
        $this->httpClient = $httpClient;
    }

    /**
     *
     * @Route("/%eccube_admin_route%/notification/ajax", name="customize_admin_notification_ajax")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajax(Request $request)
    {
        $endpoint = $this->baseInfo->getOsStoreApiEndpoint() . '/api/v1/notifications';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '. $this->baseInfo->getOsStoreAuthToken()
        ];

        $queryParams = [];
        $targetId = $request->get('targetId', 0);
        if ($targetId) {
            $queryParams['targetId'] = $targetId;
        }
        $targetEntity = $request->get('targetEntity', 'frontend_register');
        if ($targetEntity) {
            $queryParams['targetEntity'] = $targetEntity;
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

        $result = $this->httpClient->request($endpoint, $headers);

        return new JsonResponse($result, 200, [], true);
    }

    /**
     * @Route("/%eccube_admin_route%/notification/ajax/read", name="customize_admin_notification_ajax_read", methods={"PUT"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxRead(Request $request)
    {
        $endpoint = $this->baseInfo->getOsStoreApiEndpoint() . '/api/v1/notifications/' . $request->get('id');
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '. $this->baseInfo->getOsStoreAuthToken()
        ];
        $data = [
            'readAt' => 'now'
        ];
        $result = $this->httpClient->put($endpoint, $headers, $data);

        return new JsonResponse($result, 200, []);
    }
}