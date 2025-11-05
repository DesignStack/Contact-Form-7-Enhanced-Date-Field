<?php
/**
 * Plugin Name: Contact Form 7 Enhanced Date Field
 * Plugin URI: https://designstack.co.uk
 * Description: Adds a powerful, mobile-friendly enhanced date field to Contact Form 7 with advanced restrictions and customization options.
 * Version: 1.0.0
 * Author: DesignStack
 * Author URI: https://designstack.co.uk
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cf7-enhanced-date-field
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CF7_EDF_VERSION', '1.0.0');
define('CF7_EDF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CF7_EDF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CF7_EDF_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class CF7_Enhanced_Date_Field {

    /**
     * Instance of this class
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Check if Contact Form 7 is active
        if (!class_exists('WPCF7')) {
            add_action('admin_notices', array($this, 'cf7_missing_notice'));
            return;
        }

        // Load text domain
        load_plugin_textdomain('cf7-enhanced-date-field', false, dirname(CF7_EDF_PLUGIN_BASENAME) . '/languages');

        // Register form tag
        add_action('wpcf7_init', array($this, 'register_form_tag'));

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Add tag generator button
        add_action('wpcf7_admin_init', array($this, 'add_tag_generator'), 25);

        // Add validation filter
        add_filter('wpcf7_validate_date-enhanced', array($this, 'validate_date_field'), 10, 2);
        add_filter('wpcf7_validate_date-enhanced*', array($this, 'validate_date_field'), 10, 2);
    }

    /**
     * Notice if Contact Form 7 is not active
     */
    public function cf7_missing_notice() {
        $message = sprintf(
            /* translators: %s: Contact Form 7 plugin name */
            esc_html__('Contact Form 7 Enhanced Date Field requires %s to be installed and activated.', 'cf7-enhanced-date-field'),
            '<strong>' . esc_html__('Contact Form 7', 'cf7-enhanced-date-field') . '</strong>'
        );

        echo '<div class="notice notice-error"><p>' . $message . '</p></div>';
    }

    /**
     * Register the date-enhanced form tag
     */
    public function register_form_tag() {
        wpcf7_add_form_tag(
            array('date-enhanced', 'date-enhanced*'),
            array($this, 'form_tag_handler'),
            array(
                'name-attr' => true,
                'do-not-store' => false,
            )
        );
    }

    /**
     * Form tag handler
     */
    public function form_tag_handler($tag) {
        if (empty($tag->name)) {
            return '';
        }

        $validation_error = wpcf7_get_validation_error($tag->name);

        $class = wpcf7_form_controls_class($tag->type);
        $class .= ' wpcf7-date-enhanced';

        if ($validation_error) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = array();
        $atts['class'] = $tag->get_class_option($class);
        $atts['id'] = $tag->get_id_option();
        $atts['type'] = 'text';
        $atts['name'] = $tag->name;
        $atts['readonly'] = 'readonly';

        $value = (string) reset($tag->values);
        if ($tag->has_option('placeholder') || $value !== '') {
            $atts['placeholder'] = $value;
        }

        if ($tag->is_required()) {
            $atts['aria-required'] = 'true';
            $atts['required'] = 'required';
        }

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        // Get configuration options
        $config = $this->get_tag_config($tag);
        $atts['data-config'] = esc_attr(json_encode($config));

        $atts = wpcf7_format_atts($atts);

        $html = sprintf(
            '<span class="wpcf7-form-control-wrap" data-name="%1$s"><input %2$s />%3$s</span>',
            esc_attr($tag->name),
            $atts,
            $validation_error
        );

        return $html;
    }

    /**
     * Get configuration from tag options
     */
    private function get_tag_config($tag) {
        $config = array(
            'dateFormat' => 'Y-m-d',
            'minDate' => null,
            'maxDate' => null,
            'disable' => array(),
            'linkedTo' => null,
            'mode' => 'single'
        );

        // Date format
        if ($tag->has_option('date-format')) {
            $format = $tag->get_option('date-format', '', true);
            if ($format) {
                $config['dateFormat'] = $this->convert_date_format($format);
            }
        }

        // Min date
        if ($tag->has_option('min-date')) {
            $min_date = $tag->get_option('min-date', '', true);
            if ($min_date) {
                $config['minDate'] = $this->parse_date_value($min_date);
            }
        }

        // Max date
        if ($tag->has_option('max-date')) {
            $max_date = $tag->get_option('max-date', '', true);
            if ($max_date) {
                $config['maxDate'] = $this->parse_date_value($max_date);
            }
        }

        // Exclude days of week (0 = Sunday, 6 = Saturday)
        if ($tag->has_option('exclude-days')) {
            $exclude_days = $tag->get_option('exclude-days', '', true);
            if ($exclude_days) {
                $days = array_map('trim', explode(',', $exclude_days));
                $day_numbers = array();

                foreach ($days as $day) {
                    // Support both day names and numbers
                    $day_map = array(
                        'sunday' => 0, 'sun' => 0, '0' => 0,
                        'monday' => 1, 'mon' => 1, '1' => 1,
                        'tuesday' => 2, 'tue' => 2, '2' => 2,
                        'wednesday' => 3, 'wed' => 3, '3' => 3,
                        'thursday' => 4, 'thu' => 4, '4' => 4,
                        'friday' => 5, 'fri' => 5, '5' => 5,
                        'saturday' => 6, 'sat' => 6, '6' => 6,
                    );

                    $day_lower = strtolower($day);
                    if (isset($day_map[$day_lower])) {
                        $day_numbers[] = $day_map[$day_lower];
                    }
                }

                if (!empty($day_numbers)) {
                    $config['disable'] = array(
                        array(
                            'function' => 'disableDays',
                            'days' => $day_numbers
                        )
                    );
                }
            }
        }

        // Exclude date ranges (expects dd-mm-yyyy format)
        if ($tag->has_option('exclude-dates')) {
            $exclude_dates = $tag->get_option('exclude-dates', '', true);
            if ($exclude_dates) {
                $dates = array_map('trim', explode(',', $exclude_dates));
                if (!isset($config['disable']) || !is_array($config['disable'])) {
                    $config['disable'] = array();
                }
                foreach ($dates as $date) {
                    // Support both single dates and ranges (e.g., "01-12-2025" or "01-12-2025 to 05-12-2025")
                    if (strpos($date, ' to ') !== false) {
                        $range = array_map('trim', explode(' to ', $date));
                        if (count($range) === 2) {
                            // Convert from dd-mm-yyyy to Y-m-d format
                            $from = $this->convert_date_to_flatpickr_format($range[0]);
                            $to = $this->convert_date_to_flatpickr_format($range[1]);
                            if ($from && $to) {
                                $config['disable'][] = array('from' => $from, 'to' => $to);
                            }
                        }
                    } else {
                        // Convert single date from dd-mm-yyyy to Y-m-d format
                        $converted = $this->convert_date_to_flatpickr_format($date);
                        if ($converted) {
                            $config['disable'][] = $converted;
                        }
                    }
                }
            }
        }

        // Linked date field (for dependent date pickers)
        if ($tag->has_option('linked-to')) {
            $linked_to = $tag->get_option('linked-to', '', true);
            if ($linked_to) {
                $config['linkedTo'] = $linked_to;
            }
        }

        return $config;
    }

    /**
     * Convert date format from PHP to flatpickr format
     */
    private function convert_date_format($format) {
        // Common format mappings
        $format_map = array(
            'mm/dd/yyyy' => 'm/d/Y',
            'dd/mm/yyyy' => 'd/m/Y',
            'yyyy-mm-dd' => 'Y-m-d',
            'dd-mm-yyyy' => 'd-m-Y',
            'mm-dd-yyyy' => 'm-d-Y',
            'dd.mm.yyyy' => 'd.m.Y',
            'mm.dd.yyyy' => 'm.d.Y',
        );

        $format_lower = strtolower($format);
        if (isset($format_map[$format_lower])) {
            return $format_map[$format_lower];
        }

        return $format;
    }

    /**
     * Parse date value (support relative dates)
     */
    private function parse_date_value($value) {
        // If it starts with + or -, it's a relative date
        if (preg_match('/^[+-]/', $value)) {
            return $value;
        }

        // Check for special keywords
        $keywords = array('today', 'now', 'tomorrow', 'yesterday');
        if (in_array(strtolower($value), $keywords)) {
            return $value;
        }

        // Return as is (should be a valid date string)
        return $value;
    }

    /**
     * Convert date from dd-mm-yyyy format to Y-m-d format for flatpickr
     */
    private function convert_date_to_flatpickr_format($date) {
        // Check if date is in dd-mm-yyyy format
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $date, $matches)) {
            // Convert dd-mm-yyyy to Y-m-d
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }

        // If it's already in another format or invalid, return as is
        return $date;
    }

    /**
     * Validate date field
     */
    public function validate_date_field($result, $tag) {
        $name = $tag->name;
        $value = isset($_POST[$name]) ? trim($_POST[$name]) : '';

        if ($tag->is_required() && empty($value)) {
            $result->invalidate($tag, wpcf7_get_message('invalid_required'));
            return $result;
        }

        if (!empty($value)) {
            // Validate date format
            $config = $this->get_tag_config($tag);
            $date_format = $config['dateFormat'];

            // Convert flatpickr format to PHP DateTime format
            $php_format = str_replace(
                array('Y', 'm', 'd', 'M', 'D'),
                array('Y', 'm', 'd', 'M', 'D'),
                $date_format
            );

            $date = \DateTime::createFromFormat($php_format, $value);

            if (!$date || $date->format($php_format) !== $value) {
                $result->invalidate($tag, wpcf7_get_message('invalid_date'));
            }
        }

        return $result;
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Flatpickr CSS from CDN
        wp_enqueue_style(
            'flatpickr',
            'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css',
            array(),
            '4.6.13'
        );

        // Custom CSS
        wp_enqueue_style(
            'cf7-enhanced-date-field',
            CF7_EDF_PLUGIN_URL . 'assets/css/cf7-enhanced-date-field.css',
            array('flatpickr'),
            CF7_EDF_VERSION
        );

        // Flatpickr JS from CDN
        wp_enqueue_script(
            'flatpickr',
            'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js',
            array(),
            '4.6.13',
            true
        );

        // Custom JS
        wp_enqueue_script(
            'cf7-enhanced-date-field',
            CF7_EDF_PLUGIN_URL . 'assets/js/cf7-enhanced-date-field.js',
            array('jquery', 'flatpickr'),
            CF7_EDF_VERSION,
            true
        );

        // Localize script
        wp_localize_script('cf7-enhanced-date-field', 'cf7EdfConfig', array(
            'dateFormat' => get_option('date_format', 'Y-m-d'),
        ));
    }

    /**
     * Add tag generator button in CF7 admin
     */
    public function add_tag_generator() {
        if (class_exists('WPCF7_TagGenerator')) {
            $tag_generator = WPCF7_TagGenerator::get_instance();
            $tag_generator->add(
                'date-enhanced',
                __('Enhanced Date', 'cf7-enhanced-date-field'),
                array($this, 'tag_generator_dialog')
            );
        }
    }

    /**
     * Tag generator dialog
     */
    public function tag_generator_dialog($contact_form, $args = '') {
        $args = wp_parse_args($args, array());
        $type = 'date-enhanced';

        $description = __("Generate a form-tag for an enhanced date field. For more details, see %s.", 'cf7-enhanced-date-field');
        $desc_link = wpcf7_link(__('https://designstack.co.uk', 'cf7-enhanced-date-field'), __('Enhanced Date Field', 'cf7-enhanced-date-field'));
        ?>
        <div class="control-box">
            <fieldset>
                <legend><?php echo sprintf(esc_html($description), $desc_link); ?></legend>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-name'); ?>">
                                    <?php echo esc_html__('Name', 'cf7-enhanced-date-field'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'] . '-name'); ?>" />
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-values'); ?>">
                                    <?php echo esc_html__('Default value / Placeholder', 'cf7-enhanced-date-field'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="values" class="oneline" id="<?php echo esc_attr($args['content'] . '-values'); ?>" placeholder="Select a date..." />
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php echo esc_html__('Date Format', 'cf7-enhanced-date-field'); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php echo esc_html__('Date Format', 'cf7-enhanced-date-field'); ?></legend>
                                    <label>
                                        <input type="text" name="date-format" class="option" placeholder="Y-m-d" />
                                        <span class="description"><?php echo esc_html__('e.g., Y-m-d, m/d/Y, d/m/Y', 'cf7-enhanced-date-field'); ?></span>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php echo esc_html__('Minimum Date', 'cf7-enhanced-date-field'); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php echo esc_html__('Minimum Date', 'cf7-enhanced-date-field'); ?></legend>
                                    <label>
                                        <input type="text" name="min-date" class="option" placeholder="today" />
                                        <span class="description"><?php echo esc_html__('e.g., today, +1 week, +2 months, 2025-01-01', 'cf7-enhanced-date-field'); ?></span>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php echo esc_html__('Maximum Date', 'cf7-enhanced-date-field'); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php echo esc_html__('Maximum Date', 'cf7-enhanced-date-field'); ?></legend>
                                    <label>
                                        <input type="text" name="max-date" class="option" placeholder="+1 year" />
                                        <span class="description"><?php echo esc_html__('e.g., +1 month, +1 year, 2025-12-31', 'cf7-enhanced-date-field'); ?></span>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php echo esc_html__('Exclude Days', 'cf7-enhanced-date-field'); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php echo esc_html__('Exclude Days', 'cf7-enhanced-date-field'); ?></legend>
                                    <label>
                                        <input type="text" name="exclude-days" class="option" placeholder="0,6" />
                                        <span class="description"><?php echo esc_html__('Comma-separated. 0=Sunday, 1=Monday, ..., 6=Saturday. e.g., "0,6" or "saturday,sunday"', 'cf7-enhanced-date-field'); ?></span>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php echo esc_html__('Exclude Dates', 'cf7-enhanced-date-field'); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php echo esc_html__('Exclude Dates', 'cf7-enhanced-date-field'); ?></legend>
                                    <label>
                                        <input type="text" name="exclude-dates" class="option" placeholder="2025-01-01, 2025-12-25" />
                                        <span class="description"><?php echo esc_html__('Comma-separated dates or ranges. e.g., "2025-01-01" or "2025-01-01 to 2025-01-05"', 'cf7-enhanced-date-field'); ?></span>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php echo esc_html__('Linked To (For End Date)', 'cf7-enhanced-date-field'); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php echo esc_html__('Linked To', 'cf7-enhanced-date-field'); ?></legend>
                                    <label>
                                        <input type="text" name="linked-to" class="option" placeholder="start-date" />
                                        <span class="description"><?php echo esc_html__('Name of the start date field. This makes current field an end date that must be after the start date.', 'cf7-enhanced-date-field'); ?></span>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php echo esc_html__('Field Options', 'cf7-enhanced-date-field'); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php echo esc_html__('Field Options', 'cf7-enhanced-date-field'); ?></legend>
                                    <label>
                                        <input type="checkbox" name="required" class="option" />
                                        <?php echo esc_html__('Required field', 'cf7-enhanced-date-field'); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-id'); ?>">
                                    <?php echo esc_html__('Id attribute', 'cf7-enhanced-date-field'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr($args['content'] . '-id'); ?>" />
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($args['content'] . '-class'); ?>">
                                    <?php echo esc_html__('Class attribute', 'cf7-enhanced-date-field'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr($args['content'] . '-class'); ?>" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr__('Insert Tag', 'cf7-enhanced-date-field'); ?>" />
            </div>
        </div>
        <?php
    }
}

// Initialize the plugin
function cf7_enhanced_date_field() {
    return CF7_Enhanced_Date_Field::get_instance();
}

// Start the plugin
cf7_enhanced_date_field();
