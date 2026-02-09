/**
 * Partyhelp Form - Client-side validation and submission
 */
(function ($) {
  'use strict';

  const constraints = {
    first_name: { required: true, minLength: 2, maxLength: 100 },
    last_name: { required: true, minLength: 2, maxLength: 100 },
    email: { required: true, email: true },
    phone: { required: true, minLength: 8, maxLength: 20 },
    occasion_type: { required: true },
    preferred_date: { required: true, futureDate: true },
    guest_count: { required: true },
    location: { required: true, minChecked: 1 },
    special_requirements: { maxLength: 500 },
  };

  const messages = {
    required: 'This field is required.',
    minLength: 'Please enter at least {min} characters.',
    maxLength: 'Please enter no more than {max} characters.',
    email: 'Please enter a valid email address.',
    futureDate: 'Please select a future date.',
    minChecked: 'Please select at least one location.',
  };

  function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function validateFutureDate(value) {
    if (!value) return false;
    const date = new Date(value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    date.setHours(0, 0, 0, 0);
    return date > today;
  }

  function validateField(name, value, values) {
    const c = constraints[name];
    if (!c) return null;

    if (c.required && (!value || (Array.isArray(value) && value.length === 0))) {
      return messages.required;
    }
    if (!c.required && !value) return null;

    if (c.minLength && String(value).length < c.minLength) {
      return messages.minLength.replace('{min}', c.minLength);
    }
    if (c.maxLength && String(value).length > c.maxLength) {
      return messages.maxLength.replace('{max}', c.maxLength);
    }
    if (c.email && !validateEmail(value)) {
      return messages.email;
    }
    if (c.futureDate && !validateFutureDate(value)) {
      return messages.futureDate;
    }
    if (c.minChecked && Array.isArray(value) && value.length < c.minChecked) {
      return messages.minChecked;
    }

    return null;
  }

  function getFormValues($form) {
    const values = {};
    $form.find('[name]').each(function () {
      const $el = $(this);
      const name = $el.attr('name');
      if (!name || name === 'location[]') return;
      const baseName = name.replace('[]', '');
      if (name.endsWith('[]')) {
        values[baseName] = $form.find(`[name="${name}"]:checked`).map(function () {
          return $(this).val();
        }).get();
      } else {
        values[baseName] = $el.val()?.trim() || '';
      }
    });
    values.location = $form.find('[name="location[]"]:checked').map(function () {
      return $(this).val();
    }).get();
    return values;
  }

  function showErrors($form, errors) {
    $form.find('.partyhelp-input-error, .partyhelp-select.partyhelp-input-error, .partyhelp-textarea.partyhelp-input-error').removeClass('partyhelp-input-error');
    $form.find('.partyhelp-field-error').text('');

    $.each(errors, function (field, msg) {
      const $err = $form.find(`.partyhelp-field-error[data-field="${field}"]`);
      if ($err.length) {
        $err.text(msg);
        const $input = $form.find(`[name="${field}"], [name="${field}[]"]`).first();
        if ($input.length) $input.addClass('partyhelp-input-error');
      }
    });
  }

  function validateForm($form) {
    const values = getFormValues($form);
    const errors = {};

    $.each(constraints, function (field) {
      const err = validateField(field, values[field], values);
      if (err) errors[field] = err;
    });

    return errors;
  }

  $(document).ready(function () {
    const $form = $('#partyhelp-form');
    if (!$form.length) return;

    $form.on('submit', function (e) {
      e.preventDefault();

      const $btn = $('#partyhelp-submit-btn');
      const $msg = $('#partyhelp-form-message');
      $msg.removeClass('partyhelp-form-message-success partyhelp-form-message-error').text('');
      showErrors($form, {});

      const errors = validateForm($form);
      if (Object.keys(errors).length > 0) {
        showErrors($form, errors);
        $msg.addClass('partyhelp-form-message-error').text('Please fix the errors below.');
        return;
      }

      $btn.prop('disabled', true).text('Sending...');

      const formData = new FormData($form[0]);
      formData.append('action', 'partyhelp_form_submit');
      formData.append('nonce', typeof partyhelpForm !== 'undefined' ? partyhelpForm.nonce : '');

      $.ajax({
        url: typeof partyhelpForm !== 'undefined' ? partyhelpForm.ajaxUrl : '',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
      })
        .done(function (res) {
          if (res.success) {
            $form[0].reset();
            $msg.addClass('partyhelp-form-message-success').text(res.data?.message || 'Thank you! We will email you venue recommendations soon.');
          } else {
            const errs = res.data?.errors || {};
            const errMessages = [];
            $.each(errs, function (k, v) {
              if (Array.isArray(v)) errMessages.push(v[0]);
              else errMessages.push(v);
            });
            showErrors($form, res.data?.errors || {});
            $msg.addClass('partyhelp-form-message-error').text(res.data?.message || errMessages.join(' ') || 'Something went wrong. Please try again.');
          }
        })
        .fail(function () {
          $msg.addClass('partyhelp-form-message-error').text('Unable to submit. Please check your connection and try again.');
        })
        .always(function () {
          $btn.prop('disabled', false).text('Send me venues');
        });
    });
  });
})(jQuery);
