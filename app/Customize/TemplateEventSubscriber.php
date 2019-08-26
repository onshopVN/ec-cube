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
        $templateEvent->addAsset('<link rel="stylesheet" href="{{ asset("assets/css/notification.css", "customize") }}" />', false);
    }
}
