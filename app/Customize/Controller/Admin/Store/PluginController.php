<?php
namespace Customize\Controller\Admin\Store;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Eccube\Entity\Plugin;
use Eccube\Exception\PluginApiException;
use Eccube\Exception\PluginException;
use Eccube\Form\Type\Admin\PluginManagementType;

class PluginController extends \Eccube\Controller\Admin\Store\PluginController
{
    /**
     * See \Eccube\Controller\Admin\Store\PluginController::index
     *
     * @Route("/%eccube_admin_route%/store/plugin", name="admin_store_plugin")
     * @Template("@Customize/Admin/Store/Plugin/index.twig")
     *
     * @return array
     *
     * @throws PluginException
     */
    public function index()
    {
        $pluginForms = [];
        $configPages = [];
        $Plugins = $this->pluginRepository->findBy([], ['code' => 'ASC']);

        // ファイル設置プラグインの取得.
        $unregisteredPlugins = $this->getUnregisteredPlugins($Plugins);
        $unregisteredPluginsConfigPages = [];
        foreach ($unregisteredPlugins as $unregisteredPlugin) {
            try {
                $code = $unregisteredPlugin['code'];
                // プラグイン用設定画面があれば表示(プラグイン用のサービスプロバイダーに定義されているか)
                $unregisteredPluginsConfigPages[$code] = $this->generateUrl('plugin_'.$code.'_config');
            } catch (RouteNotFoundException $e) {
                // プラグインで設定画面のルートが定義されていない場合は無視
            }
        }

        $officialPlugins = [];
        $unofficialPlugins = [];

        foreach ($Plugins as $Plugin) {
            $form = $this->formFactory
                ->createNamedBuilder(
                    'form'.$Plugin->getId(),
                    PluginManagementType::class,
                    null,
                    [
                        'plugin_id' => $Plugin->getId(),
                    ]
                )
                ->getForm();
            $pluginForms[$Plugin->getId()] = $form->createView();

            try {
                // プラグイン用設定画面があれば表示(プラグイン用のサービスプロバイダーに定義されているか)
                $configPages[$Plugin->getCode()] = $this->generateUrl(Container::underscore($Plugin->getCode()).'_admin_config');
            } catch (\Exception $e) {
                // プラグインで設定画面のルートが定義されていない場合は無視
            }
            if ($Plugin->getSource() == 0) {
                // 商品IDが設定されていない場合、非公式プラグイン
                $unofficialPlugins[] = $Plugin;
            } else {
                $officialPlugins[$Plugin->getSource()] = $Plugin;
            }
        }

        // オーナーズストア通信
        $officialPluginsDetail = [];
        try {
            $data = $this->pluginApiService->getPurchased();
            foreach ($data as $item) {
                if (isset($officialPlugins[$item['id']]) === false) {
                    $Plugin = new Plugin();
                    $Plugin->setName($item['name']);
                    $Plugin->setCode($item['code']);
                    $Plugin->setVersion($item['version']);
                    $Plugin->setSource($item['id']);
                    $Plugin->setEnabled(false);
                    $officialPlugins[$item['id']] = $Plugin;
                }
                $officialPluginsDetail[$item['id']] = $item;
            }
        } catch (PluginApiException $e) {
//            $this->addWarning($e->getMessage(), 'admin');
        }

        return [
            'plugin_forms' => $pluginForms,
            'officialPlugins' => $officialPlugins,
            'unofficialPlugins' => $unofficialPlugins,
            'configPages' => $configPages,
            'unregisteredPlugins' => $unregisteredPlugins,
            'unregisteredPluginsConfigPages' => $unregisteredPluginsConfigPages,
            'officialPluginsDetail' => $officialPluginsDetail,
        ];
    }
}
