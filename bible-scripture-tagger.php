<?php
/**
 * Plugin Name:       BibleScriptureTagger
 * Description:       Create a hover for Bible references. It reveals the verse text and provides a link for further study at the Bible Portal.
 * Version:           1.0.1
 * Author:            Bible Portal
 * Author URI:        https://bibleportal.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');

function bible_scripture_tagger_supported_versions(): array
{
    return [
        [
            "language" => "English",
            "lang" => "en",
            "title" => "English Standard Version",
            "abbr" => "ESV"
        ],
        [
            "language" => "English",
            "lang" => "en",
            "title" => "King James Version",
            "abbr" => "KJV"
        ],
        [
            "language" => "English",
            "lang" => "en",
            "title" => "New International Version",
            "abbr" => "NIV"
        ],
        [
            "language" => "English",
            "lang" => "en",
            "title" => "American Standard Version",
            "abbr" => "ASV"
        ],
        [
            "language" => "English",
            "lang" => "en",
            "title" => "Berean Study Bible",
            "abbr" => "BSB"
        ],
        [
            "language" => "English",
            "lang" => "en",
            "title" => "New American Standard Bible",
            "abbr" => "NASB"
        ],
        [
            "language" => "English",
            "lang" => "en",
            "title" => "New American Standard Bible 1995",
            "abbr" => "NASB1995"
        ],
        [
            "language" => "English",
            "lang" => "en",
            "title" => "New International Version 1984",
            "abbr" => "NIV1984"
        ],
        [
            "language" => "English",
            "lang" => "en",
            "title" => "New King James Version",
            "abbr" => "NKJV"
        ],
        [
            "language" => "English",
            "lang" => "en",
            "title" => "New Living Translation",
            "abbr" => "NLT"
        ],
        [
            "language" => "English",
            "lang" => "en",
            "title" => "World English Bible",
            "abbr" => "WEB"
        ]
    ];
}

function bible_scripture_tagger_options_admin()
{
    ?>
    <div class="wrap">
        <h2>BibleScriptureTagger Options</h2>
        <?php

        // If the user clicked submit, update the preferences
        if (bible_scripture_tagger_get_option_value($_REQUEST, 'submit')) {
            bible_scripture_tagger_options_update();
        }

        // Print the options page
        bible_scripture_tagger_options_page();

        ?>
    </div>
    <?php
}

function bible_scripture_tagger_options_page()
{
    $bp_bible_version = get_option('bp_bible_version');
    ?>
    <form method="post">
        <?php wp_nonce_field('bible_scripture_tagger_submit_options'); ?>
        <table class="form-table">
            <tr style="vertical-align:top">
                <th scope="row">Bible version:</th>
                <td>
                    <select name="bp_bible_version">
                        <?php
                        foreach (bible_scripture_tagger_supported_versions() as $s_version) {
                            $abbr = $s_version['abbr'];
                            $title = $s_version['title'];
                            echo '<option value="' . esc_attr($abbr) . '" ' . ($abbr == $bp_bible_version ? esc_attr('selected=SELECTED ') : "") . '>' . esc_html($title) . ' (' . esc_html($abbr) . ')' . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="submit" value="Save Changes"/>
        </p>
    </form>
    <?php
}

function bible_scripture_tagger_get_option_value($option_object, $index)
{
    return isset($option_object[$index]) ? sanitize_text_field($option_object[$index]) : 0;
}

function bible_scripture_tagger_options_update()
{
    if (!check_admin_referer('bible_scripture_tagger_submit_options') || !current_user_can('manage_options')) {
        die("Authorization failed.");
    }

    $changed = false;
    if (bible_scripture_tagger_get_option_value($_REQUEST, 'bp_bible_version')) {
        $new_bible_version = sanitize_option('bp_bible_version', bible_scripture_tagger_get_option_value($_REQUEST, 'bp_bible_version'));
        update_option('bp_bible_version', $new_bible_version);
        $changed = true;
    }

    if ($changed) {
        ?>
        <div id="message" class="updated fade">
            <p>Settings Saved.</p>
        </div>
        <?php
    }
}

function bible_scripture_tagger_load_options_menu()
{
    add_options_page('BibleScriptureTagger', 'BibleScriptureTagger', 'manage_options', __FILE__, 'bible_scripture_tagger_options_admin');
}

add_action('admin_menu', 'bible_scripture_tagger_load_options_menu');


// Set default options
function bible_scripture_tagger_activate()
{
    add_option('bp_bible_version', 'ESV');
}

// Unset default options
function bible_scripture_tagger_deactivate()
{
    delete_option('bp_bible_version');
}

/**
 * Activation and Deactivation Hooks
 */
register_activation_hook(__FILE__, 'bible_scripture_tagger_activate');
register_deactivation_hook(__FILE__, 'bible_scripture_tagger_deactivate');

function bible_scripture_tagger_loader()
{
    $selected_bible_version = get_option('bp_bible_version');
    wp_enqueue_script("bp-scripture-tagger", "https://bibleportal.com/assets/scripts/bp-scripture-tagger-min.js");
    wp_add_inline_script("bp-scripture-tagger", "BP.ScriptureTagger.Config.Translation = '$selected_bible_version'");
}

add_action('wp_footer', 'bible_scripture_tagger_loader');

?>