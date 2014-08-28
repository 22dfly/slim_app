<?php
Class AppController extends \Slim\Slim
{
    protected $data;

    public function __construct()
    {
        $settings = array(
            'view' => new \Slim\View(),
            'templates.path' => VIEW_PATH
        );

        parent::__construct($settings);
    }

    public function render($name, $data = array(), $status = null)
    {
        $name = $name . ".php";
        parent::render($name, $data, $status);
    }
}
