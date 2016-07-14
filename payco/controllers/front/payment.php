<?php
/**
 * @since 1.5.0
 */
class PaycoPaymentModuleFrontController extends ModuleFrontController
{
        public $ssl = true;
        public $display_column_left = false;
        public $display_column_right = false;

        /**
	 * @see FrontController::initContent()
	 */
        public function initContent()
        { 
                parent::initContent();

                $cart = $this->context->cart;
		if (!$this->module->checkCurrency($cart))
			Tools::redirect('index.php?controller=order');

                $this->context->smarty->assign(array(
                        'nbProducts' => $cart->nbProducts(),
                        'cust_currency' => $cart->id_currency,
                        'currencies' => $this->module->getCurrency((int)$cart->id_currency),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
                        'iva' => $cart->getOrderTotal(true, Cart::BOTH) - $cart->getOrderTotal(false, Cart::BOTH),
                        'baseDevolucionIva' => $cart->getOrderTotal(false, Cart::BOTH),
                        'isoCode' => $this->context->language->iso_code,
                        'pagosonlineuserid' => $this->module->userid,
                        'merchandid' => $this->module->merchandid,
                        'merchanttest' => $this->module->merchandtest,
                        'merchanpassword' => $this->module->merchandpassword,
                        'p_key'=>sha1($this->module->merchandpassword.$this->module->merchandid),
                        'this_path' => $this->module->getPathUri(),
                        'this_path_bw' => $this->module->getPathUri(),
                        'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
                ));

                $this->setTemplate('payment_execution.tpl');
        }
}
?>