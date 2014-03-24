<?php

/*======================================
Kodexy Framework v0.8.2
Author: Matt Larsen
Web: github.com/mattlarsen/kodexy
======================================*/

/**
 * Application startup file: code that should be run for every request before the relevant controller is executed.
 */

//session config
ini_set('session.gc_probability', '1');
ini_set('session.gc_divisor', '100');
ini_set('session.gc_maxlifetime', '1800');
//optional: session_set_save_handler(...)

//initialize Kodexy Session class.
kodexy()->session->init();