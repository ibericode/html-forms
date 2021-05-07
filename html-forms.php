<?php
/*
Plugin Name: HTML Forms
Plugin URI: https://www.htmlformsplugin.com/#utm_source=wp-plugin&utm_medium=html-forms&utm_campaign=plugins-page
Description: Not just another forms plugin. Simple and flexible.
Version: 1.3.20
Author: ibericode
Author URI: https://ibericode.com/
License: GPL v3
Text Domain: html-forms

HTML Forms
Copyright (C) 2017-2021, Danny van Kooten, danny@ibericode.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace HTML_Forms;

use wpdb;

function _bootstrap() {
    $settings = hf_get_settings();

    $forms = new Forms( __FILE__, $settings );
    $forms->hook();

    // hook actions
    $email_action = new Actions\Email();
    $email_action->hook();

    if( class_exists( 'MC4WP_MailChimp' ) ) {
        $mailchimp_action = new Actions\MailChimp();
        $mailchimp_action->hook();
    }

    if( is_admin() ) {
        if( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
            $admin = new Admin\Admin( __FILE__ );
            $admin->hook();
        }

        $gdpr = new Admin\GDPR();
        $gdpr->hook();
    }
}

function _install_mu_wrapper() {
    if ( ! is_multisite() ) // not a multisite install, skip multisite setup
        return _install();

    $added_caps = [];

    foreach ( get_sites(['number' => PHP_INT_MAX]) as $site ) {
        switch_to_blog((int) $site->blog_id);

        // install table for current blog
        _install_table();

        // iterate through current blog admins
        foreach ( get_users(['blog_id' => (int) $site->blog_id, 'role' => 'administrator', 'fields' => 'ID']) as $admin_id ) {
            if ( ! (int) $admin_id || in_array($admin_id, $added_caps) )
                continue;

            // add "edit_forms" cap to site admin
            $user = new \WP_User( (int) $admin_id );
            $user->add_cap('edit_forms', true);

            $added_caps []= $admin_id;
        }

        restore_current_blog();
    }
}

// install table for main site on regular installs, or active site for multisite
function _install_table() {
    /** @var wpdb */
    global $wpdb;

    // create table for storing submissions
    $table = $wpdb->prefix . 'hf_submissions';
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$table}(
        `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `form_id` INT UNSIGNED NOT NULL,
        `data` TEXT NOT NULL,
        `user_agent` TEXT NULL,
        `ip_address` VARCHAR(255) NULL,
        `referer_url` TEXT NULL,
        `submitted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB CHARACTER SET={$wpdb->charset};");
}

// runs on regular wordpress installs post plugin activation
function _install() {
    // install table for regular wp install
    _install_table();

    // add "edit_forms" cap to user that activated the plugin
    $user = wp_get_current_user();
    $user->add_cap('edit_forms', true);
}

function hf_add_user_to_blog($user_id, $role, $blog_id) {
    if ( 'administrator' != $role )
        return;

    // add "edit_forms" cap to site admin
    $user = new \WP_User( (int) $user_id );
    $user->add_cap('edit_forms', true);
}

function hf_wp_insert_site( \WP_Site $site ) {
    switch_to_blog((int) $site->blog_id);

    // install table
    _install_table();

    restore_current_blog();
}

define('HTML_FORMS_VERSION', '1.3.20');

if( ! function_exists( 'hf_get_form' ) ) {
    require __DIR__ . '/vendor/autoload.php';
}

register_activation_hook( __FILE__, 'HTML_Forms\\_install_mu_wrapper');
add_action( 'plugins_loaded', 'HTML_Forms\\_bootstrap', 10 );

// add cap to site admin after being added to blog
add_action('add_user_to_blog', 'HTML_Forms\\hf_add_user_to_blog', 10, 3);

// install db table for newly added sites
add_action('wp_insert_site', 'HTML_Forms\\hf_wp_insert_site');
