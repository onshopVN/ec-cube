<?php
namespace Customize;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Eccube\Event\TemplateEvent;

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
            ]
        ];
    }

    public function disableDownloadButton(TemplateEvent $templateEvent)
    {
        $templateEvent->addSnippet("@Customize/Admin/Template/disableDownloadSnippet.twig");
    }
}
