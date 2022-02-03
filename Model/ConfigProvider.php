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

use Devx\BackendUiProductExtensibility\Api\ConfigProviderInterface;
use Devx\BackendUiProductExtensibility\Api\Data\ConfigInterface;
use Devx\BackendUiProductExtensibility\Api\Data\ConfigInterfaceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class ConfigProvider implements ConfigProviderInterface
{
    private const XML_PATH_IS_ENABLED = 'catalog/backend_ui_product_extensibility/is_enabled';
    private const XML_PATH_SERVICE_URL = 'catalog/backend_ui_product_extensibility/service_url';
    private const XML_PATH_INSTANCE_TAG = 'catalog/backend_ui_product_extensibility/instance_tag';
    private const XML_PATH_PRODUCT_SECTIONS = 'catalog/backend_ui_product_extensibility/product_sections';

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ConfigInterfaceFactory $configFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigInterfaceFactory $configFactory,
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->configFactory = $configFactory;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): ConfigInterface
    {
        /** @var ConfigInterface $config */
        $config = $this->configFactory->create(
            [
                'isEnabled' => $this->isEnabled(),
                'serviceUrl' => $this->getServiceUrl(),
                'instanceTag' => $this->getInstanceTag(),
                'productSections' => $this->getProductSections(),
            ]
        );
        return $config;
    }

    /**
     * @inheritdoc
     */
    private function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @inheritdoc
     */
    private function getServiceUrl(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_SERVICE_URL, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * @inheritdoc
     */
    private function getInstanceTag(): string
    {
        return trim(
            (string)$this->scopeConfig->getValue(self::XML_PATH_INSTANCE_TAG, ScopeInterface::SCOPE_WEBSITE)
        );
    }

    /**
     * @inheritdoc
     */
    private function getProductSections(): array
    {
        $productSections = (string)$this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_SECTIONS,
            ScopeInterface::SCOPE_WEBSITE
        );

        if (!$productSections) {
            return [];
        }

        try {
            return $this->serializer->unserialize($productSections);
        } catch (\Exception $e) {
            $phrase = __('Error during reading product extensibility config %1.', $productSections);
            $this->logger->error($phrase . ' ' . $e->getMessage());
            throw new LocalizedException($phrase, $e);
        }
    }
}
