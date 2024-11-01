<?php

if (!class_exists('BST_Utils_Configuration')) {
    class BST_Utils_Configuration
    {
        public static function isProActivated()
        {
            if (class_exists('SureCart\Licensing\Client')) {
                $activation_key = get_option('wpswisstoolkitpro_license_options');

                if( $activation_key && count($activation_key) > 0 && isset($activation_key['sc_license_key']) && $activation_key['sc_license_key'] !== '') {
                    return true;
                }
            } else {
                global $bst_pro_license;
                if ($bst_pro_license) {
                    return $bst_pro_license->is_valid();
                }
            }

            return false;
        }
    }
}
