/**
 * Contact Form 7 Enhanced Date Field
 * JavaScript functionality
 */

(function($) {
    'use strict';

    // Store flatpickr instances
    var flatpickrInstances = {};

    /**
     * Initialize date pickers
     */
    function initDatePickers() {
        $('.wpcf7-date-enhanced').each(function() {
            var $input = $(this);
            var fieldName = $input.attr('name');

            // Skip if already initialized
            if (flatpickrInstances[fieldName]) {
                return;
            }

            // Get configuration from data attribute
            var config = $input.data('config') || {};

            // Prepare flatpickr configuration
            var flatpickrConfig = {
                dateFormat: config.dateFormat || 'Y-m-d',
                minDate: config.minDate || null,
                maxDate: config.maxDate || null,
                disable: [],
                allowInput: false,
                clickOpens: true,
                // Make it mobile-friendly
                disableMobile: false,
                // Add clear button
                // Add change listener for validation
                onChange: function(selectedDates, dateStr, instance) {
                    // Trigger change event for CF7 validation
                    $input.trigger('change');

                    // Update dependent date pickers
                    updateDependentDatePickers(fieldName, dateStr);
                },
                // Accessibility
                ariaDateFormat: config.dateFormat || 'Y-m-d',
                locale: {
                    firstDayOfWeek: 1 // Monday
                }
            };

            // Handle disabled days
            if (config.disable && config.disable.length > 0) {
                config.disable.forEach(function(disableRule) {
                    if (disableRule.function === 'disableDays' && disableRule.days) {
                        // Disable specific days of the week
                        flatpickrConfig.disable.push(function(date) {
                            return disableRule.days.indexOf(date.getDay()) !== -1;
                        });
                    } else if (disableRule.from && disableRule.to) {
                        // Disable date range
                        flatpickrConfig.disable.push({
                            from: disableRule.from,
                            to: disableRule.to
                        });
                    } else if (typeof disableRule === 'string') {
                        // Disable specific date
                        flatpickrConfig.disable.push(disableRule);
                    }
                });
            }

            // Handle linked date fields (dependent date pickers)
            if (config.linkedTo) {
                setupDependentDatePicker($input, config.linkedTo, flatpickrConfig);
            }

            // Initialize flatpickr
            var instance = flatpickr($input[0], flatpickrConfig);
            flatpickrInstances[fieldName] = instance;
        });
    }

    /**
     * Setup dependent date picker
     * The current field is the "end date" that depends on a "start date"
     */
    function setupDependentDatePicker($endDateInput, startDateFieldName, config) {
        var $startDateInput = $('input[name="' + startDateFieldName + '"]');

        if ($startDateInput.length === 0) {
            console.warn('CF7 Enhanced Date: Linked field "' + startDateFieldName + '" not found');
            return;
        }

        // Set minimum date based on start date
        config.minDate = null; // Will be set dynamically

        // Listen for changes on start date
        $startDateInput.on('change', function() {
            var startDateInstance = flatpickrInstances[startDateFieldName];
            var endDateInstance = flatpickrInstances[$endDateInput.attr('name')];

            if (startDateInstance && endDateInstance) {
                var selectedDate = startDateInstance.selectedDates[0];

                if (selectedDate) {
                    // Set minimum date to the day after start date
                    var nextDay = new Date(selectedDate);
                    nextDay.setDate(nextDay.getDate() + 1);
                    endDateInstance.set('minDate', nextDay);

                    // Clear end date if it's before the new minimum
                    var currentEndDate = endDateInstance.selectedDates[0];
                    if (currentEndDate && currentEndDate < nextDay) {
                        endDateInstance.clear();
                    }
                } else {
                    // If start date is cleared, reset end date minimum
                    endDateInstance.set('minDate', null);
                }
            }
        });
    }

    /**
     * Update dependent date pickers when a date changes
     */
    function updateDependentDatePickers(changedFieldName, newValue) {
        // Find any fields that are linked to this field
        $('.wpcf7-date-enhanced').each(function() {
            var $input = $(this);
            var config = $input.data('config') || {};

            if (config.linkedTo === changedFieldName) {
                var fieldName = $input.attr('name');
                var instance = flatpickrInstances[fieldName];
                var startInstance = flatpickrInstances[changedFieldName];

                if (instance && startInstance) {
                    var selectedDate = startInstance.selectedDates[0];

                    if (selectedDate) {
                        var nextDay = new Date(selectedDate);
                        nextDay.setDate(nextDay.getDate() + 1);
                        instance.set('minDate', nextDay);

                        // Clear if current value is invalid
                        var currentDate = instance.selectedDates[0];
                        if (currentDate && currentDate < nextDay) {
                            instance.clear();
                        }
                    } else {
                        instance.set('minDate', null);
                    }
                }
            }
        });
    }

    /**
     * Reinitialize on AJAX form submission (CF7 dynamic forms)
     */
    function reinitialize() {
        // Destroy existing instances
        Object.keys(flatpickrInstances).forEach(function(key) {
            if (flatpickrInstances[key] && flatpickrInstances[key].destroy) {
                flatpickrInstances[key].destroy();
            }
        });
        flatpickrInstances = {};

        // Reinitialize
        initDatePickers();
    }

    // Initialize on document ready
    $(document).ready(function() {
        initDatePickers();
    });

    // Reinitialize after CF7 form submission (for multi-step or conditional forms)
    $(document).on('wpcf7mailsent', function() {
        setTimeout(reinitialize, 100);
    });

    // Reinitialize when CF7 form is reset
    $(document).on('wpcf7invalid wpcf7spam wpcf7mailfailed', function() {
        setTimeout(reinitialize, 100);
    });

    // For AJAX-loaded forms
    if (typeof MutationObserver !== 'undefined') {
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    var hasNewDateFields = false;
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            if ($(node).find('.wpcf7-date-enhanced').length > 0 ||
                                $(node).hasClass('wpcf7-date-enhanced')) {
                                hasNewDateFields = true;
                            }
                        }
                    });

                    if (hasNewDateFields) {
                        setTimeout(initDatePickers, 100);
                    }
                }
            });
        });

        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

})(jQuery);
