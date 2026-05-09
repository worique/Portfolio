document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const themeToggle = document.getElementById('themeToggle');
  const themeIcon = document.getElementById('themeIcon');
  const menuToggle = document.getElementById('menuToggle');
  const navLinks = document.getElementById('navLinks');

  const savedTheme = localStorage.getItem('portfolio_theme');
  if (savedTheme === 'light') {
    body.classList.add('light-mode');
    if (themeIcon) themeIcon.textContent = '☀️';
  } else {
    if (themeIcon) themeIcon.textContent = '🌙';
  }

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      body.classList.toggle('light-mode');
      const isLight = body.classList.contains('light-mode');
      localStorage.setItem('portfolio_theme', isLight ? 'light' : 'dark');
      if (themeIcon) themeIcon.textContent = isLight ? '☀️' : '🌙';
    });
  }

  if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', () => {
      navLinks.classList.toggle('open');
      const expanded = navLinks.classList.contains('open');
      menuToggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    });
  }

  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', (event) => {
      const targetId = anchor.getAttribute('href');
      const target = targetId ? document.querySelector(targetId) : null;
      if (target) {
        event.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      const statusBox = document.getElementById('formStatus');

      const validationResult = typeof window.validateContactForm === 'function'
        ? window.validateContactForm(contactForm)
        : { isValid: true };

      if (!validationResult.isValid) {
        if (statusBox) {
          statusBox.textContent = 'Please fix the highlighted errors.';
          statusBox.className = 'form-status error';
        }
        return;
      }

      try {
        const endpoint = contactForm.dataset.endpoint;
        const formData = new FormData(contactForm);

        const response = await fetch(endpoint, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        const data = await response.json();
        if (data.success) {
          contactForm.reset();
          if (statusBox) {
            statusBox.textContent = data.message;
            statusBox.className = 'form-status success';
          }
          contactForm.querySelectorAll('[data-error-for]').forEach((el) => {
            el.textContent = '';
          });
        } else {
          if (statusBox) {
            statusBox.textContent = data.message || 'An error occurred while sending your message.';
            statusBox.className = 'form-status error';
          }
        }
      } catch (error) {
        if (statusBox) {
          statusBox.textContent = 'Network error. Please try again later.';
          statusBox.className = 'form-status error';
        }
      }
    });
  }

  const reloadProjectsBtn = document.getElementById('reloadProjectsBtn');
  const projectsContainer = document.getElementById('projectsContainer');
  if (reloadProjectsBtn && projectsContainer) {
    reloadProjectsBtn.addEventListener('click', async () => {
      const endpoint = reloadProjectsBtn.dataset.fetchUrl;
      reloadProjectsBtn.disabled = true;
      reloadProjectsBtn.textContent = 'Loading...';

      try {
        const response = await fetch(endpoint);
        const data = await response.json();

        if (!data.success || !Array.isArray(data.projects)) {
          throw new Error('Invalid payload');
        }

        if (data.projects.length === 0) {
          projectsContainer.innerHTML = '<p class="empty-state">No projects found.</p>';
          return;
        }

        projectsContainer.innerHTML = data.projects.map((project) => `
          <article class="project-card">
            <img src="${project.image_url}" alt="${project.title}" loading="lazy">
            <div class="project-content">
              <h3>${project.title}</h3>
              <p>${project.description}</p>
              <p class="tech-tags">${project.technologies}</p>
              <div class="project-links">
                <a href="${project.github_link}" target="_blank" rel="noopener">GitHub</a>
                <a href="${project.demo_link}" target="_blank" rel="noopener">Live Demo</a>
              </div>
            </div>
          </article>
        `).join('');
      } catch (error) {
        projectsContainer.insertAdjacentHTML('afterbegin', '<p class="form-status error">Could not reload projects right now.</p>');
      } finally {
        reloadProjectsBtn.disabled = false;
        reloadProjectsBtn.textContent = 'Reload via AJAX';
      }
    });
  }
});
