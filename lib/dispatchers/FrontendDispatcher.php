<?php

if ( ! defined( 'ABSPATH' ) ) exit;


if (!class_exists('Oc3dAig_FrontendDispatcher')) {

    class Oc3dAig_FrontendDispatcher{
        
        public $chatbot_controller;
        

        public function __construct() {
            if (!class_exists('Oc3dAig_ChatBotController')) {
                $contr_path = OC3DAIG_PATH . "/lib/controllers/ChatBotController.php";
                include_once $contr_path;
            }
            $this->chatbot_controller = new Oc3dAig_ChatBotController();
        }
        
        

    }

}
