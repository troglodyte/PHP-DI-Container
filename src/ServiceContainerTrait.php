<?php

namespace DIContainer;

trait ServiceContainerTrait
{
    protected DIContainer $container;

    /*
     * Usage when used in class:
     *    $logger = $this->getContainer()
     *       ->get(MyLoggerWithContext::class);
     *
     * or:
     *    $logger = $this->getContainer(DIContainer::MY_APP_TYPE)
     *       ->get(MyLoggerWithContext::class);
     */
    protected function getContainer(): DIContainer
    {
        if (empty($this->container)) {
            $this->container = new DIContainer(DIContainer::MY_APP_TYPE);
        }
        return $this->container;
    }
}