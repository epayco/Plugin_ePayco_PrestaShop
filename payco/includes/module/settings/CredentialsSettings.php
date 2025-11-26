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

require_once EP_ROOT_URL . '/includes/module/settings/AbstractSettings.php';

class CredentialsSettings extends AbstractSettings
{
    public function __construct()
    {
        parent::__construct();
        $this->submit = 'submitEpaycoCredentials';
        $this->values = $this->getFormValues();
        $this->form = $this->generateForm();
        $this->process = $this->verifyPostProcess();
    }

    /**
     * Generate inputs form
     *
     * @return void
     */
    public function generateForm()
    {
        $states = CreditCard_OrderState::getOrderStates();
        $order_states = array();
        foreach ($states as $state) {
            $order_states[] = array("id" => $state["id_order_state"], "name" => $state["name"]);
        }
        $title = $this->module->l('Ajustes', 'CredentialsSettings');
        $fields = array(
            array(
                'col' => 4,
                'type' => 'switch',
                'label' => $this->module->l('Habilitar modo de pruebas', 'CredentialsSettings'),
                'name' => 'EPAYCO_PROD_STATUS',
                'is_bool' => true,
                'desc' => $this->module->l('Elija "NO" solo cuando esté listo para comenzar a vender en producción. Seleccione "Sí" para activar el Sandbox (pruebas), entorno de prueba.', 'CredentialsSettings'),
                'values' => array(
                    array(
                        'id' => 'EPAYCO_PROD_STATUS_ON',
                        'value' => true,
                        'label' => $this->module->l('Yes', 'CredentialsSettings')
                    ),
                    array(
                        'id' => 'EPAYCO_PROD_STATUS_OFF',
                        'value' => false,
                        'label' => $this->module->l('No', 'CredentialsSettings')
                    )
                ),
                'class' => 'custom-switch-class'
            ),
            array(
                'col' => 8,
                'type' => 'html',
                'name' => '',
                'desc' => '',
                'label' => $this->module->l('Cargar credenciales', 'CredentialsSettings'),
                'html_content' => '<a href="https://eks-dashboard-service.epayco.io/configuration" target="_blank" class="btn btn-default mp-btn-credenciais">'
                    . $this->module->l('Buscar mis credenciales', 'CredentialsSettings') . '</a>'
            ),
            array(
                'col' => 8,
                'type' => 'text',
                'desc' => '',
                'name' => 'EPAYCO_P_CUST_ID_CLIENTE',
                'label' => $this->module->l('P_CUST_ID_CLIENTE', 'CredentialsSettings'),
                'required' => true,
                'class' => 'custom-html-class'
            ),
            array(
                'col' => 8,
                'type' => 'text',
                'desc' => '',
                'name' => 'EPAYCO_P_KEY',
                'label' => $this->module->l('P_KEY', 'CredentialsSettings'),
                'required' => true,
                'class' => 'custom-html-class'
            ),
            array(
                'col' => 8,
                'type' => 'text',
                'desc' => '',
                'name' => 'EPAYCO_PUBLIC_KEY',
                'label' => $this->module->l('PUBLIC_KEY', 'CredentialsSettings'),
                'required' => true,
                'class' => 'custom-html-class'
            ),
            array(
                'col' => 8,
                'type' => 'text',
                'desc' => '',
                'name' => 'EPAYCO_PRIVATE_KEY',
                'label' => $this->module->l('PRIVATE_KEY', 'CredentialsSettings'),
                'required' => true,
                'class' => 'custom-html-class'
            ),
            array(
                'type' => 'select',
                'label' => $this->module->l('Estado orden','CredentialsSettings'),
                'name' => 'EPAYCO_STANDARD_STATE_END_TRANSACTION',
                'desc' => $this->module->l('Elija el estado de pago a aplicar al aceptar la transacción.', 'CredentialsSettings'),
                'required' => true,
                'options' => array(
                    'id' => 'id',
                    'name' => 'name',
                    'default' => array(
                        'value' => '2',
                        'label' => $this->module->l('Pago aceptado', 'CredentialsSettings')
                    ),
                    'query' => $order_states,
                ),
                'class' => 'custom-html-class'
            )
        );

        return $this->buildForm($title, $fields);
    }

    /**
     * Save form data
     *
     * @return void
     */
    public function postFormProcess()
    {
        $this->validate = ([
            'EPAYCO_P_CUST_ID_CLIENTE' => 'p_cust_id',
            'EPAYCO_P_KEY' => 'p_key',
            'EPAYCO_PUBLIC_KEY' => 'public_key',
            'EPAYCO_PRIVATE_KEY' => 'private_key',
        ]);

        parent::postFormProcess();

        //activate checkout
        if (Payco::$form_alert != 'alert-danger') {
            //$access_token = $this->payco->token->create();
            Payco::$form_message = $this->module->l('Configuración guardada exitosamente. Ahora puedes configurar el módulo.', 'CredentialsSettings');

            Configuration::updateValue('EPAYCO_CHECK_CREDENTIALS', true);
        }
    }

    /**
     * Set values for the form inputs
     *
     * @return array
     */
    public function getFormValues()
    {
        return array(
            'EPAYCO_PROD_STATUS' => Configuration::get('EPAYCO_PROD_STATUS'),
            'EPAYCO_P_CUST_ID_CLIENTE' => Configuration::get('EPAYCO_P_CUST_ID_CLIENTE'),
            'EPAYCO_P_KEY' => Configuration::get('EPAYCO_P_KEY'),
            'EPAYCO_PUBLIC_KEY' => Configuration::get('EPAYCO_PUBLIC_KEY'),
            'EPAYCO_PRIVATE_KEY' => Configuration::get('EPAYCO_PRIVATE_KEY'),
            'EPAYCO_STANDARD_STATE_END_TRANSACTION' => Configuration::get('EPAYCO_STANDARD_STATE_END_TRANSACTION'),
        );
    }


}
