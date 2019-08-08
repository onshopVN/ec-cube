<?php
namespace Customize\Entity;

use Eccube\Annotation as Eccube;

/**
 * @Eccube\EntityExtension("Eccube\Entity\BaseInfo")
 */
trait BaseInfoTrait
{
    /**
     * Return authenticate token which access to ownerstore api
     *
     * @return string
     */
    public function getOsStoreAuthToken()
    {
        return env("OS_STORE_AUTH_TOKEN");
    }

    /**
     * Return api endpoint of ownerstore
     *
     * @return string
     */
    public function getOsStoreApiEndpoint()
    {
        return env("OS_STORE_API_ENDPOINT");
    }
}
