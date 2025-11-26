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

//require_once EP_ROOT_URL . '/vendor/epayco/epayco-php/src/Epayco.php';

if (!defined('_PS_VERSION_')) {
    exit;
}
use Epayco as EpaycoSdk;
class AbstractSettings
{
    public $form;
    public $module;
    public $values;
    public $submit;
    public $process;
    public $epayco;
    protected $validate;
    private const EP_APIFY  = "https://eks-apify-service.epayco.io";

    public function __construct()
    {
        $this->module = Module::getInstanceByName('payco');
        $public_key = Configuration::get('EPAYCO_PUBLIC_KEY');
        $private_key = Configuration::get('EPAYCO_PRIVATE_KEY');
        $test = (bool)Configuration::get('EPAYCO_PROD_STATUS');
        $lang = $this->module->_context->language->iso_code == 'es' ? 'es' : "en";
        $this->epayco  = new EpaycoSdk\Epayco(array(
            "apiKey" => $public_key,
            "privateKey" => $private_key,
            "lenguage" => $lang,
            "test" => !$test
        ));
    }

    /**
     * Build Config Form
     *
     * @return void
     */
    public function buildForm($title, $fields)
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $title,
                    'icon' => 'icon-cogs',
                ),
                'class' => 'credentials',
                'input' => $fields,
                'submit' => array(
                    'title' => $this->module->l('Save', 'AbstractSettings')
                ),
            ),
        );
    }

    /**
     * Verify form submit
     *
     * @return void
     */
    public function verifyPostProcess()
    {
        if (((bool) Tools::isSubmit($this->submit)) == true) {
            return $this->postFormProcess();
        }
    }

    /**
     * Save form data
     *
     * @return void
     */
    public function postFormProcess()
    {
        $form_alert = false;

        foreach (array_keys($this->values) as $key) {
            $value = htmlentities(strip_tags(Tools::getValue($key)), ENT_QUOTES, 'UTF-8');

            if (!$this->validateInput($key, $value)) {
                $form_alert = true;
                continue;
            }

            $this->values[$key] = $value;
            Configuration::updateValue($key, $value);
        }

        if ($form_alert == false) {
            if(!$this->validateCredentials($this->values)){
                Payco::$form_alert = 'alert-danger';
                Payco::$form_message .= $this->module->l(
                        'invalid credentials', 'AbstractSettings');
            }else{
                Payco::$form_alert = 'alert-success';
                Payco::$form_message = $this->module->l('Settings saved successfully.', 'AbstractSettings');
            }
        }
    }

    /**
     * Validate credentials and save seller information
     *
     * @param string $value
     * @return boolean
     */
    public function validateCredentials($value)
    {
        if(isset($value["EPAYCO_PUBLIC_KEY"]) && isset($value["EPAYCO_PRIVATE_KEY"])){
            $token_validation = $this->isValidAccessToken($value["EPAYCO_PUBLIC_KEY"],$value["EPAYCO_PRIVATE_KEY"]);
            if (!$token_validation) {
                return false;
            }
            return true;
        }else{
            return true;
        }

    }

    /**
     * Validate credentials and save seller information
     * @param string $publick_key
     * @param string $private_key
     * @return array|bool
     */
    public function isValidAccessToken($publick_key,$private_key)
    {
        try {
            $publicKey = trim($publick_key);
            $private_key = trim($private_key);

            $headers = [];
            $uri     = '/login';
            $accessToken = base64_encode($publicKey.":".$private_key);
            $headers[] = 'Authorization: Basic ' . $accessToken;
            $headers[] = 'Content-Type: application/json ';
            $body = array(
                'public_key' => $publicKey,
                'private_key' => $private_key,
            );
            $response           = $this->my_prestashop_post_request($uri, $headers, $body);
            if(isset($response) && isset($response['token'])){
                return true;
            }else{
                return false;
            }

        } catch (Exception $e) {
            return false;
        }
    }
    private function my_prestashop_post_request($uri, $headers, $body = []) {
        $url = self::EP_APIFY.$uri;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => $headers,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response_data = json_decode( $response, true );

        return $response_data;
    }

    /**
     * Validate input for submit
     *
     * @param mixed $input
     * @return boolean
     */
    public function validateInput($input, $value)
    {
        if ($this->validate != null && array_key_exists($input, $this->validate)) {
            switch ($this->validate[$input]) {
                case "expiration_preference":
                    if ($value != '' && !is_numeric($value)) {
                        Epayco::$form_alert = 'alert-danger';
                        Epayco::$form_message .= $this->module->l(
                            'The time to save payment preferences ',
                            'AbstractSettings'
                        ) . $this->module->l('must be an integer.', 'AbstractSettings');
                        return false;
                    }
                    break;

                case "title":
                    if ($value == '') {
                        Epayco::$form_alert = 'alert-danger';
                        Epayco::$form_message = $this->module->l('El título no puede estar vacío y debe ser válido. ', 'AbstractSettings') .
                            $this->module->l('Por favor completa el título para habilitar el módulo.', 'AbstractSettings');
                        return false;
                    }
                    break;

                case "p_cust_id":
                    if ($value == '') {
                        Epayco::$form_alert = 'alert-danger';
                        Epayco::$form_message = $this->module->l('P_CUST_ID no puede estar vacío y debe ser válido. ', 'AbstractSettings') .
                            $this->module->l('Por favor completa tus credenciales para habilitar el módulo.', 'AbstractSettings');
                        return false;
                    }
                    break;

                case "p_key":
                    if ($value == '') {
                        Epayco::$form_alert = 'alert-danger';
                        Epayco::$form_message = $this->module->l('P_KEY no puede estar vacío y debe ser válido. ', 'AbstractSettings') .
                            $this->module->l('Por favor completa tus credenciales para habilitar el módulo.', 'AbstractSettings');
                        return false;
                    }
                    break;

                case "public_key":
                    if ($value == '') {
                        Epayco::$form_alert = 'alert-danger';
                        Epayco::$form_message = $this->module->l('PUBLIC_KEY no puede estar vacío y debe ser válido. ', 'AbstractSettings') .
                            $this->module->l('Por favor completa tus credenciales para habilitar el módulo.', 'AbstractSettings');
                        return false;
                    }
                    break;
                case "private_key":
                    if ($value == '') {
                        Epayco::$form_alert = 'alert-danger';
                        Epayco::$form_message = $this->module->l('PRIVATE_KEY no puede estar vacío y debe ser válido. ', 'AbstractSettings') .
                            $this->module->l('Por favor completa tus credenciales para habilitar el módulo.', 'AbstractSettings');
                        return false;
                    }
                    break;
                default:
                    return true;
            }
        }

        return true;
    }
}
