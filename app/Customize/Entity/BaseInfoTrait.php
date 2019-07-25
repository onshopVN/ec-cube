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
    public function getOwnerStoreAuthToken()
    {
        return '';
    }

    /**
     * Return api endpoint of ownerstore
     *
     * @return string
     */
    public function getOwnerStoreApiEndpoint()
    {
        return 'http://ownerstore.demo';
    }
}
