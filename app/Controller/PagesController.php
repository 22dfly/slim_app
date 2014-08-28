<?php
require 'AppController.php';

class PagesController extends AppController {

    public function home()
    {
        $this->render('home', array('message' => 'Hello World!'));
    }
}
