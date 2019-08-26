<?php
namespace Customize\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;

class HttpClient
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * HttpClient constructor.
     * @param KernelInterface $kernel
     * @param Filesystem $filesystem
     */
    public function __construct(
        KernelInterface $kernel,
        Filesystem $filesystem
    ) {
        $this->kernel = $kernel;
        $this->filesystem = $filesystem;
    }

    /**
     * Request to url return content of request
     *
     * @param $url
     * @param $header
     * @return bool|string
     */
    public function request($url, $header = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Request to url by POST method
     *
     * @param $url
     * @param array $header
     * @param array $data
     * @return bool|string
     */
    public function post($url, $header = [], $data = [])
    {
        if (in_array('Content-Type: application/json', $header)) {
            $data = json_encode($data);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Request to url by PUT method
     *
     * @param $url
     * @param array $header
     * @param array $data
     * @return bool|string
     */
    public function put($url, $header = [], $data = [])
    {
        if (in_array('Content-Type: application/json', $header)) {
            $data = json_encode($data);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Download a resource to $localPath
     *
     * @param $url
     * @param $header
     * @param null $localPath
     * @return string
     * @throws \ErrorException
     */
    public function download($url, $header = [], $localPath = null)
    {
        if (is_null($localPath)) {
            $localPath = $this->kernel->getProjectDir() . str_replace("/", DIRECTORY_SEPARATOR, "/var/customize/packages");
        }
        if (!is_dir($localPath)) {
            $this->filesystem->mkdir($localPath);
        }

        if (!is_dir($localPath)) {
            throw new \ErrorException("{$localPath} is not exists");
        }

        $urlParts = explode("/", $url);
        $filepath = $localPath . DIRECTORY_SEPARATOR . end($urlParts);
        if (!($fp = fopen ($filepath, "w+")))  {
            throw new \ErrorException("{$filepath} is not writable");
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \ErrorException(curl_error($ch));
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return $statusCode == 200 ? $filepath : "";
    }
}
