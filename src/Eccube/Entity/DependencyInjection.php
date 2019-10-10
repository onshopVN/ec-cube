<?php
namespace Eccube\Entity;

if (!class_exists("Eccube\Entity\DependencyInjection")) {
    class DependencyInjection
    {
        /**
         * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
         */
        protected $eventDispatcher;

        /**
         * @required
         *
         * Set $eventDispatcher
         *
         * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
         * @return $this
         */
        public function setEventDispatcher(\Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher)
        {
            $this->eventDispatcher = $eventDispatcher;
            return $this;
        }

        /**
         * Get $eventDispatcher
         *
         * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
         */
        public function getEventDispatcher()
        {
            return $this->eventDispatcher;
        }
    }
}
