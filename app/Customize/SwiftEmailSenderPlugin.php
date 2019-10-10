<?php
namespace Customize;

class SwiftEmailSenderPlugin implements \Swift_Events_SendListener
{
    /**
     * @param \Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();
        $sender = $this->getEmailSender();
        $from = $message->getFrom();
        if (count($from) === 1) {
            $newFrom = [$sender => current($from)];
        } else {
            $newFrom = [$sender => null];
        }
        if (!$message->getReplyTo()) {
            $message->setReplyTo($newFrom);
        }

        $message->setSender(key($newFrom), current($newFrom));
        $message->setReturnPath($sender);
        $message->setFrom($newFrom);
    }

    /**
     * @param \Swift_Events_SendEvent $evt
     */
    public function sendPerformed(\Swift_Events_SendEvent $evt)
    {
    }

    /**
     * Ger config email which used to send mail
     *
     * @return string
     */
    protected function getEmailSender()
    {
        return getenv('OS_EMAIL_SENDER') ?: 'info@onshop.vn';
    }
}