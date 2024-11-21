<?php
/*
Plugin Name: Floating QR Code vCard
Description: Transform your website into a digital business card hub with an elegant floating QR code that allows instant contact sharing.
Version: 1.0
Author: WecodeGeneva
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

add_action('admin_menu', 'my_vcard_plugin_menu');

function my_vcard_plugin_menu()
{
    add_menu_page(
        __('VCard Settings', 'floating-qr-code-vcard'),
        __('VCard', 'floating-qr-code-vcard'),
        'manage_options',
        'floating-qr-code-vcard',
        'my_vcard_plugin_settings_page',
        'dashicons-id', // Icône du menu
        100 // Position du menu
    );
}

function my_vcard_plugin_settings_page()
{
    // Vérifier les permissions
    if (!current_user_can('manage_options')) {
        return;
    }

    // Add nonce field and verification
    if (isset($_POST['my_vcard_plugin_hidden_field']) && $_POST['my_vcard_plugin_hidden_field'] == 'Y') {
        // Verify nonce
        if (!isset($_POST['my_vcard_plugin_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['my_vcard_plugin_nonce'])), 'my_vcard_plugin_settings')) {
            wp_die(esc_html__('Security check failed', 'floating-qr-code-vcard'));
        }

        // Récupérer et nettoyer les données du formulaire
        $fields = [
            'company',
            'last_name',
            'first_name',
            'mobile_phone',
            'landline_phone',
            'website',
            'email',
            'address',
            'postal_code',
            'city',
            'job_title',
            'button_position',
            'qr_text'
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_option('my_vcard_plugin_' . $field, sanitize_text_field(wp_unslash($_POST[$field])));
            }
        }

        echo '<div class="updated"><p><strong>' . esc_html__('Settings saved.', 'floating-qr-code-vcard') . '</strong></p></div>';
    }

    // Récupérer les valeurs enregistrées
    $fields = [
        'company',
        'last_name',
        'first_name',
        'mobile_phone',
        'landline_phone',
        'website',
        'email',
        'address',
        'postal_code',
        'city',
        'job_title',
        'button_position',
        'qr_text'
    ];
    $values = [];
    foreach ($fields as $field) {
        $values[$field] = get_option('my_vcard_plugin_' . $field, '');
    }

    // Afficher le formulaire
?>
    <div class="wrap">
        <h1><?php esc_html_e('VCard Settings', 'floating-qr-code-vcard'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('my_vcard_plugin_settings', 'my_vcard_plugin_nonce'); ?>
            <input type="hidden" name="my_vcard_plugin_hidden_field" value="Y">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Company', 'floating-qr-code-vcard'); ?></th>
                    <td><input type="text" name="company" value="<?php echo esc_attr($values['company']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Nom</th>
                    <td><input type="text" name="last_name" value="<?php echo esc_attr($values['last_name']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Prénom</th>
                    <td><input type="text" name="first_name" value="<?php echo esc_attr($values['first_name']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Téléphone portable</th>
                    <td><input type="text" name="mobile_phone" value="<?php echo esc_attr($values['mobile_phone']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Téléphone fixe</th>
                    <td><input type="text" name="landline_phone" value="<?php echo esc_attr($values['landline_phone']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Site internet</th>
                    <td><input type="text" name="website" value="<?php echo esc_attr($values['website']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Email</th>
                    <td><input type="email" name="email" value="<?php echo esc_attr($values['email']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Adresse</th>
                    <td><input type="text" name="address" value="<?php echo esc_attr($values['address']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Code postal</th>
                    <td><input type="text" name="postal_code" value="<?php echo esc_attr($values['postal_code']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Ville</th>
                    <td><input type="text" name="city" value="<?php echo esc_attr($values['city']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Titre du poste</th>
                    <td><input type="text" name="job_title" value="<?php echo esc_attr($values['job_title']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Button Position', 'floating-qr-code-vcard'); ?></th>
                    <td>
                        <select name="button_position">
                            <option value="bottom-left" <?php selected($values['button_position'], 'bottom-left'); ?>><?php _e('Bottom Left', 'floating-qr-code-vcard'); ?></option>
                            <option value="bottom-center" <?php selected($values['button_position'], 'bottom-center'); ?>><?php _e('Bottom Center', 'floating-qr-code-vcard'); ?></option>
                            <option value="bottom-right" <?php selected($values['button_position'], 'bottom-right'); ?>><?php _e('Bottom Right', 'floating-qr-code-vcard'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Texte du QR code</th>
                    <td>
                        <input type="text" name="qr_text" value="<?php echo esc_attr(get_option('my_vcard_plugin_qr_text', 'Scannez pour enregistrer le contact')); ?>" class="regular-text">
                    </td>
                </tr>
            </table>
            <?php submit_button('Enregistrer les paramètres'); ?>
        </form>
    </div>
<?php
}

function my_vcard_plugin_generate_vcf()
{
    $fields = [
        'company',
        'last_name',
        'first_name',
        'mobile_phone',
        'landline_phone',
        'website',
        'email',
        'address',
        'postal_code',
        'city',
        'job_title'
    ];

    $values = [];
    foreach ($fields as $field) {
        $values[$field] = get_option('my_vcard_plugin_' . $field, '');
    }

    $vcf_content = "BEGIN:VCARD\r\n";
    $vcf_content .= "VERSION:3.0\r\n";
    $vcf_content .= "N:{$values['last_name']};{$values['first_name']}\r\n";
    $vcf_content .= "FN:{$values['first_name']} {$values['last_name']}\r\n";
    $vcf_content .= "ORG:{$values['company']}\r\n";
    $vcf_content .= "TITLE:{$values['job_title']}\r\n";
    if (!empty($values['mobile_phone'])) {
        $vcf_content .= "TEL;TYPE=CELL:{$values['mobile_phone']}\r\n";
    }
    if (!empty($values['landline_phone'])) {
        $vcf_content .= "TEL;TYPE=WORK:{$values['landline_phone']}\r\n";
    }
    if (!empty($values['email'])) {
        $vcf_content .= "EMAIL;TYPE=INTERNET:{$values['email']}\r\n";
    }
    if (!empty($values['website'])) {
        $vcf_content .= "URL:{$values['website']}\r\n";
    }
    if (!empty($values['address']) || !empty($values['postal_code']) || !empty($values['city'])) {
        $address = "{$values['address']};{$values['postal_code']};{$values['city']}";
        $vcf_content .= "ADR;TYPE=WORK:;;{$address};;;;\r\n";
    }
    $vcf_content .= "END:VCARD\r\n";

    return $vcf_content;
}

add_action('init', 'my_vcard_plugin_add_endpoint');

function my_vcard_plugin_add_endpoint()
{
    add_rewrite_rule('^vcard/download/?$', 'index.php?my_vcard_plugin_action=download_vcf', 'top');
    // Flush rewrite rules only once when needed
    if (get_option('my_vcard_plugin_flush_rewrite') != true) {
        flush_rewrite_rules(false);
        update_option('my_vcard_plugin_flush_rewrite', true);
    }
}

add_filter('query_vars', 'my_vcard_plugin_query_vars');

function my_vcard_plugin_query_vars($vars)
{
    $vars[] = 'my_vcard_plugin_action';
    return $vars;
}

add_action('parse_request', 'my_vcard_plugin_parse_request');

function my_vcard_plugin_parse_request($wp)
{
    if (array_key_exists('my_vcard_plugin_action', $wp->query_vars) && $wp->query_vars['my_vcard_plugin_action'] == 'download_vcf') {
        header('Content-Type: text/vcard; charset=utf-8');
        header('Content-Disposition: attachment; filename="contact.vcf"');
        echo my_vcard_plugin_generate_vcf();
        exit();
    }
}

function my_vcard_plugin_get_qr_code_url()
{
    // Use the new endpoint URL
    $vcf_url = home_url('/vcard/download/');
    $vcf_url = rawurlencode($vcf_url);
    return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$vcf_url}";
}

add_action('wp_footer', 'my_vcard_plugin_display_button');

function my_vcard_plugin_display_button()
{
    $position = get_option('my_vcard_plugin_button_position', 'bottom-right');
    $qr_text = get_option('my_vcard_plugin_qr_text', 'Scannez pour enregistrer le contact');

    $positions_css = [
        'bottom-left' => 'left: 20px; bottom: 20px;',
        'bottom-center' => 'left: 50%; transform: translateX(-50%); bottom: 20px;',
        'bottom-right' => 'right: 20px; bottom: 20px;',
    ];
    $css_position = isset($positions_css[$position]) ? $positions_css[$position] : $positions_css['bottom-right'];
    $qr_code_url = my_vcard_plugin_get_qr_code_url();
?>
    <style>
        #floating-qr-code-vcard-button {
            position: fixed;
            <?php echo esc_html($css_position); ?>;
            z-index: 9999;
            cursor: pointer;
            background: white;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
        }

        #floating-qr-code-vcard-button img {
            width: 30px;
            height: 30px;
            display: block;
        }

        @media (max-width: 768px) {
            #floating-qr-code-vcard-button {
                width: 40px;
                height: 40px;
            }

            #floating-qr-code-vcard-button img {
                width: 24px;
                height: 24px;
            }
        }

        #floating-qr-code-vcard-qr {
            display: none;
            position: fixed;
            <?php echo esc_attr($css_position); ?>z-index: 9998;
            margin-bottom: 70px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        #floating-qr-code-vcard-qr img {
            width: 200px;
            height: 200px;
            margin-bottom: 10px;
        }

        #floating-qr-code-vcard-qr p {
            margin: 0;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }

        @media (max-width: 767px) {
            #floating-qr-code-vcard-qr {
                left: 50% !important;
                transform: translateX(-50%);
                bottom: 90px;
                right: auto;
            }
        }
    </style>
    <div id="floating-qr-code-vcard-button">
        <img src="<?php echo esc_url($qr_code_url); ?>" alt="QR Code">
    </div>
    <div id="floating-qr-code-vcard-qr">
        <img src="<?php echo esc_url($qr_code_url); ?>" alt="QR Code">
        <p><?php echo esc_html($qr_text); ?></p>
    </div>
    <script>
        (function($) {
            $('#floating-qr-code-vcard-button, #floating-qr-code-vcard-qr').on('click', function() {
                <?php if (wp_is_mobile()) : ?>
                    // On mobile, redirect to the .vcf file
                    window.location.href = '<?php echo esc_url(home_url('/vcard/download/')); ?>';
                <?php else : ?>
                    // On desktop, toggle QR code visibility and handle click
                    if ($(this).attr('id') === 'floating-qr-code-vcard-button') {
                        $('#floating-qr-code-vcard-qr').toggle();
                    } else if ($(this).attr('id') === 'floating-qr-code-vcard-qr') {
                        window.location.href = '<?php echo esc_url(home_url('/vcard/download/')); ?>';
                    }
                <?php endif; ?>
            });
        })(jQuery);
    </script>
<?php
}

add_action('wp_enqueue_scripts', 'my_vcard_plugin_enqueue_scripts');

function my_vcard_plugin_enqueue_scripts()
{
    wp_enqueue_script('jquery');
}

// Add this to handle plugin activation
register_activation_hook(__FILE__, 'my_vcard_plugin_activate');
function my_vcard_plugin_activate()
{
    // This will force the rewrite rules to be regenerated on activation
    delete_option('my_vcard_plugin_flush_rewrite');
}

// Add translation loading
add_action('plugins_loaded', 'my_vcard_plugin_load_textdomain');
function my_vcard_plugin_load_textdomain()
{
    load_plugin_textdomain('floating-qr-code-vcard', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
?>