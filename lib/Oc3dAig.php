<?php

if (!class_exists('Oc3dAig')) {

    class Oc3dAig {

        public $admin_controller;
        public $frontend_dispatcher;
        
        public function __construct() {
            $this->admin_controller = new Oc3dAig_AdminController();
            $this->frontend_dispatcher = new Oc3dAig_FrontendDispatcher();
            add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
            add_filter('plugin_action_links', [$this, 'actionLinks'], 10, 2);
        }

        public function enqueueScripts() {
            wp_enqueue_script('oc3daig_backend', OC3DAIG_URL . '/views/resources/js/oc3daig-admin.js', [], '2.4', true);
        }

        public function actionLinks($links, $file) {
            $fl2 = plugin_basename(dirname(dirname(__FILE__))) . '/oc3d-ai-genius.php';
            if ($file == $fl2) {
                $mylinks[] = '<a href="' . get_admin_url(null, 'admin.php?page=oc3daig_settings') . '">' . __('Settings', 'woocommerce') . '</a>';

                return $mylinks + $links;
            }
            return $links;
        }

        public static function install() {
            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $wpdb->prefix . 'oc3daig_instructions';
            $wpdb->query(
                    'CREATE TABLE IF NOT EXISTS `' . $table_name . '` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `typeof_instruction` int(11) NOT NULL DEFAULT "0" COMMENT "1-instruction for text-edit , 2 - for code-edit,  other values are reserved for funture needs"  ,
                `instruction`  MEDIUMTEXT  ,
                `disabled`  SMALLINT NOT NULL DEFAULT "0",
                `user_id`  int(11) NOT NULL DEFAULT "0" 
                ) ENGINE = INNODB
            ' . $charset_collate
            );

            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Correct any grammatical errors in the document.',
                    ),
                    array('%d', '%s'));

            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Improve the clarity and readability of this passage.',
                    ),
                    array('%d', '%s'));

            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Paraphrase the highlighted sentences without changing the meaning.',
                    ),
                    array('%d', '%s'));
            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Rewrite this paragraph to evoke a specific emotion (e.g., joy, empathy, curiosity).',
                    ),
                    array('%d', '%s'));

            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Adjust the language to appeal to a specific target audience (e.g., teenagers, professionals, parents).',
                    ),
                    array('%d', '%s'));
            
            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Add more details and examples to support the main argument.',
                    ),
                    array('%d', '%s'));
            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Simplify the language to make it more accessible.',
                    ),
                    array('%d', '%s'));
            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Restructure the text for better flow.',
                    ),
                    array('%d', '%s'));
            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Replace repetitive words and phrases.',
                    ),
                    array('%d', '%s'));
            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Revise the introduction to make it more engaging.',
                    ),
                    array('%d', '%s'));
            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Check for and correct any spelling mistakes.',
                    ),
                    array('%d', '%s'));
            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Rewrite this sentence to be more concise and clear.',
                    ),
                    array('%d', '%s'));
            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Improve the overall organization and structure of the content.',
                    ),
                    array('%d', '%s'));
            $wpdb->insert($wpdb->prefix . 'oc3daig_instructions', array(
                'typeof_instruction' => 1,
                'instruction' => 'Add a relevant example to illustrate this text.',
                    ),
                    array('%d', '%s'));
            
            
            

        }

        public static function deactivate() {
            
        }

        public static function uninstall() {
            
        }

        public function bootstrap() {
            
        }

        /**
         * Get localized string.
         *
         * @param string $msg
         * @return string
         */
        public static function __($msg) {
            return __($msg, self::TEXT_DOMAIN);
        }
    }

}
