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
        parent::__construct($context);
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
        
        $ticketPaymentMethods = [
            [
                'id' => 'sured',
                'name' => 'Su Red',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/Sured.png"
            ],
            [
                'id' => 'pagatodo',
                'name' => 'Paga Todo',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/Pagatodo.png"
            ],
            [
                'id' => 'gana',
                'name' => 'Gana',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/gana.png"
            ],
            [
                'id' => 'acertemos',
                'name' => 'Acertemos',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/Acertemos.png"
            ],
            [
                'id' => 'ganagana',
                'name' => 'Gana Gana',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/Ganagana.png"
            ],
            [
                'id' => 'suchance',
                'name' => 'SuChance',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/SuChance.png"
            ],
            [
                'id' => 'redservicioscesar',
                'name' => 'Red Servicios del Cesar',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/Reddeservicios.png"
            ],
            [
                'id' => 'apuestascucuta',
                'name' => 'Apuestas Cúcuta 75',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/Apuestascucuta.png"
            ],
            [
                'id' => 'jer',
                'name' => 'Jer',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/Jer.png"
            ],
            [
                'id' => 'laperla',
                'name' => 'La Perla',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/Laperla.png"
            ],
            [
                'id' => 'efecty',
                'name' => 'Efecty',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/Efecty.png"
            ],
            [
                'id' => 'puntored',
                'name' => 'Punto Red',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/Puntored.png"
            ],
            [
                'id' => 'redservi',
                'name' => 'Red Servi',
                'status' => 'active',
                'secure_thumbnail' => "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/cash/Redservi.png"
            ],
        ];

        // Order of the payment methods to be displayed in the front, based on the name. If a method is not in the list, it will be displayed at the end.
        $order = [
            'Su Red',
            'Paga Todo',
            'Gana',
            'Acertemos',
            'Gana Gana',
            'SuChance',
            'Red Servicios del Cesar',
            'Apuestas Cúcuta 75',
            'Jer',
            'La Perla',
            'Efecty',
            'Punto Red',
            'Red Servi',
        ];

        $ticket = [];
        if (!empty($ticketPaymentMethods)) {
            foreach ($ticketPaymentMethods as $ticketPaymentMethod) {
                $configValue = Configuration::get('EPAYCO_TICKET_PAYMENT_' . $ticketPaymentMethod['id']);
                // Include by default. Only exclude if explicitly disabled ('0' or 'no')
                if ($configValue !== '0' && $configValue !== 'no') {
                    $ticket[] = $ticketPaymentMethod;
                }
            }
        }

        // Order whith the array $order
        usort($ticket, function ($a, $b) use ($order) {
            $posA = array_search($a['name'], $order);
            $posB = array_search($b['name'], $order);
            return $posA - $posB;
        });

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