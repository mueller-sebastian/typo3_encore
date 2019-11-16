<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\Aspect;

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

use Ssch\Typo3Encore\Asset\JsonManifestVersionStrategy;
use Ssch\Typo3Encore\Asset\VersionStrategyInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;

final class ResourceFactorySlot
{
    /**
     * @var VersionStrategyInterface
     */
    private $versionStrategy;

    /**
     * ResourceFactorySlot constructor.
     *
     * @param VersionStrategyInterface $versionStrategy
     */
    public function __construct(VersionStrategyInterface $versionStrategy)
    {
        $this->versionStrategy = $versionStrategy;
    }

    /**
     * @param ResourceFactory $resourceFactory
     * @param string|null $fileIdentifier
     * @param string|null $slotName
     *
     * @return array
     */
    public function jsonManifestVersionStrategy(ResourceFactory $resourceFactory, string $fileIdentifier = null, string $slotName = null): array
    {
        return [$resourceFactory, $this->versionStrategy->getVersion($fileIdentifier)];
    }
}
