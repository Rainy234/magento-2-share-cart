<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ShareCart
 * @copyright   Copyright (c) 2018 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\ShareCart\Block\Cart;

use Magento\Customer\Model\Context;
use \Magento\Quote\Api\CartRepositoryInterface;
class Button extends \Magento\Framework\View\Element\Template
{
    /** @var $cartepository */
    protected $cartRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Button constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CartRepositoryInterface $cartRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CartRepositoryInterface $cartRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $data = [])
    {
        $this->_storeManager = $storeManager;
        $this->_currency = $currency;
        $this->cartRepository = $cartRepository;
        $this->checkoutSession =$checkoutSession;
        $this->_productRepository = $productRepository;
        $this->configurable  =$configurable;
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }
    /**
     * Get store base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * Get current store currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Get default store currency code
     *
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        return $this->_storeManager->getStore()->getDefaultCurrencyCode();
    }

    /**
     * @param bool $skipBaseNotAllowed
     * @return mixed
     */
    public function getAvailableCurrencyCodes($skipBaseNotAllowed = false)
    {
        return $this->_storeManager->getStore()->getAvailableCurrencyCodes($skipBaseNotAllowed);
    }

    /**
     * Get array of installed currencies for the scope
     *
     * @return array
     */
    public function getAllowedCurrencies()
    {
        return $this->_storeManager->getStore()->getAllowedCurrencies();
    }

    /**
     * Get current currency rate
     *
     * @return float
     */
    public function getCurrentCurrencyRate()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyRate();
    }

    /**
     * Get currency symbol for current locale and currency code
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    public function getItems()
    {
        return $this->checkoutSession->getQuote()->getItemsCollection();
    }

    /**
     * @param $item
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getItemName($item)
    {
        if($item->getHasChildren()) {
            $product = $this->_productRepository->get($item->getSku());
            return $product->getName();
        }else{
            return $item->getName();
        }

    }

    /**
     * @param $item
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getParentProductType($item)
    {
        return $this->_productRepository->get($item->getSku())->getTypeId();
    }

    /**
     * @param $item
     * @return array
     */
    public function checkConfigurableProduct($item)
    {
        return $product = $this->configurable->getParentIdsByChild($item->getProductId());
        if(isset($product[0])){
            return $product[0];
        }
    }

    /**
     * @param $item
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getNameConfigurable($item)
    {
        return $this->_productRepository->get($item->getSku())->getName();
    }

    /**
     * @return float
     */
    public function getBaseSubtotal()
    {
        return $this->checkoutSession->getQuote()->getBaseSubtotal();
    }

    /**
     * @return string
     */
    public function getLinkDownload()
    {
        return $this->_urlBuilder->getUrl('sharecart/index/download');
    }
}