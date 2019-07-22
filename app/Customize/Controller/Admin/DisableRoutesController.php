<?php
namespace Customize\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Eccube\Controller\AbstractController;

class DisableRoutesController extends AbstractController
{
    /**
     * See \Eccube\Controller\Admin\Store\PluginController::authenticationSetting
     *
     * @Route("/%eccube_admin_route%/store/plugin/authentication_setting", name="admin_store_authentication_setting")
     *
     * @throws NotFoundHttpException
     */
    public function authenticationSetting()
    {
        throw $this->createNotFoundException();
    }
}
