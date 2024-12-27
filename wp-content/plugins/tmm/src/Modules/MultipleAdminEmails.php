<?php

namespace TMM\Modules;

use TMM\Core\Module;

class MultipleAdminEmails {


    const OPTION_NAME = 'tmm_multiple_admin_emails';

    use Module;

    public function init() {
        add_filter('admin_init', [$this, 'register_fields']);
        add_filter('pre_update_option_' . self::OPTION_NAME, [$this, 'sanitize_multiple_emails'], 10, 2);
        add_filter('option_admin_email', [$this, 'get_real_addresses']);
        add_action('update_option_' . self::OPTION_NAME, [$this, 'remove_hashes'], 10, 3);
        
//        add_action('wp_head', function (){
//            wp_mail(get_option('admin_email'), 'test', 'test email body');
//        });
    }


    public function remove_hashes($option, $old_value, $value) {
        delete_option('adminhash');
        delete_option('new_admin_email');
    }

    public function register_fields() {
        register_setting('general', self::OPTION_NAME);
        add_settings_field(self::OPTION_NAME, '<label for="tmm-multiple-admin-emails">' . __('Multiple Admin Emails', 'tmm') . '</label>', [$this, 'fields_html'], 'general');
    }

    public function fields_html() {
        $value = get_option(self::OPTION_NAME, '');
        echo '<input type="text" id="tmm-multiple-admin-emails" name="' . self::OPTION_NAME . '" class="regular-text ltr" value="' . $value . '" />';
        echo '<p class="description" id="multiple-admin-emails-description">' . __('This address overrides the Admin Email. You can add one or more emails seperated by commas', 'tmm') . '</p>';
    }

    public function get_real_addresses($value) {
        $multi = get_option(self::OPTION_NAME);

        if (strlen($multi) == 0)
            return $value;
        return $multi;
    }

    public function sanitize_multiple_emails($new_value, $old_value) {
        $result = "";
        $emails = explode(",", $new_value);

        if( empty( $emails ) ) {
            return $old_value;
        }

        foreach ($emails as $email) {
            $email = trim($email);
            $email = sanitize_email($email);
            if (!is_email($email)) {
                continue;
            }
            $result .= $email . ",";

        }

        if ($result === "") {
            return $old_value;
        }

        return substr($result, 0, -1);
    }

}
