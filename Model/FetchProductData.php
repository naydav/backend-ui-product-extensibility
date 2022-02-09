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

use Devx\BackendUiProductExtensibility\Api\Data\ConfigInterface;
use Devx\BackendUiProductExtensibility\Api\FetchProductDataInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class FetchProductData implements FetchProductDataInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ClientInterface $httpClient
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        ClientInterface $httpClient,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function fetchData(ConfigInterface $config, string $productSku = null): array
    {
        $serviceUrl = sprintf(
            '%s?instance=%s&product=%s',
            $config->getServiceUrl(),
            urlencode($config->getInstanceTag()),
            $productSku
        );

        try {
            $this->httpClient->get($serviceUrl);
        } catch (\Exception $e) {
            $phrase = __('Error during fetching data from %1.', $serviceUrl);
            $this->logger->error($phrase . ' ' . $e->getMessage());
            throw new LocalizedException($phrase, $e);
        }

        $status = $this->httpClient->getStatus();
        if ($status !== 200) {
            $phrase = __('Error during fetching data from %1. Status code: %2.', $serviceUrl, $status);
            $this->logger->error($phrase);
            throw new LocalizedException($phrase);
        }

        $body = $this->httpClient->getBody();
        try {
            $result = $this->serializer->unserialize($body);
        } catch (\Exception $e) {
            $phrase = __('Error during fetching data from %1.', $serviceUrl);
            $this->logger->error($phrase . ' ' . $e->getMessage());
            throw new LocalizedException($phrase, $e);
        }
        return $result;
    }
}
