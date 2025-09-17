<?php

/**
 * Defines statuses which will trigger the
 * deletion of credit data from the database
 */

class CreditCard_OrderState extends ObjectModel
{

    public static function getOrderStates($ids_only = false)
    {
        global $cookie;

        $returnStates = array();

        $states = OrderState::getOrderStates($cookie->id_lang);

        $id_initial_state = Configuration::get('PAYCO_ORDERSTATE_WAITING');

        foreach($states as $k => $state)
        {
            if($ids_only)
            {
                $returnStates[] = $state['id_order_state'];
            }
            else
            {
                $returnStates[] = $state;
            }
        }
        return $returnStates;
    }



    public static function getInitialState()
    {
        return Configuration::get('PAYCO_ORDERSTATE_WAITING');
    }


    public static function updateStates($id_initial_state, $delete_on)
    {
        //Configuration::updateValue('CREDITCARD_DATA_OS_DELETEON', implode(',', $delete_on));
        //Configuration::updateValue('CREDITCARD_DATA_OS_INITIAL', intval($id_initial_state));
        return true;
    }

    public static function setup()
    {
        if (!Configuration::get('PAYCO_ORDERSTATE_WAITING'))
        {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Esperando Pago';

            $order_state->send_email = false;
            $order_state->color = '#FEFF64';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->add();
            Configuration::updateValue('PAYCO_ORDERSTATE_WAITING', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_ORDERSTATE_WAITING_TEST'))
        {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Esperando Pago Prueba';

            $order_state->send_email = false;
            $order_state->color = '#FEFF64';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            //$order_state->add();
            //Configuration::updateValue('PAYCO_ORDERSTATE_WAITING_TEST', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_PENDING'))
        {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Pendiente';

            $order_state->send_email = false;
            $order_state->color = '#FEFF64';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->add();
            Configuration::updateValue('PAYCO_OS_PENDING', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_PENDING_TEST'))
        {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Pendiente Prueba';

            $order_state->send_email = false;
            $order_state->color = '#FEFF64';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            //$order_state->add();
            //Configuration::updateValue('PAYCO_OS_PENDING_TEST', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_FAILED'))
        {
            $order_state = new OrderState();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Fallido';
            $order_state->send_email = false;
            $order_state->color = '#8F0621';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->add();

            Configuration::updateValue('PAYCO_OS_FAILED', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_FAILED_TEST'))
        {
            $order_state = new OrderState();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Fallido Prueba';
            $order_state->send_email = false;
            $order_state->color = '#8F0621';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            //$order_state->add();

            //Configuration::updateValue('PAYCO_OS_FAILED_TEST', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_REJECTED'))
        {
            $order_state = new OrderState();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Rechazado';
            $order_state->send_email = false;
            $order_state->color = '#8F0621';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->add();
            Configuration::updateValue('PAYCO_OS_REJECTED', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_REJECTED_TEST'))
        {
            $order_state = new OrderState();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Rechazado Prueba';
            $order_state->send_email = false;
            $order_state->color = '#8F0621';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            //$order_state->add();
            //Configuration::updateValue('PAYCO_OS_REJECTED_TEST', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_EXPIRED'))
        {
            $order_state = new OrderState();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Expirado';
            $order_state->send_email = false;
            $order_state->color = '#8F0621';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->add();
            Configuration::updateValue('PAYCO_OS_EXPIRED', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_EXPIRED_TEST'))
        {
            $order_state = new OrderState();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Expirado Prueba';
            $order_state->send_email = false;
            $order_state->color = '#8F0621';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            //$order_state->add();
            //Configuration::updateValue('PAYCO_OS_EXPIRED_TEST', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_ABANDONED'))
        {
            $order_state = new OrderState();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Abandonado';
            $order_state->send_email = false;
            $order_state->color = '#8F0621';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->add();
            Configuration::updateValue('PAYCO_OS_ABANDONED', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_ABANDONED_TEST'))
        {
            $order_state = new OrderState();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Abandonado Prueba';
            $order_state->send_email = false;
            $order_state->color = '#8F0621';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            //$order_state->add();
            //Configuration::updateValue('PAYCO_OS_ABANDONED_TEST', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_CANCELED'))
        {
            $order_state = new OrderState();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Cancelado';
            $order_state->send_email = false;
            $order_state->color = '#8F0621';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->add();
            Configuration::updateValue('PAYCO_OS_CANCELED', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_CANCELED_TEST'))
        {
            $order_state = new OrderState();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Cancelado Prueba';
            $order_state->send_email = false;
            $order_state->color = '#8F0621';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            //$order_state->add();
            //Configuration::updateValue('PAYCO_OS_CANCELED_TEST', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_PAYMENT'))
        {
            $order_state = new OrderState();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Aceptado';
            $order_state->send_email = true;
            $order_state->color = '#3498D8';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->add();
            Configuration::updateValue('PAYCO_OS_PAYMENT', (int)$order_state->id);
        }

        if (!Configuration::get('PAYCO_OS_PAYMENT_TEST'))
        {
            $order_state = new OrderState();
            foreach (Language::getLanguages() as $language)
                $order_state->name[$language['id_lang']] = 'ePayco Pago Aceptado Prueba';
            $order_state->send_email = true;
            $order_state->color = '#3498D8';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            //$order_state->add();
            //Configuration::updateValue('PAYCO_OS_PAYMENT_TEST', (int)$order_state->id);
        }
    }

    public static function remove(){

        $statuses = [
            'PAYCO_ORDERSTATE_WAITING',
            'PAYCO_OS_PENDING',
            'PAYCO_OS_FAILED',
            'PAYCO_OS_REJECTED',
            'PAYCO_OS_EXPIRED',
            'PAYCO_OS_ABANDONED',
            'PAYCO_OS_CANCELED',
            'PAYCO_OS_PAYMENT'
            /*'PAYCO_ORDERSTATE_WAITING_TEST',
            'PAYCO_OS_PENDING_TEST',
            'PAYCO_OS_FAILED_TEST',
            'PAYCO_OS_REJECTED_TEST',
            'PAYCO_OS_EXPIRED_TEST',
            'PAYCO_OS_ABANDONED_TEST',
            'PAYCO_OS_CANCELED_TEST',
            'PAYCO_OS_PAYMENT_TEST'*/

        ];

        foreach ($statuses as $state) {
            self::deleteOrderState((int)Configuration::get($state));
            Configuration::deleteByName($state);
        }

        /*Configuration::deleteByName('PAYCO_OS_PENDING');
        Configuration::deleteByName('PAYCO_OS_FAILED');
        Configuration::deleteByName('PAYCO_OS_REJECTED');
        Configuration::deleteByName('PAYCO_OS_EXPIRED');
        Configuration::deleteByName('PAYCO_OS_ABANDONED');
        Configuration::deleteByName('PAYCO_OS_CANCELED');*/
    }

    public static function deleteOrderState($id_order_state) {

        $orderState = new OrderState($id_order_state);
        $orderState->delete();

    }
}

?>