<?php
// Plugin Name: Breakdance OpenRouterAI Settings
// Plugin URI: https://tools4wp.com
// Description: Power your website with Open Router AI.
// Version: 1.0.0
// Author: Tools4WP
// License: GPLv2 or later
// Requires PHP: 8.0

function display_missing_dependency_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <b>Breakdance AI:</b> Breakdance AI must be installed.
        </p>
    </div>
    <?php
}

add_action('plugins_loaded', function () {
    if (!defined('BREAKDANCE_AI_VERSION')) {
        add_action('admin_notices', 'display_missing_dependency_notice');
    }
});

function override_breakdance_ai_endpoint($url) {
    return 'https://openrouter.ai/api';
}
add_filter('breakdance_ai_api_endpoint', 'override_breakdance_ai_endpoint');

function override_breakdance_ai_model($model_version, $model) {
    return get_option('breakdance_openrouter_ai_model', 'mistralai/pixtral-12b:free');
}
add_filter('breakdance_ai_model', 'override_breakdance_ai_model', 10, 2);

add_action('breakdance_register_admin_settings_page_register_tabs', 'register');

function register() {
    \Breakdance\Admin\SettingsPage\addTab(
        'OpenRouter AI Assistant',
        'openrouterai',
        'tab',
        5001
    );
}

function tab() {
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        check_admin_referer('openrouter_ai_nonce'); // Ensure the nonce is valid
        $model_version = sanitize_text_field($_POST['model_version']);
        update_option('breakdance_openrouter_ai_model', $model_version);
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    // Get the current model version from the database
    $current_model_version = get_option('breakdance_openrouter_ai_model', 'mistralai/pixtral-12b:free');
    ?>
    <h2>OpenRouter AI Assistant</h2>

    <form action="" method="post">
        <?php wp_nonce_field('openrouter_ai_nonce'); ?>

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        OpenRouter Model
                    </th>

                    <td>
                        <input name="model_version" type="text" id="breakdance_openrouter_ai_model" value="<?php echo esc_attr($current_model_version); ?>" class="regular-text" autocomplete="off" />
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>
    </form>
<?php
}
