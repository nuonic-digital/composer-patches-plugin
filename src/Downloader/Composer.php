<?php
namespace Nuonic\ComposerPatchesPlugin\Downloader;

/*                                                                        *
 * This script belongs to the Composer-TYPO3-Installer package            *
 * (c) 2014 Netresearch GmbH & Co. KG                                     *
 * This copyright notice MUST APPEAR in all copies of the script!         *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Composer\Json\JsonFile;
use Composer\Util\HttpDownloader;
use Composer\Util\RemoteFilesystem;
use Netresearch\Composer\Patches\Downloader\stdClass;

/**
 * Downloader, which uses the composer RemoteFilesystem
 */
class Composer implements DownloaderInterface
{
    /**
     * @var RemoteFilesystem
     */
    protected $remoteFileSystem;

    /**
     * @var HttpDownloader
     */
    private $httpDownloader;

    /**
     * Construct the RFS
     *
     * @param \Composer\IO\IOInterface $io
     */
    public function __construct(\Composer\IO\IOInterface $io, \Composer\Config $config)
    {
        $this->remoteFileSystem = new RemoteFilesystem($io, $config);
        $this->httpDownloader = new HttpDownloader($io, $config);
    }

    /**
     * Get the origin URL required by composer rfs
     *
     * @param  string $url
     * @return string
     */
    protected function getOriginUrl($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }

    /**
     * Download the file and return its contents
     *
     * @param  string $url The URL from where to download
     * @return string Contents of the URL
     */
    public function getContents($url)
    {
        $originUrl = $this->getOriginUrl($url);

        if (is_null($originUrl)) {
            var_dump(getcwd());
            return file_get_contents($url);
        }

        return $this->remoteFileSystem->getContents($originUrl, $url, false);
    }

    /**
     * Download file and decode the JSON string to PHP object
     *
     * @param  string $json
     * @return stdClass
     */
    public function getJson($url)
    {
        return (new JsonFile($url, $this->httpDownloader))->read();
    }
}
