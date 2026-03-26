<?php
 
 namespace Epayco;
 
/**
 * Resource methods
 */
 class Resource extends Client
 {
    protected $epayco;
    /**
      * Instance payco class
      * @param array $epayco
     */
     public function __construct($epayco)
    {
        $this->epayco = $epayco;
    }
 }