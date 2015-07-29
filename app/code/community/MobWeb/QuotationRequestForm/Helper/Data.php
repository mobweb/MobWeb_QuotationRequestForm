<?php

class MobWeb_QuotationRequestForm_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getUserCompany()
	{
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
		    return '';
		}
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if($defaultBillingAddress = $customer->getDefaultBillingAddress()) {
			return trim($defaultBillingAddress->getCompany());
		}
	}

	public function getCartContentsFormatted()
	{
		if(!$cart = Mage::getModel('checkout/cart')->getQuote()) {
			return '';
		}

		$cartItems = array();

		foreach($cart->getAllVisibleItems() AS $cartItem) {
			$cartItemData = '';

			$product = $cartItem->getProduct();

			// If the product is configurable, grab its options
			$productOptions = '';
			if($product->getData('type_id') === "configurable") {
				$orderOptions = $product->getTypeInstance(true)->getOrderOptions($product);

				if(isset($orderOptions['attributes_info']) && count($orderOptions['attributes_info'])) {
					foreach($orderOptions['attributes_info'] AS $attributeInfo) {
						$productOptions .= sprintf('%s: %s', $attributeInfo['label'],  $attributeInfo['value']);
					}
				}
			}

			// Format the item data as a string
			$cartItemData .= sprintf("%s: %s \n", Mage::helper('catalog')->__('SKU'), $product->getSku());
			$cartItemData .= sprintf("%s: %s \n", Mage::helper('catalog')->__('Name'), $product->getName());
			$cartItemData .= sprintf("%s: %s \n", Mage::helper('catalog')->__('Options'), $productOptions);
			$cartItemData .= sprintf("%s: %s \n", Mage::helper('catalog')->__('Price'), Mage::helper('core')->currency($cartItem->getPriceInclTax(), true, false));
			$cartItemData .= sprintf("%s: %s \n", Mage::helper('quotationrequestform')->__('Price without tax'), Mage::helper('core')->currency($cartItem->getBasePrice(), true, false));
			$cartItemData .= sprintf("%s: %s \n", Mage::helper('catalog')->__('Quantity'), $cartItem->getQty());

			$cartItems[] = $cartItemData;
		}

		// Return the cart item data as a string
		return implode($cartItems, "\n");
	}
}