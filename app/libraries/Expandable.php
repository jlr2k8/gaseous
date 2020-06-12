<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 6/7/20
 *
 * Expandable.php
 *
 * Filter class calls through this object to pick up counterpart classes from enabled expansions
 *
 **/

class Expandable
{
    private $return, $caller;
    private $loaded_expansions = [];


    public function __construct()
    {
    }


    /**
     * @param $return
     * @return mixed
     */
    public function return($return)
    {
        $this->caller = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
        $this->return = $return;

        $this->load();
        $this->run();

        return $this->return;
    }


    /**
     * @return bool
     */
    protected function load()
    {
        $class  = $this->caller[1]['class'];

        $scandir = scandir(EXPANSION_ROOT);

        foreach($scandir as $s) {
            if ($s == '.' || $s == '..') {
                continue;
            }

            $this->loaded_expansions[] = $s;
        }

        $namespaced_filepath    = str_replace('\\', DIRECTORY_SEPARATOR, $class);

        foreach ($this->loaded_expansions as $expansion) {
            $expansion_file     = EXPANSION_ROOT . DIRECTORY_SEPARATOR . $expansion . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . $namespaced_filepath . '.php';

            if (is_readable($expansion_file)) {
                require_once $expansion_file;
            }
        }

        return true;
    }


    /**
     * @return bool
     */
    protected function run()
    {
        $function                   = $this->caller[1]['function'];
        $args                       = $this->caller[1]['args'];
        $class                      = $this->caller[1]['class'];

        $this->caller[1]['return']  = $this->return;

        foreach ($this->loaded_expansions as $expansion) {
            $expansion_class    = $expansion . '\\' . $class;
            $expansion_function = $function;
            $expansion_args     = $args;

            if (method_exists($expansion_class, $function)) {
                $this->return = call_user_func_array(
                    [
                        new $expansion_class($this->caller[1]), $expansion_function
                    ],
                    $expansion_args
                );
            }
        }

        return true;
    }

}