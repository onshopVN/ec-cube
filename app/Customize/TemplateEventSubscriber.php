<?php
namespace Customize;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Eccube\Event\TemplateEvent;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Entity\BaseInfo;
use Customize\Service\HttpClient;

class TemplateEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var BaseInfo
     */
    protected $baseInfo;

    /**
     * TemplateEventSubscriber constructor.
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
     * {@inheritDoc}
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            "@admin/Store/template.twig" => [
                ["disableDownloadButton", 100]
            ],
            "@Customize/Admin/index.twig" => [
                ["prepareNotificationData", 100]
            ]
        ];
    }

    /**
     * Disabled download button on store template
     *
     * @param TemplateEvent $templateEvent
     */
    public function disableDownloadButton(TemplateEvent $templateEvent)
    {
        $templateEvent->addSnippet("@Customize/Admin/Template/disableDownloadSnippet.twig");
    }

    /**
     * Prepare first data for notification block
     *
     * @param TemplateEvent $templateEvent
     */
    public function prepareNotificationData(TemplateEvent $templateEvent)
    {
        $endpoint = $this->baseInfo->getOsStoreApiEndpoint();
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '. $this->baseInfo->getOsStoreAuthToken()
        ];
        $osInfoJson = $this->httpClient->request($endpoint. '/api/v1/me', $headers);
        $osInfo = json_decode($osInfoJson, true);
        $osInfo['currentVersion'] = env('OS_VERSION') ?: \Eccube\Common\Constant::VERSION;
        if (isset($osInfo['id'])) {
            $notificationsJson = $this->httpClient->request($endpoint. '/api/v1/notifications?pageSize=5&targetEntity=frontend_register&targetId='.$osInfo['id'], $headers);
        } else {
            $notificationsJson = '{"totals":"0","pageSize":"10","pageNum":"1","items":[]}';
        }
        $osInfoJson = json_encode($osInfo);

        $templateEvent->setParameter('osInfoJson', $osInfoJson);
        $templateEvent->setParameter('notificationsJson', $notificationsJson);
    }
}
