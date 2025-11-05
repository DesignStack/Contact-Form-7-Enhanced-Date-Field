=== Contact Form 7 Enhanced Date Field ===
Contributors: designstack
Tags: contact form 7, date picker, date field, form, contact
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.2
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add a beautiful, mobile-friendly enhanced date field to Contact Form 7 with advanced restrictions and customization options.

== Description ==

Contact Form 7 Enhanced Date Field is a powerful addon that extends Contact Form 7 with a modern, responsive date picker featuring advanced date restrictions and beautiful styling.

= Key Features =

* **Beautiful & Modern Design** - Clean, professional date picker interface that looks great on all devices
* **Mobile-Friendly** - Optimized for touch devices with responsive design
* **Flexible Date Restrictions** - Set minimum and maximum dates with ease
* **Relative Dates** - Use dynamic dates like "today", "+1 week", "+2 months"
* **Exclude Days** - Disable specific days of the week (e.g., weekends)
* **Exclude Date Ranges** - Block specific dates or date ranges
* **Dependent Date Pickers** - Create start/end date pairs where the end date depends on the start date
* **Custom Date Formats** - Display dates in your preferred format (Y-m-d, m/d/Y, d/m/Y, etc.)
* **Dark Mode Support** - Automatically adapts to user's dark mode preference
* **Accessible** - Built with accessibility in mind
* **No jQuery Dependency** - Uses modern flatpickr library
* **Lightweight** - Minimal footprint with CDN-hosted libraries

= Use Cases =

* Event booking forms with date restrictions
* Hotel/accommodation reservation forms
* Appointment scheduling
* Project deadline submissions
* Date range selection for reports
* Any form requiring date input with validation

= Improved User Experience =

By implementing this enhanced date field in your Contact Form 7, you enhance the user experience. Users will only see and be able to select valid and relevant dates, reducing confusion and potential errors.

= Documentation & Support =

For detailed documentation, examples, and support, visit [DesignStack](https://designstack.co.uk)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/contact-form-7-enhanced-date-field` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Make sure Contact Form 7 is installed and activated
4. Use the "Enhanced Date" button in the Contact Form 7 form editor to insert date fields

== Usage ==

= Basic Usage =

1. Edit your Contact Form 7 form
2. Click the "Enhanced Date" button
3. Configure your date field options
4. Insert the generated tag into your form

= Manual Tag Examples =

**Basic date field:**
```
[date-enhanced your-date]
```

**Required date field:**
```
[date-enhanced* your-date]
```

**With minimum date (today):**
```
[date-enhanced your-date min-date:today]
```

**With date range:**
```
[date-enhanced your-date min-date:today max-date:"+1 month"]
```

**Exclude weekends:**
```
[date-enhanced your-date exclude-days:"0,6"]
```

**Exclude specific dates (use dd-mm-yyyy format):**
```
[date-enhanced your-date exclude-dates:"01-01-2025,25-12-2025"]
```

**Exclude date range (use dd-mm-yyyy format):**
```
[date-enhanced your-date exclude-dates:"01-01-2025 to 05-01-2025"]
```

**Custom date format:**
```
[date-enhanced your-date date-format:"m/d/Y"]
```

**Dependent date pickers (Start and End Date):**
```
[date-enhanced start-date placeholder "Select Start Date"]
[date-enhanced end-date linked-to:start-date placeholder "Select End Date"]
```

= Configuration Options =

* **min-date** - Minimum selectable date (e.g., "today", "+1 week", "2025-01-01")
* **max-date** - Maximum selectable date (e.g., "+1 year", "2025-12-31")
* **exclude-days** - Comma-separated list of days to exclude (0=Sunday, 6=Saturday)
* **exclude-dates** - Comma-separated dates or ranges to exclude (use dd-mm-yyyy format: "01-01-2025,25-12-2025")
* **date-format** - Display format (Y-m-d, m/d/Y, d/m/Y, etc.)
* **linked-to** - Name of the start date field (for end date fields)
* **placeholder** - Placeholder text for the input field

= Date Format Options =

Common formats you can use:

* `Y-m-d` - 2025-01-31 (default)
* `m/d/Y` - 01/31/2025
* `d/m/Y` - 31/01/2025
* `d-m-Y` - 31-01-2025
* `m-d-Y` - 01-31-2025
* `d.m.Y` - 31.01.2025

= Relative Date Examples =

* `today` - Current date
* `tomorrow` - Next day
* `+1 day` - Tomorrow
* `+1 week` - One week from today
* `+2 weeks` - Two weeks from today
* `+1 month` - One month from today
* `+3 months` - Three months from today
* `+1 year` - One year from today
* `-1 week` - One week ago (for historical dates)

= Exclude Days Examples =

**Exclude weekends:**
```
exclude-days:"0,6"
```
or
```
exclude-days:"saturday,sunday"
```

**Exclude Mondays and Fridays:**
```
exclude-days:"1,5"
```

= Complete Example: Event Booking Form =

```
<label>Event Name
    [text* event-name]
</label>

<label>Event Start Date (Minimum 2 weeks from today, no weekends)
    [date-enhanced* start-date min-date:"+2 weeks" exclude-days:"0,6" placeholder "Select start date"]
</label>

<label>Event End Date (Must be after start date, no weekends)
    [date-enhanced* end-date linked-to:start-date exclude-days:"0,6" placeholder "Select end date"]
</label>

<label>Number of Attendees
    [number* attendees min:1 max:100]
</label>

[submit "Book Event"]
```

== Frequently Asked Questions ==

= Does this plugin require Contact Form 7? =

Yes, Contact Form 7 must be installed and activated for this plugin to work.

= Is the date picker mobile-friendly? =

Yes! The date picker is fully responsive and optimized for touch devices.

= Can I have multiple date fields in one form? =

Yes, you can have as many date fields as you need in a single form.

= Can I create dependent date pickers (start/end dates)? =

Yes! Use the `linked-to` parameter to make an end date field depend on a start date field.

= How do I exclude weekends? =

Use `exclude-days:"0,6"` where 0 is Sunday and 6 is Saturday.

= Can I exclude specific dates? =

Yes! Use `exclude-dates:"01-01-2025,25-12-2025"` to exclude specific dates. Note: Use dd-mm-yyyy format.

= Can I exclude a range of dates? =

Yes! Use `exclude-dates:"01-01-2025 to 05-01-2025"` to exclude a date range. Note: Use dd-mm-yyyy format.

= What date formats are supported? =

The plugin supports various formats including Y-m-d, m/d/Y, d/m/Y, and more. See the date format section for details.

= Does it support relative dates? =

Yes! You can use relative dates like "today", "+1 week", "+2 months", etc.

= Is the plugin accessible? =

Yes, the plugin is built with accessibility in mind and follows WCAG guidelines.

= Does it support dark mode? =

Yes, the plugin automatically adapts to the user's dark mode preference.

= Can I customize the styling? =

Yes! You can add custom CSS to override the default styles. All elements have specific classes you can target.

= Does it work with AJAX forms? =

Yes, the plugin is compatible with AJAX form submissions and dynamic forms.

== Screenshots ==

1. Enhanced date field in a form
2. Date picker calendar interface
3. Admin tag generator interface
4. Mobile view of date picker
5. Dependent date pickers example
6. Date exclusion in action

== Changelog ==

= 1.0.2 =
* CRITICAL FIX: Resolved form tag registration issue causing shortcodes to display as raw text
* Fixed: Changed hook from 'plugins_loaded' to 'wpcf7_init' for proper registration timing
* Fixed: Direct call to register_form_tag() instead of nested action hooks
* This version is essential for the plugin to work correctly

= 1.0.1 =
* Fixed: Field not displaying on frontend
* Improved: Form tag handler with better class handling
* Improved: JavaScript initialization with error checking and debug logging
* Added: Troubleshooting guide (TROUBLESHOOTING.md)
* Added: Autocomplete="off" attribute to prevent browser autofill
* Fixed: Better compatibility with various CF7 configurations
* Updated: Documentation with correct shortcode syntax examples

= 1.0.0 =
* Initial release
* Beautiful, mobile-friendly date picker
* Minimum and maximum date restrictions
* Relative date support
* Exclude specific days of the week
* Exclude specific dates and date ranges
* Custom date formats
* Dependent date pickers (start/end dates)
* Dark mode support
* Accessibility features
* AJAX form compatibility

== Upgrade Notice ==

= 1.0.2 =
CRITICAL UPDATE: Fixes form tag registration. Plugin will not work without this update. Shortcodes were displaying as raw text in version 1.0.1. Update immediately.

= 1.0.1 =
Important bug fix: Resolves issue where date field was not displaying on frontend. Update recommended for all users.

= 1.0.0 =
Initial release of Contact Form 7 Enhanced Date Field

== Credits ==

This plugin uses:
* [Flatpickr](https://flatpickr.js.org/) - A lightweight and powerful datetime picker (MIT License)

== Developer Information ==

**Author:** DesignStack
**Website:** [https://designstack.co.uk](https://designstack.co.uk)

== Privacy Policy ==

This plugin does not collect, store, or transmit any user data. All date selection happens locally in the user's browser.

== Support ==

For support, please visit [DesignStack Support](https://designstack.co.uk/support) or use the WordPress.org support forums.
