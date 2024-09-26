<?php

namespace Scrawler;

class Storage{
    protected $engine;
    public function setAdapter($adapter){
        $this->engine = new StorageEngine($adapter);
    }

    public function __call($method, $args){
       if(is_null($this->engine)){
           throw new \Exception('Please set adapter using storage()->setAdapter($adapter) first');
       }
       return $this->engine->$method(...$args);
    }
}