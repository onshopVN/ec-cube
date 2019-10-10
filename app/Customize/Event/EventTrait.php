<?php
namespace Customize\Event;

trait EventTrait
{
    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * Set $eventManager
     *
     * @required
     * @param EventManager $eventManager
     */
    public function setEventManager(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }
}
