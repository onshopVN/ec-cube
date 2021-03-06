<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Repository;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\AbstractEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @return EccubeConfig
     */
    public function getEccubeConfig(): EccubeConfig
    {
        return $this->eccubeConfig;
    }

    /**
     * @param EccubeConfig $eccubeConfig
     */
    public function setEccubeConfig(EccubeConfig $eccubeConfig): void
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * エンティティを削除します。
     * 物理削除ではなく、del_flgを利用した論理削除を行います。
     *
     * @param AbstractEntity $entity
     */
    public function delete($entity)
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * エンティティの登録/保存します。
     *
     * @param $entity|AbstractEntity エンティティ
     */
    public function save($entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    protected function getCacheLifetime()
    {
        return $this->eccubeConfig['eccube_result_cache_lifetime'];
    }
}
