<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2024 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once EP_ROOT_URL . '/includes/module/checkouts/AbstractEpaycoCheckout.php';

class TicketEpaycoCheckout extends AbstractEpaycoCheckout
{
    public $name;
    public $context;
    public $path;
    public $assetsExtMin;
    /**
     * Ticket Checkout constructor
     *
     * @param $name
     * @param $context
     * @param $path
     */
    public function __construct($name, $context, $path)
    {
        parent::__construct();
        $this->name = $name;
        $this->context = $context;
        $this->path = $path;
        $this->assetsExtMin = !_PS_MODE_DEV_ ? '.min' : '';
    }

    /**
     * @param $cart
     * @return array
     * @throw PrestaShopException
     */
    public function getTicketCheckoutPS16($cart)
    {
        $checkoutInfo = $this->getTicketCheckout($cart);
        $frontInformations = array_merge(
            $checkoutInfo,
            array("ep_logo" =>  _MODULE_DIR_ . 'payco/views/img/icon-ticket.png')
        );
        return $frontInformations;
    }

    /**
     * @param $cart
     * @return array
     * @throw PrestaShopException
     */
    public function getTicketCheckoutPS17($cart)
    {
        $checkoutInfo = $this->getTicketCheckout($cart);
        $fronInformations = array_merge(
            $checkoutInfo,
            array("module_dir" => $this->path)
        );
        return $fronInformations;
    }

    /**
     * @param $cart
     * @return array
     * @throw PrestaShopException
     */
    public function getTicketCheckout($cart)
    {
        $this->getTicketJS();
        $ticket = array();
        $module = Module::getInstanceByName('payco');
        $ticketPaymentMethods = [
            [
                'id' => 'efecty',
                'name'              => 'Efecty',
                'status'            => 'active',
                'thumbnail'         => 'https://secure.epayco.co/img/efecty.png'
            ],
            [
                'id' => 'gana',
                'name'              => 'Gana',
                'status'            => 'active',
                'thumbnail'         => 'https://secure.epayco.co/img/gana_no_red.png'
            ],
            [
                'id' => 'puntored',
                'name'              => 'Puntored',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/puntored.jpg'
            ],
            [
                'id' => 'redservi',
                'name'              => 'Redservi',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/redservi.jpg'
            ],
            [
                'id' => 'sured',
                'name'              => 'Sured',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/sured.jpg'
            ],
            [
                'id' => 'suchance',
                'name'              => 'Suchance',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/suchance.jpg'
            ],
            [
                'id' => 'laperla',
                'name'              => 'Laperla',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/laperla.jpg'
            ],
            [
                'id' => 'jer',
                'name'              => 'Jer',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/jer.jpg'
            ],
            [
                'id' => 'pagatodo',
                'name'              => 'Pagatodo',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/pagatodo.jpg'
            ],
            [
                'id' => 'acertemos',
                'name'              => 'Acertemos',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/acertemos.jpg'
            ],
            [
                'id' => 'ganagana',
                'name'              => 'Ganagana',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/ganagana.jpg'
            ],
        ];

        if (!empty($ticketPaymentMethods)) {
            foreach ($ticketPaymentMethods as $ticketPaymentMethod) {
                if (Configuration::get('EPAYCO_TICKET_PAYMENT_' . $ticketPaymentMethod['id']) != "") {
                    $ticket[] = $ticketPaymentMethod;
                }
            }
        }

        sort($ticket);

        $address = new Address((int) $cart->id_address_invoice);
        $context = Context::getContext();
        $discount = Configuration::get('EPAYCO_TICKET_DISCOUNT');
        $redirect = $this->context->link->getModuleLink($this->name, 'ticket');
        $info = array(
            "ticket" => json_encode($this->treatTicketPaymentMethods($ticket)),
            "documents" => json_encode($this->getIdentificationDocuments()),
            "address" => $address,
            "version" => EP_VERSION,
            "context" => $context,
            "redirect" => $redirect,
            "discount" => $discount,
            "module_dir" => $this->path,
            "assets_ext_min" => $this->assetsExtMin
        );

        return $info;
    }

    /**
     * Get ticket JS
     */
    public function getTicketJS()
    {
        $this->context->controller->addJS(
            $this->path . '/views/js/checkouts/ticket/ep-ticket-checkout.js?v=' . EP_VERSION
        );
    }

    /**
     * Treat ticket payment methods with composite IDs
     *
     * @param array $paymentMethods
     *
     * @return array
     */
    public function treatTicketPaymentMethods(array $paymentMethods): array
    {
        $treatedPaymentMethods = [];

        foreach ($paymentMethods as $paymentMethod) {
            $treatedPaymentMethod = [];

            if (isset($paymentMethod['payment_places'])) {
                foreach ($paymentMethod['payment_places'] as $place) {
                    $paymentPlaceId                  = $paymentMethod['id'];
                    $treatedPaymentMethod['id']      = $paymentPlaceId;
                    $treatedPaymentMethod['value']   = $paymentPlaceId;
                    $treatedPaymentMethod['rowText'] = $place['name'];
                    $treatedPaymentMethod['img']     = $place['thumbnail'];
                    $treatedPaymentMethod['alt']     = $place['name'];
                    $treatedPaymentMethods[]         = $treatedPaymentMethod;
                }
            } else {
                $treatedPaymentMethod['id']      = $paymentMethod['id'];
                $treatedPaymentMethod['value']   = $paymentMethod['id'];
                $treatedPaymentMethod['rowText'] = $paymentMethod['name'];
                $treatedPaymentMethod['img']     = $paymentMethod['thumbnail']??$paymentMethod['secure_thumbnail'];
                $treatedPaymentMethod['alt']     = $paymentMethod['name'];
                $treatedPaymentMethods[]         = $treatedPaymentMethod;
            }
        }

        return $treatedPaymentMethods;
    }


}