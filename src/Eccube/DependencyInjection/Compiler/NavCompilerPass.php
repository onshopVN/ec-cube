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

namespace Eccube\DependencyInjection\Compiler;

use Eccube\Common\EccubeNav;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NavCompilerPass implements CompilerPassInterface
{
    const NAV_TAG = 'eccube.nav';

    public function process(ContainerBuilder $container)
    {
        $ids = $container->findTaggedServiceIds(self::NAV_TAG);
        $nav = $container->getParameter('eccube_nav');

        foreach ($ids as $id => $tags) {
            $def = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($def->getClass());
            if (!is_subclass_of($class, EccubeNav::class)) {
                throw new \InvalidArgumentException(
                    sprintf('Service "%s" must implement interface "%s".', $id, EccubeNav::class));
            }

            /** @var $class EccubeNav */
            $addNav = $class::getNav();
            foreach (array_keys($addNav) as $key) {
                if (!isset($nav[$key])) {
                    $nav[$key] = [];
                }
            }
            $nav = array_replace_recursive($nav, $addNav);
        }

        $nav = $this->arrange($nav);

        $container->setParameter('eccube_nav', $nav);
    }

    /**
     * Remove nav item which disabled = true
     * Sorting nav with 'before' option
     *
     * @param $nav
     * @return array
     */
    protected function arrange($nav)
    {
        $original = $nav;
        foreach ($original as $key => $item) {
            if (isset($item['disabled']) && $item['disabled'] == true) {
                unset($nav[$key]);
            }
            if (!isset($nav[$key])) {
                continue;
            }
            if (isset($item['before']) && isset($nav[$item['before']])) {
                $tmp = [$key => $item];
                $start = array_splice($nav, 0, array_search($item['before'], array_keys($nav)));
                if (isset($start[$key])) {
                    unset($start[$key]);
                }
                $nav = $start + $tmp + $nav;
            }
            if (isset($item['children'])) {
                $nav[$key]['children'] = $this->arrange($item['children']);
            }
        }

        return $nav;
    }
}
