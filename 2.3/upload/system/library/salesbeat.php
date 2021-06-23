<?php

class Salesbeat
{
    protected $adaptor;

    /**
     * Salesbeat constructor.
     * @param string $adaptor
     * @param object $registry
    */
    public function __construct($adaptor, $registry = null) {
        $class = 'Salesbeat\\' . $adaptor;

        if (class_exists($class)) {
            if ($registry) {
                $this->adaptor = new $class($registry);
            } else {
                $this->adaptor = new $class();
            }

            $this->{$adaptor} = $this->adaptor;
        } else {
            trigger_error('Error: Could not load salesbeat adaptor ' . $adaptor . '!');
            exit();
        }
    }
}