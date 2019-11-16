<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\Asset;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use RuntimeException;
use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\JsonDecoderInterface;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

final class JsonManifestVersionStrategy implements VersionStrategyInterface
{
    /**
     * @var string
     */
    private $manifestPath;

    /**
     * @var array
     */
    private $manifestData;

    /**
     * @var JsonDecoderInterface
     */
    private $jsonDecoder;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    public function __construct(SettingsServiceInterface $settingsService, FilesystemInterface $filesystem, JsonDecoderInterface $jsonDecoder)
    {
        $this->manifestPath = $filesystem->getFileAbsFileName($settingsService->getByPath('manifestJsonPath'));
        $this->jsonDecoder = $jsonDecoder;
        $this->filesystem = $filesystem;
    }

    /**
     * With a manifest, we don't really know or care about what
     * the version is. Instead, this returns the path to the
     * versioned file.
     *
     * @param string $path
     *
     * @return string|null
     */
    public function getVersion($path)
    {
        return $this->applyVersion($path);
    }

    public function applyVersion($path)
    {
        return $this->getManifestPath($path) ?: $path;
    }

    private function getManifestPath($path)
    {
        if (null === $this->manifestData) {
            if (!$this->filesystem->exists($this->manifestPath)) {
                throw new RuntimeException(sprintf('Asset manifest file "%s" does not exist.', $this->manifestPath));
            }

            $this->manifestData = $this->jsonDecoder->decode($this->filesystem->get($this->manifestPath));
        }

        // Resolve also path identifiers beginning with EXT:
        $path = $this->filesystem->getFileAbsFileName($path);

        if (StringUtility::beginsWith($path, Environment::getPublicPath())) {
            $path = PathUtility::stripPathSitePrefix($path);
        }

        return $this->manifestData[$path] ?? null;
    }
}
