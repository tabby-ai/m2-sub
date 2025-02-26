<?php
namespace Tabby\Sub\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class MerchantCodeProcessor
{
    public const MERCHANT_CODE_SUFFIX = '_sub';

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Class constructor
     *
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * Process Merchant code to additional ID for sku list
     *
     * @param string $merchantCode
     * @param array $skuList
     * @param float $price
     * @param ?string $currencyCode
     * @return string
     */
    public function process(
        string $merchantCode,
        array $skuList,
        float $price,
        string $currencyCode = null
    ) {
        if ($currencyCode === null) {
            $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        };

        if ($currencyCode == 'AED'
            && $this->isEnabledForSkus($skuList)
            && $this->isEnabledForPrice($price)
        ) {
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
     * Check for price match some range
     *
     * @param float $price
     * @return bool
     */
    private function isEnabledForPrice($price)
    {
        return ($price >= 2699) && ($price <= 5555);
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
