<?php
namespace Customize\Entity;

trait GetSetTrait
{
    /**
     * Magic method which used to dynamic set/get property
     *
     * @param $method
     * @param $arguments
     * @return $this
     * @throws \ErrorException
     */
    public function __call($method, $arguments)
    {
        $prefix = substr($method, 0, 3);
        $property = lcfirst(substr($method, 3));
        switch ($prefix) {
            case 'get':
                if (property_exists($this, $property)) {
                    return $this->$property;
                }
                break;
            case 'set':
                if (property_exists($this, $property)) {
                    $value = isset($arguments[0]) ? $arguments[0] : null;
                    $this->$property = $value;
                    return $this;
                }
                break;
        }

        throw new \ErrorException(sprintf("Invalid method %s::%s", get_class($this), $method));
    }
}
