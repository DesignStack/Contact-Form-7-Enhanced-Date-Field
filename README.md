# Contact Form 7 Enhanced Date Field

A powerful WordPress plugin that adds a beautiful, mobile-friendly enhanced date field to Contact Form 7 with advanced restrictions and customization options.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress](https://img.shields.io/badge/wordpress-5.0%2B-blue.svg)
![PHP](https://img.shields.io/badge/php-7.2%2B-purple.svg)
![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)

## Features

### Core Features
- **Beautiful & Modern Design** - Clean, professional date picker interface
- **Mobile-Friendly** - Optimized for touch devices with responsive design
- **Lightweight** - Minimal footprint with CDN-hosted libraries
- **No jQuery Dependency** - Uses modern flatpickr library

### Date Restrictions
- **Minimum Date** - Set the earliest selectable date
- **Maximum Date** - Set the latest selectable date
- **Relative Dates** - Use dynamic dates like "today", "+1 week", "+2 months"
- **Exclude Days** - Disable specific days of the week (e.g., weekends)
- **Exclude Date Ranges** - Block specific dates or date ranges

### Advanced Features
- **Dependent Date Pickers** - Create start/end date pairs
- **Custom Date Formats** - Display dates in your preferred format
- **Dark Mode Support** - Automatically adapts to user's preference
- **Accessible** - Built with accessibility in mind
- **AJAX Compatible** - Works with dynamic forms

## Installation

1. Download the plugin
2. Upload to `/wp-content/plugins/contact-form-7-enhanced-date-field`
3. Activate the plugin through WordPress admin
4. Ensure Contact Form 7 is installed and activated
5. Use the "Enhanced Date" button in CF7 form editor

## Usage

### Basic Example

```
[date-enhanced your-date]
```

### Required Field

```
[date-enhanced* your-date]
```

### With Date Restrictions

```
[date-enhanced your-date min-date:today max-date:"+1 month"]
```

### Exclude Weekends

```
[date-enhanced your-date exclude-days:"0,6"]
```

### Dependent Date Pickers (Start/End Dates)

```
[date-enhanced start-date placeholder "Select Start Date"]
[date-enhanced end-date linked-to:start-date placeholder "Select End Date"]
```

## Configuration Options

| Option | Description | Example |
|--------|-------------|---------|
| `min-date` | Minimum selectable date | `today`, `+1 week`, `2025-01-01` |
| `max-date` | Maximum selectable date | `+1 year`, `2025-12-31` |
| `exclude-days` | Days to exclude (0=Sun, 6=Sat) | `0,6` or `saturday,sunday` |
| `exclude-dates` | Specific dates to exclude (dd-mm-yyyy format) | `01-01-2025,25-12-2025` |
| `date-format` | Display format | `Y-m-d`, `m/d/Y`, `d/m/Y` |
| `linked-to` | Start date field name | `start-date` |
| `placeholder` | Placeholder text | `Select a date...` |

## Date Format Options

- `Y-m-d` - 2025-01-31 (default)
- `m/d/Y` - 01/31/2025
- `d/m/Y` - 31/01/2025
- `d-m-Y` - 31-01-2025
- `m-d-Y` - 01-31-2025
- `d.m.Y` - 31.01.2025

## Relative Date Examples

- `today` - Current date
- `tomorrow` - Next day
- `+1 day` - Tomorrow
- `+1 week` - One week from today
- `+2 weeks` - Two weeks from today
- `+1 month` - One month from today
- `+3 months` - Three months from today
- `+1 year` - One year from today
- `-1 week` - One week ago

## Complete Example: Event Booking Form

```html
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

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- Contact Form 7 plugin

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Development

### File Structure

```
contact-form-7-enhanced-date-field/
├── assets/
│   ├── css/
│   │   └── cf7-enhanced-date-field.css
│   └── js/
│       └── cf7-enhanced-date-field.js
├── contact-form-7-enhanced-date-field.php
├── readme.txt
├── README.md
└── LICENSE
```

### Technologies Used

- **Flatpickr** - Modern date picker library (MIT License)
- **Vanilla JavaScript** - No jQuery dependency
- **CSS3** - Modern styling with dark mode support

## Credits

- Developed by [DesignStack](https://designstack.co.uk)
- Uses [Flatpickr](https://flatpickr.js.org/) date picker library

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support, please visit:
- [DesignStack Website](https://designstack.co.uk)
- [WordPress.org Support Forums](https://wordpress.org/support/plugin/contact-form-7-enhanced-date-field)

## Changelog

### 1.0.0
- Initial release
- Beautiful, mobile-friendly date picker
- Minimum and maximum date restrictions
- Relative date support
- Exclude specific days of the week
- Exclude specific dates and date ranges
- Custom date formats
- Dependent date pickers (start/end dates)
- Dark mode support
- Accessibility features
- AJAX form compatibility

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Privacy

This plugin does not collect, store, or transmit any user data. All date selection happens locally in the user's browser.

---

Made with ❤️ by [DesignStack](https://designstack.co.uk)
