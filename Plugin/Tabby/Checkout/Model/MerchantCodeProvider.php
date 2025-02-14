<?php
namespace Tabby\Sub\Plugin\Tabby\Checkout\Model;

use Tabby\Sub\Model\MerchantCodeProcessor;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;

class MerchantCodeProvider
{
    /**
     * @var MerchantCodeProcessor
     */
    private $processor;

    /**
     * Class constructor
     *
     * @param MerchantCodeProcessor $processor
     */
    public function __construct(
        MerchantCodeProcessor $processor
    ) {
        $this->processor = $processor;
    }

    /**
     * Change Merchant code to additional ID for product promotions
     *
     * @param \Tabby\Checkout\Model\MerchantCodeProvider $provider
     * @param string $result
     * @param ProductInterface $product
     * @return string
     */
    public function afterGetMerchantCodeForProduct(
        \Tabby\Checkout\Model\MerchantCodeProvider $provider,
        $result,
        ProductInterface $product
    ) {
        return $this->processor->process($result, [$product->getSku()]);
    }

    /**
     * Change Merchant code to additional ID for cart promotions
     *
     * @param \Tabby\Checkout\Model\MerchantCodeProvider $provider
     * @param string $result
     * @param CartInterface $quote
     * @return string
     */
    public function afterGetMerchantCodeForCart(
        \Tabby\Checkout\Model\MerchantCodeProvider $provider,
        $result,
        CartInterface $quote
    ) {
        return $this->processor->process($result, $this->getSkusFromCart($quote));
    }

    /**
     * Change Merchant code to additional ID for order
     *
     * @param \Tabby\Checkout\Model\MerchantCodeProvider $provider
     * @param string $result
     * @param OrderInterface $order
     * @return string
     */
    public function afterGetMerchantCodeForOrder(
        \Tabby\Checkout\Model\MerchantCodeProvider $provider,
        $result,
        OrderInterface $order
    ) {
        return $this->processor->process($result, $this->getSkusFromOrder($order));
    }

    /**
     * Returns array of order items skus
     *
     * @param OrderInterface $order
     * @return array
     */
    private function getSkusFromOrder(OrderInterface $order)
    {
        $skus = [];
        foreach ($order->getAllVisibleItems() as $item) {
            $skus[] = $item->getSku();
        }
        return $skus;
    }

    /**
     * Returns array of cart items skus
     *
     * @param CartInterface $quote
     * @return array
     */
    private function getSkusFromCart(CartInterface $quote)
    {
        $skus = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $skus[] = $item->getSku();
        }
        return $skus;
    }
}
