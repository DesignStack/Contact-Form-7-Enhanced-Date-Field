# Troubleshooting Guide

## Date Field Not Showing

If your enhanced date field is not appearing on the frontend, follow these steps:

### 1. Check Your Shortcode Syntax

**Incorrect (has errors):**
```
[date-enhanced* date-enhanced-88 date-format:m/d/Y min-date:today exclude-dates:2025-12-01 to 2025-02-28 required]
```

**Correct:**
```
[date-enhanced* date-enhanced-88 date-format:m/d/Y min-date:today exclude-dates:"01-12-2025 to 28-02-2026"]
```

**Common Mistakes:**
- Missing quotes around values with spaces (like date ranges)
- Adding `required` at the end (the `*` already makes it required)
- Using yyyy-mm-dd format instead of dd-mm-yyyy for exclude-dates

### 2. Correct Shortcode Examples

**Basic date field:**
```
[date-enhanced your-date]
```

**Required date field with minimum date:**
```
[date-enhanced* booking-date min-date:today]
```

**Date field with exclusions (correct dd-mm-yyyy format):**
```
[date-enhanced* event-date min-date:today exclude-dates:"01-12-2025,25-12-2025,01-01-2026"]
```

**Date field with date range exclusion:**
```
[date-enhanced* appointment-date exclude-dates:"01-12-2025 to 28-02-2026"]
```

**Date field excluding weekends:**
```
[date-enhanced* booking-date exclude-days:"0,6"]
```

**Custom date format:**
```
[date-enhanced* your-date date-format:"m/d/Y"]
```

**Complete example:**
```
[date-enhanced* booking-date date-format:"d/m/Y" min-date:"+1 week" max-date:"+6 months" exclude-days:"0,6" exclude-dates:"25-12-2025,01-01-2026"]
```

### 3. Check Browser Console

1. Open your browser's Developer Tools (F12)
2. Go to the Console tab
3. Look for any errors mentioning "CF7 Enhanced Date" or "flatpickr"
4. You should see a message like: `CF7 Enhanced Date: Initializing field date-enhanced-88`

### 4. Verify Plugin is Active

1. Go to WordPress Admin > Plugins
2. Ensure "Contact Form 7" is activated
3. Ensure "Contact Form 7 Enhanced Date Field" is activated

### 5. Clear Cache

If you're using a caching plugin:
1. Clear your WordPress cache
2. Clear your browser cache
3. Try viewing the form in an incognito/private window

### 6. Check for JavaScript Conflicts

Some themes or plugins may conflict with jQuery or flatpickr. To test:
1. Temporarily switch to a default WordPress theme (Twenty Twenty-Four)
2. Deactivate other plugins except Contact Form 7
3. Test if the date field appears

### 7. Verify Scripts are Loading

View page source and check for:
- `flatpickr.min.css` from CDN
- `cf7-enhanced-date-field.css`
- `flatpickr.min.js` from CDN
- `cf7-enhanced-date-field.js`

If these aren't loading, there may be a theme compatibility issue.

## Date Format Reference

### For date-format option (how dates are displayed):
- `Y-m-d` = 2025-01-31
- `m/d/Y` = 01/31/2025
- `d/m/Y` = 31/01/2025
- `d-m-Y` = 31-01-2025

### For exclude-dates option (always use dd-mm-yyyy):
- Single dates: `"01-12-2025,25-12-2025"`
- Date ranges: `"01-12-2025 to 31-12-2025"`
- **Important:** Always use dd-mm-yyyy format for exclude-dates, regardless of your display format

## Still Having Issues?

1. Check that you have WordPress 5.0+ and PHP 7.2+
2. Ensure Contact Form 7 is up to date
3. Check your theme's compatibility with Contact Form 7
4. Look for JavaScript errors in the browser console
5. Try using the tag generator button in CF7 form editor instead of typing manually

## Support

For additional support, visit: https://designstack.co.uk
