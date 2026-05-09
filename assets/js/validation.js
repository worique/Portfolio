(function () {
  function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function setInlineErrors(form, errors) {
    const errorTargets = form.querySelectorAll('[data-error-for]');
    errorTargets.forEach((target) => {
      const field = target.getAttribute('data-error-for');
      target.textContent = errors[field] || '';
    });
  }

  function validateContactForm(form) {
    const data = {
      name: (form.name?.value || '').trim(),
      email: (form.email?.value || '').trim(),
      subject: (form.subject?.value || '').trim(),
      message: (form.message?.value || '').trim(),
    };

    const errors = {};

    if (!data.name || data.name.length < 3) {
      errors.name = 'Name must be at least 3 characters.';
    }

    if (!data.email || !validateEmail(data.email)) {
      errors.email = 'Please enter a valid email address.';
    }

    if (!data.subject) {
      errors.subject = 'Subject is required.';
    }

    if (!data.message || data.message.length < 10) {
      errors.message = 'Message must be at least 10 characters.';
    }

    setInlineErrors(form, errors);

    return {
      isValid: Object.keys(errors).length === 0,
      errors,
    };
  }

  window.validateContactForm = validateContactForm;
})();
