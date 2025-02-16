<?php
namespace Tabby\Sub\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class MerchantCodeProcessor
{
    public const MERCHANT_CODE_SUFFIX = '_sub';

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * Class constructor
     *
     * @param ScopeConfigInterface $config
     */
    public function __construct(
        ScopeConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * Process Merchant code to additional ID for sku list
     *
     * @param string $merchantCode
     * @param array $skuList
     * @return string
     */
    public function process(
        string $merchantCode,
        array $skuList
    ) {
        if ($this->isEnabledForSkus($skuList)) {
            $merchantCode .= self::MERCHANT_CODE_SUFFIX;
        }
        return $merchantCode;
    }

    /**
     * Add suffix to merchant code if not present
     *
     * @param string $merchantCode
     * @return string
     */
    public function addSuffix(
        string $merchantCode
    ) {
        if (strpos($merchantCode, self::MERCHANT_CODE_SUFFIX) === false) {
            $merchantCode .= self::MERCHANT_CODE_SUFFIX;
        }
        return $merchantCode;
    }

    /**
     * Check for feature is enabled and skus is configured
     *
     * @param ?int $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        // if module not enabled
        if (!$this->config->getValue(
            "tabby/tabby_sub/enabled",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        )) {
            return false;
        }

        // if config list is empty
        if (empty($this->getSkus())) {
            return false;
        }

        return true;
    }
    /**
     * Check for all items from list provided present in configured list
     *
     * @param array $list
     * @return bool
     */
    private function isEnabledForSkus($list)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        foreach ($list as $productSku) {
            foreach ($this->getSkus() as $sku) {
                if ($sku == $productSku) {
                    continue 2;
                }
            }
            // if sku not found in config list
            return false;
        }
        // all skus found in list
        return true;
    }

    /**
     * Returns configured list
     *
     * @param ?int $storeId
     * @return bool
     */
    private function getSkus($storeId = null)
    {
        return array_filter(
            array_map(
                'trim',
                explode(
                    "\n",
                    $this->config->getValue(
                        "tabby/tabby_sub/skus",
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $storeId
                    ) ?: ''
                )
            )
        );
    }
}
