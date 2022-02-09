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

namespace Devx\BackendUiProductExtensibility\Ui\DataProvider\Product\Form\Modifier;

use Devx\BackendUiProductExtensibility\Api\ConfigProviderInterface;
use Devx\BackendUiProductExtensibility\Api\FetchProductDataInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form;

/**
 * Adds product data panels
 */
class ProductExtensibility extends AbstractModifier
{
    /**
     * @var ConfigProviderInterface
     */
    private $configProvider;

    /**
     * @var FetchProductDataInterface
     */
    private $fetchProductData;

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ConfigProviderInterface $configProvider
     * @param FetchProductDataInterface $fetchProductData
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ConfigProviderInterface $configProvider,
        FetchProductDataInterface $fetchProductData,
        LocatorInterface $locator,
        ArrayManager $arrayManager
    ) {
        $this->configProvider = $configProvider;
        $this->fetchProductData = $fetchProductData;
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        $config = $this->configProvider->getConfig();
        if (false === $config->isEnabled()) {
            return $meta;
        }

        $productSectionsConfig = $config->getProductSections();
        if (!$productSectionsConfig) {
            return $meta;
        }

        try {
            $productData = $this->fetchProductData->fetchData(
                $config,
                $this->locator->getProduct() ? $this->locator->getProduct()->getSku(): null
            );
        } catch (\Exception $e) {
            return $meta;
        }

        foreach ($productSectionsConfig as $key => $productSectionConfig) {
            if (!isset($productData[$productSectionConfig['key']])) {
                continue;
            }

            $meta = $this->arrayManager->set(
                $productSectionConfig['key'],
                $meta,
                $this->getSectionUiConfig(
                    $productSectionConfig['display_name'],
                    $productData[$productSectionConfig['key']],
                    $key
                )
            );
        }

        return $meta;
    }

    /**
     * @param string $title
     * @param string $content
     * @param int $key
     * @return array
     */
    private function getSectionUiConfig(string $title, string $content, int $key): array
    {
        $sectionConfig = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Form\Fieldset::NAME,
                        'label' => $title,
                        'collapsible' => true,
                        'opened' => false,
                        'sortOrder' => 1000 + ($key * 10),
                    ],
                ],
            ],
            'children' => [
                'content' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/form/components/html',
                                'additionalClasses' => 'admin__fieldset-note',
                                'content' => $content['content'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        return $sectionConfig;
    }
}
