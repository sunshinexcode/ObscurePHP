<?php
/**
 * Test file
 */

// Var
$user = 'sunshine';
$email = '24xinhui@163.com';
$hello_world = 'Hello World!';

/* One line comment */
$SETTINGS = [];

// Global var
$_REQUEST = [];
$_POST = [];
$_GET = [];
$_SERVER = [];
$_COOKIE = [];
$_SESSION = [];
$_FILES = [];
$GLOBALS = [];

// Class Test
class HelloWorld {
    private $_user = 'sunshine';
    private $__users = [];
    public $user = '';
    protected $name = '';
}

// Output
echo $user;
echo $email;
echo $hello_world;
