<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://mediuminteractive.com/
 * @since      1.0.0
 *
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/admin/partials
 */

// Try to get the redirect URL from the database.
$is_enabled   = EasyCountrySpamBlocker_Helper::get_wp_option( 'mi-ecsb_is_enabled', true );
$countries    = EasyCountrySpamBlocker_Helper::get_wp_option( 'mi-ecsb_countries', [] );
$redirect_url = EasyCountrySpamBlocker_Helper::get_url();
$url_protocol = '';

// If the URL is not valid, do not use it.
if( $redirect_url === null )
{
    $redirect_url = '';
    $url_protocol = '';
}
else // Else, set the URL protocol.
{
    $url_protocol = EasyCountrySpamBlocker_Helper::get_url_protocol( $redirect_url );
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="ecsb-admin-page" class="ecsb-admin-page">
    <h2><?php _e( 'Easy Country Spam Blocker Settings', 'mi-ecsb' ); ?></h2>
    <p><?php _e( 'Easy Country Spam Blocker is a simple plugin that blocks all non-US traffic from a site. Choose a redirection URL below and click "Save."', 'mi-ecsb' ); ?></p>

    <div id="ecsb-notices"></div>

    <div class="ecsb-left">
        <!-- Settings Section 1 -->
        <div class="section">
            <div class="content">
                <form id="ecsb-settings-form" class="ecsb-settings-form" method="post">
                    <div class="ecsb-field">
                        <label class="ecsb-field-label" for="is_enabled">
                            <?php _e( 'ECSB Enabled?', 'mi-ecsb' ); ?>
                        </label>

                        <label class="switch">
                            <input name="isEnabled" type="checkbox" <?php if( $is_enabled ) echo 'checked'; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    
                    <div class="ecsb-field marginless-bottom">
                        <p class="ecsb-explanation">
                            <?php _e( 'What URL do you want to redirect non-US visitors to?', 'mi-ecsb' ); ?>
                        </p>
                    </div>
                    
                    <div class="ecsb-field url-select-container">
                        <?php for($i = 0; $i < 1; $i++): ?>
                            <div class="url-select-item">
                                <div class="url-protocol-dropdown">
                                    <button class="url-protocol-button" data-url-protocol="<?php echo esc_attr( $url_protocol ); ?>">
                                        <?php echo strtoupper( esc_html( $url_protocol ) ); ?>
                                    </button>
                                    
                                    <ul class="url-protocol-options initial">
                                        <?php for($k = 0; $k < count( EasyCountrySpamBlocker_Helper::get_allowed_url_protocols() ); $k++): ?>
                                            <li class="url-protocol-option" data-url-protocol="<?php echo esc_attr( EasyCountrySpamBlocker_Helper::get_allowed_url_protocols()[ $k ] ); ?>">
                                                <?php echo strtoupper( esc_html( EasyCountrySpamBlocker_Helper::get_allowed_url_protocols()[ $k ] ) ); ?>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </div>

                                <input class="url-text" name="redirectUrl" type="text" value="<?php echo EasyCountrySpamBlocker_Helper::strip_url_protocol( esc_url( $redirect_url ) ); ?>">
                            </div>
                        <?php endfor; ?>

                        <input type="hidden" name="url_protocol" value="<?php echo esc_attr( $url_protocol ); ?>">
                    </div>
                    
                    <div class="ecsb-field">
                        <input type="hidden" name="action" value="ecsb_settings">
                    </div>
                    
                    <div class="ecsb-field">
                        <input class="button button-primary button-large save-button" type="submit" value="<?php echo _e( 'Save', 'mi-ecsb' ); ?>">
                    </div>
                </form>
            </div>
        </div>

        <!-- Question Section -->
        <div style="background-color: white; padding: 10px; margin-top: 10px; margin-right: 10px;">
            <h2><?php _e( 'Questions?', 'mi-ecsb' ); ?></h2>
            <p><?php _e( 'If you are having issues with Easy Country Spam Blocker or just have a general question to ask,<br>feel free to reach out anytime!', 'mi-ecsb' ); ?></p>
            <a class="button button-primary button-large" href="https://www.mediuminteractive.com/contact/" target="_blank" rel="nofollow"><?php _e( 'Contact Us', 'mi-ecsb' ); ?></a>
        </div>

        <div style="background-color: white; padding: 10px; margin-top: 10px; margin-right: 10px;">
            <h2><?php _e( 'Quickstart', 'mi-ecsb' ); ?>
            <ul style="list-style-type: circle; margin-left: 25px; font-weight: normal; font-size: 1rem;">
                <li>
                    <?php _e( 'Install & Activate', 'mi-ecsb' ); ?>
                </li>
                <li>
                    <?php _e( 'Enable whitelisted countries' ); ?>
                </li>
                <li>
                    <?php _e( 'Set redirect URL above', 'mi-ecsb' ); ?>
                </li>
                <li>
                    <?php _e( 'Choose either HTTP or HTTPS', 'mi-ecsb' ); ?>
                </li>
                <li>
                    <?php _e( 'Click "Save"', 'mi-ecsb' ); ?>
                </li>
                <li>
                    <?php _e( 'Done!', 'mi-ecsb' ); ?>
                </li>
            </ul>
        </div>
    </div>

    <div class="ecsb-right">
        <!-- Settings Section 2 -->
        <div class="section">
            <div class="content">
                <h2 style="color: white"><?php _e( 'Whitelisted Countries', 'mi-ecsb' ); ?></h2>
                <div class="ecsb-field">
                    <input class="button button-primary button-large save-button" type="submit" value="<?php echo _e( 'Save', 'mi-ecsb' ); ?>">
                </div>

                <?php foreach( EasyCountrySpamBlocker_Helper::get_countries() as $key => $value ): ?>
                    <div class="country" style="width: 100%; display: block; overflow: hidden;">
                        <div style="display: inline-block: width: 5%; float: left;">
                            <?php if( strtolower( $value ) === 'us' ) { ?>
                                <input id="usa-toggle" class="country-toggle" value="<?php echo $value; ?>" type="checkbox" checked />
                            <?php } else { ?>
                                <input class="country-toggle" value="<?php echo $value; ?>" type="checkbox" <?php if( in_array( $value, $countries ) ) echo 'checked'; ?> />
                            <?php } ?>
                        </div>

                        <div style="display: inline-block; width: 95%; float: right;">
                            <?php if( strtolower( $value ) === 'us' ) { ?>
                                <?php _e( $key, 'mi-ecsb' ); ?> <strong><?php _e( '(ALWAYS ENABLED)', 'mi-ecsb' ); ?></strong>
                            <?php } else { ?>
                                <?php _e( $key, 'mi-ecsb' ); ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="ecsb-field">
                    <input class="button button-primary button-large save-button" type="submit" value="<?php echo _e( 'Save', 'mi-ecsb' ); ?>">
                </div>
            </div>
        </div>
    </div>
</div>
