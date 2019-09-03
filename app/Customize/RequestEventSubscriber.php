<?php
namespace Customize;

use Symfony\Component\HttpKernel\KernelEvents;

class RequestEventSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    /**
     * @var \Eccube\Entity\BaseInfo
     */
    protected $baseInfo;

    /**
     * @var Service\HttpClient
     */
    protected $httpClient;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Eccube\Request\Context
     */
    protected $eccubeContext;

    /**
     * RequestEventSubscriber constructor.
     * @param \Twig_Environment $twig
     * @param \Eccube\Request\Context $eccubeContext
     * @param \Eccube\Repository\BaseInfoRepository $baseInfoRepository
     * @param Service\HttpClient $httpClient
     * @throws \Exception
     */
    public function __construct(
        \Twig_Environment $twig,
        \Eccube\Request\Context $eccubeContext,
        \Eccube\Repository\BaseInfoRepository $baseInfoRepository,
        \Customize\Service\HttpClient $httpClient
    ) {
        $this->twig = $twig;
        $this->eccubeContext = $eccubeContext;
        $this->baseInfo = $baseInfoRepository->get();
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['initOsRegisterData', 100]
        ];
    }

    /**
     * Initialize onshop data
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function initOsRegisterData(\Symfony\Component\HttpKernel\Event\GetResponseEvent $event)
    {
        if (!$this->eccubeContext->isAdmin()) {
            return;
        }

        $endpoint = $this->baseInfo->getOsStoreApiEndpoint() . '/api/v1/me';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '. $this->baseInfo->getOsStoreAuthToken()
        ];

        $result = $this->httpClient->request($endpoint, $headers);
        $result = json_decode($result, true);
        $result['currentVersion'] = $this->baseInfo->getOsPlatformVersion();
        $result['currentVersionId'] = $this->baseInfo->getOsPlatformId();
        $result['needUpdate'] = (version_compare($result['currentVersion'], $result['latestVersion']) < 0);
        $this->twig->addGlobal('osRegister', $result);
        $event->getRequest()->attributes->set('osRegister', $result);
    }
}
