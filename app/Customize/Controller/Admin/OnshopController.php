<?php
namespace Customize\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class OnshopController extends \Eccube\Controller\AbstractController
{
    /**
     * @var \Customize\Service\HttpClient
     */
    protected $httpClient;

    /**
     * @var \Eccube\Entity\BaseInfo
     */
    protected $baseInfo;

    /**
     * OnshopController constructor.
     * @param \Customize\Service\HttpClient $httpClient
     * @param \Eccube\Repository\BaseInfoRepository $baseInfoRepository
     * @throws \Exception
     */
    public function __construct(
        \Customize\Service\HttpClient $httpClient,
        \Eccube\Repository\BaseInfoRepository $baseInfoRepository
    ) {
        $this->httpClient = $httpClient;
        $this->baseInfo = $baseInfoRepository->get();
    }

    /**
     * @Route("/%eccube_admin_route%/onshop/update/info", name="admin_onshop_update_info", methods={"POST"})
     */
    public function updateInfo()
    {
        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $request = $this->get('request_stack')->getCurrentRequest();
        $endpoint = $this->baseInfo->getOsStoreApiEndpoint() . '/api/v1/platforms';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '. $this->baseInfo->getOsStoreAuthToken()
        ];
        $queryParams = [];

        $id = $request->get('id', $this->baseInfo->getOsPlatformId());
        $queryParams["id"] = '>' . $id;

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
     * @Route("/%eccube_admin_route%/onshop/update/submit", name="admin_onshop_update_submit", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function updateSubmit()
    {
        ini_set('memory_limit', -1);

        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $request = $this->get('request_stack')->getCurrentRequest();
        $osRegister = $request->attributes->get('osRegister');
        $version = $request->get("version", "");
        $id = $request->get("id", "");
        $result = [];

        $endpoint = $this->baseInfo->getOsStoreApiEndpoint() . '/api/v1/platforms/' . $version . '/update?currentVersion=' . $this->baseInfo->getOsPlatformVersion();
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '. $this->baseInfo->getOsStoreAuthToken()
        ];
        $response = $this->httpClient->request($endpoint, $headers);
        $sh = $this->getParameter("kernel.project_dir") . DIRECTORY_SEPARATOR . 'update.sh';
        if (file_put_contents($sh, $response)) {
            $response = exec("chmod 755 {$sh} && sh {$sh}");
            if ($response === 'success') {
                $result['result'] = true;
                $result['currentVersion'] = $version;
                $result['needUpdate'] = (version_compare($version, $osRegister['latestVersion']) === -1);


                $envParameters = [
                    'OS_PLATFORM_VERSION' => $version,
                    'OS_PLATFORM_ID' => $id
                ];
                $envDir = $this->getParameter('kernel.project_dir');
                $envFile = $envDir.'/.env';
                $envDistFile = $envDir.'/.env.dist';
                $env = file_exists($envFile)
                    ? file_get_contents($envFile)
                    : file_get_contents($envDistFile);

                $env = \Eccube\Util\StringUtil::replaceOrAddEnv($env, $envParameters);
                file_put_contents($envFile, $env);

            } else {
                $result['result'] = false;
                $result['currentVersion'] = $osRegister['currentVersion'];
                $result['needUpdate'] = (version_compare($osRegister['currentVersion'], $osRegister['latestVersion']) === -1);
                $result['message'] = trans("customize.store.message.system_error");
            }
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }

        $result = [
            'result' => false,
            'message' => trans("customize.store.message.system_error")
        ];
        return new JsonResponse($result);
    }
}
