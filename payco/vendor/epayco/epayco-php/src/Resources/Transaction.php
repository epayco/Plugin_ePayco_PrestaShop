<?php

namespace Epayco\Resources;

use Epayco\Resource;

/**
 * Class Transaction
 *
 * Genrate get transaction
 */
Class Transaction extends Resource{
    /**
     * Return data payment cash
     * @param  array $options data transaction
     * @return object
     */
    public function get($options = null)
    {
        return $this->request(
            "POST",
            "/transaction",
            $this->epayco->api_key,
            $options,
            $this->epayco->private_key,
            $this->epayco->test,
            false,
            $this->epayco->lang,
            true,
            false,
            true
        );
    }
}