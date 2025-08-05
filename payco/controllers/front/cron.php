<?php
use PrestaShop\PrestaShop\Adapter\Entity\Order;

class PaycoCronModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_header = false;
    public $display_footer = false;


    public function display(){
        try
        {
            if(!Tools::isPHPCLI()){
                $this->ajaxRender('Forbidden: se fue permitido realizar la acción!');
                return;
            }
            PrestaShopLogger::addLog(
                'Órdenes procesadas command: ',
                1,      // Severidad (1=info, 2=alerta, 3=error)
                null,   // Objeto
                null,   // ID objeto
                null,   // ID empleado
                true    // Permite duplicados
            );
            $this->writeCronLog("Inicio ejecución cron command ePayco");
            $this->ajaxRender('Ejecutando command Cron...');
            Hook::exec('actionCronJob');
        } catch (\Exception $e){
            $this->writeCronLog("Inicio ejecución cron command ePayco"+ $e->getMessage());
        } 

    }

    public function initContent()
    {
        parent::initContent();

        try
        {
            PrestaShopLogger::addLog(
                'Órdenes procesadas : ',
                1,      // Severidad (1=info, 2=alerta, 3=error)
                null,   // Objeto
                null,   // ID objeto
                null,   // ID empleado
                true    // Permite duplicados
            );
            $this->writeCronLog("Inicio ejecución cron ePayco");
            //$this->ajaxRender('Ejecutando Cron...');
            Hook::exec('actionCronJob');
        } catch (\Exception $e){
            $this->writeCronLog("Inicio ejecución cron ePayco"+ $e->getMessage());
        } 
        
    }


    private function writeCronLog($message)
    {
        $logFile = _PS_MODULE_DIR_.'payco/logs/cron.log';
        $date = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$date] $message\n", FILE_APPEND);
    }
}
