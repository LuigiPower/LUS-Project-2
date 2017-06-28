<?php

class log {
    private $base_path;
    
    public function __construct($path) {
        $this->base_path = $path;
    }
    
    
    public function write($type, $message){
        $filepath = $this->base_path . $type . '_' . date("Y-m-d") . '.log';
        return file_put_contents($filepath, "\n$type-".date("his")."\n$message", FILE_APPEND);
    }
    
}