<?php
namespace Tabby\Sub\Plugin\Tabby\Checkout\Api\Tabby;

class Webhooks
{
    /**
     * @var MerchantCodeProcessor
     */
    private $processor;

    /**
     * Class constructor
     *
     * @param \Tabby\Sub\Model\MerchantCodeProcessor $processor
     */
    public function __construct(
        \Tabby\Sub\Model\MerchantCodeProcessor $processor
    ) {
        $this->processor = $processor;
    }

    /**
     * Change Merchant code to additional ID for product promotions
     *
     * @param \Tabby\Checkout\Model\Api\Tabby\Webhooks $webhooks
     * @param string $result
     * @param int $storeId
     * @param string $merchantCode
     * @param string $url
     * @return bool|void
     */
    public function afterRegisterWebhook(
        \Tabby\Checkout\Model\Api\Tabby\Webhooks $webhooks,
        $result,
        $storeId,
        $merchantCode,
        $url
    ) {
        if ($this->processor->isEnabled($storeId)) {
            $newMerchantCode = $this->processor->addSuffix($merchantCode);
            if ($newMerchantCode != $merchantCode) {
                $this->api->registerWebhook($storeId, $newMerchantCode, $url);
            }
        }
        return $result;
    }
}
