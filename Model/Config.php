<?php
/**
 * Copyright 2021 Adobe. All rights reserved.
 * This file is licensed to you under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License. You may obtain a copy
 * of the License at http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under
 * the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR REPRESENTATIONS
 * OF ANY KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */
declare(strict_types=1);

namespace Devx\BackendUiProductExtensibility\Model;

use Devx\BackendUiProductExtensibility\Api\Data\ConfigExtensionInterface;
use Devx\BackendUiProductExtensibility\Api\Data\ConfigInterface;

/**
 * @inheritdoc
 */
class Config implements ConfigInterface
{
    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var string
     */
    private $serviceUrl;

    /**
     * @var string
     */
    private $instanceTag;

    /**
     * @var array
     */
    private $productSections;

    /**
     * @var ConfigExtensionInterface
     */
    private $extensionAttributes;

    /**
     * @param bool $isEnabled
     * @param string $serviceUrl
     * @param string $instanceTag
     * @param array $productSections
     * @param ConfigExtensionInterface $extensionAttributes
     */
    public function __construct(
        bool $isEnabled,
        string $serviceUrl,
        string $instanceTag,
        array $productSections,
        ConfigExtensionInterface $extensionAttributes
    ) {
        $this->isEnabled = $isEnabled;
        $this->serviceUrl = $serviceUrl;
        $this->instanceTag = $instanceTag;
        $this->productSections = $productSections;
        $this->extensionAttributes = $extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @inheritdoc
     */
    public function getServiceUrl(): string
    {
        return $this->serviceUrl;
    }

    /**
     * @inheritdoc
     */
    public function getInstanceTag(): string
    {
        return $this->instanceTag;
    }

    /**
     * @inheritdoc
     */
    public function getProductSections(): array
    {
        return $this->productSections;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ConfigExtensionInterface
    {
        return $this->extensionAttributes;
    }
}
