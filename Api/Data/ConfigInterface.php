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

namespace Devx\BackendUiProductExtensibility\Api\Data;

use Devx\BackendUiProductExtensibility\Api\Data\ConfigExtensionInterface;

/**
 * @api
 */
interface ConfigInterface
{
    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @return string
     */
    public function getServiceUrl(): string;

    /**
     * @return string
     */
    public function getInstanceTag(): string;

    /**
     * @return string
     */
    public function getProductSections(): array;

    /**
     * Get extension attributes object
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Devx\BackendUiProductExtensibility\Api\Data\ConfigExtensionInterface|null
     */
    public function getExtensionAttributes(): ?ConfigExtensionInterface;
}
