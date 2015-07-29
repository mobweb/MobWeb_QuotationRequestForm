<?php
class MobWeb_QuotationRequestForm_IndexController extends Mage_Core_Controller_Front_Action
{
	const XML_PATH_EMAIL_RECIPIENT = 'quotationrequestform/email/recipient_email';
	const XML_PATH_EMAIL_SENDER = 'quotationrequestform/email/sender_email_identity';

	const EMAIL_TEMPLATE_ID = 'quotation_request';

	public function indexAction()
	{
		$this->loadLayout();
		$this->getLayout()->getBlock('form')
		    ->setFormAction( Mage::getUrl('*/index/post') );

		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
		$this->renderLayout();
	}

	public function postAction()
	{
		$post = $this->getRequest()->getPost();
		if ( $post ) {
			$translate = Mage::getSingleton('core/translate');
			/* @var $translate Mage_Core_Model_Translate */
			$translate->setTranslateInline(false);
			try {

				// Append the customer ID to the post data
				$customer = Mage::getSingleton('customer/session')->getCustomer();
				$post['customer_id'] = $customer ? $customer->getId() : '-';
				
				// nl2br the comment
				$post['comment'] = nl2br($post['comment']);

				// Append the contents of the cart to the post data
				$post['cart_contents_formatted'] = nl2br(Mage::helper('quotationrequestform')->getCartContentsFormatted());

				$postObject = new Varien_Object();
				$postObject->setData($post);

				$error = false;

				if (!Zend_Validate::is(trim($post['company']) , 'NotEmpty')) {
					$error = true;
				}

				if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
					$error = true;
				}

				/*
				if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
					$error = true;
				}
				*/

				if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
					$error = true;
				}

				if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
					$error = true;
				}

				if ($error) {
					throw new Exception();
				}

				$mailTemplate = Mage::getModel('core/email_template');
				/* @var $mailTemplate Mage_Core_Model_Email_Template */
				$mailTemplate->setDesignConfig(array('area' => 'frontend'))
					->setReplyTo($post['email'])
					->sendTransactional(
						self::EMAIL_TEMPLATE_ID,
						Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
						Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
						null,
						array('data' => $postObject)
					);

				if (!$mailTemplate->getSentSuccess()) {
					throw new Exception();
				}

				$translate->setTranslateInline(true);

				Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
				$this->_redirect('*/*/');

				return;
			} catch (Exception $e) {
				$translate->setTranslateInline(true);

				Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
				$this->_redirect('*/*/');
				return;
			}
		} else {
			$this->_redirect('*/*/');
		}
	}
}  