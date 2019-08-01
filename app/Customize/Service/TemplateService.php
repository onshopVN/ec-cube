<?php
namespace Customize\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Eccube\Repository\TemplateRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Template;
use Eccube\Util\StringUtil;

class TemplateService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var TemplateRepository
     */
    protected $templateRepository;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * TemplateService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param DeviceTypeRepository $deviceTypeRepository
     * @param TemplateRepository $templateRepository
     * @param KernelInterface $kernel
     * @param Filesystem $filesystem
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        DeviceTypeRepository $deviceTypeRepository,
        TemplateRepository $templateRepository,
        KernelInterface $kernel,
        Filesystem $filesystem
    ) {
        $this->entityManager = $entityManager;
        $this->deviceTypeRepository = $deviceTypeRepository;
        $this->templateRepository = $templateRepository;
        $this->kernel = $kernel;
        $this->filesystem = $filesystem;
    }

    /**
     * Install a template from path to package
     *
     * @param $templatePath
     * @param $templateName
     * @param $templateCode
     * @return bool
     * @throws \ErrorException
     */
    public function install($templatePath, $templateName, $templateCode)
    {
        $Template = $this->templateRepository->findByCode($templateCode);

        // テンプレートコードの重複チェック.
        if ($Template) {
            throw new \ErrorException(trans('admin.store.template.template_code_already_exists'));
        }

        $Template = new Template();
        $Template->setName($templateName);
        $Template->setCode($templateCode);

        $targetRealDir = $this->kernel->getProjectDir().'/app/template/'.$templateCode;
        $targetHtmlRealDir = $this->kernel->getProjectDir().'/html/template/'.$templateCode;

        // 一時ディレクトリ
        $uniqId = sha1(StringUtil::random(32));
        $tmpDir = \sys_get_temp_dir().'/'.$uniqId;
        $appDir = $tmpDir.'/app';
        $htmlDir = $tmpDir.'/html';

        $file  = new \Symfony\Component\HttpFoundation\File\File($templatePath);

        // ファイル名
        $archive = $templateCode.'.'.$file->getExtension();
        $this->filesystem->copy($templatePath, $tmpDir . DIRECTORY_SEPARATOR . $archive);

        // 一時ディレクトリへ解凍する.
        if ($file->getMimeType() === 'application/zip') {
            $zip = new \ZipArchive();
            $zip->open($tmpDir.'/'.$archive);
            $zip->extractTo($tmpDir);
            $zip->close();
        } elseif (in_array($file->getMimeType(), ['application/x-gzip', 'application/x-tar'])) {
            $phar = new \PharData($tmpDir.'/'.$archive);
            $phar->extractTo($tmpDir, null, true);
        } else {
            throw new \ErrorException("Unsupported file type.");
        }

        // appディレクトリの存在チェック.
        if (!file_exists($appDir)) {
            $this->filesystem->mkdir($appDir);
        }

        // htmlディレクトリの存在チェック.
        if (!file_exists($htmlDir)) {
            $this->filesystem->mkdir($htmlDir);
        }

        // 一時ディレクトリから該当テンプレートのディレクトリへコピーする.
        $this->filesystem->mirror($appDir, $targetRealDir);
        $this->filesystem->mirror($htmlDir, $targetHtmlRealDir);

        // 一時ディレクトリを削除.
        $this->filesystem->remove($tmpDir);

        /** @var DeviceType $DeviceType */
        $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);
        $Template->setDeviceType($DeviceType);

        $this->entityManager->persist($Template);
        $this->entityManager->flush();

        return true;
    }
}
