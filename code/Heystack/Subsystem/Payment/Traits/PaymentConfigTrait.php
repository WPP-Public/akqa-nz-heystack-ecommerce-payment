<?php

namespace Heystack\Subsystem\Payment\Traits;

trait PaymentConfigTrait
{
    abstract protected function getRequiredConfigParameters();
    
    public function setConfig(array $config)
    {
        $missing = array_diff($this->getRequiredConfigParameters(), array_keys($config));
        
        if(!count($missing)){
            foreach($config as $key => $value){
                $this->data[self::CONFIG_KEY][$key] = $value;
            }
        }else{
            throw new \Exception('The following settings are missing: ' . implode(', ', $missing));
        }
    }
    
    public function getConfig()
    {
        return isset($this->data[self::CONFIG_KEY]) ? $this->data[self::CONFIG_KEY] : null;
    }
}