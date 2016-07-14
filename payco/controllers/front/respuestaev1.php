<?php

class Respuestaev1ModuleFrontController extends ModuleFrontController 
{
public $php_self = 'respuestaev1';
public function initContent()
{
parent::initContent();
$this->assignAll();
$this->setTemplate(_PS_THEME_DIR_.'respuestaev1.tpl');
}
}
?>