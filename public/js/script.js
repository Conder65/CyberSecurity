(() => {
  const registerButton = document.getElementById('register-button');
  const loginButton = document.getElementById('login-button');
  const registerForm = document.getElementById('register-form');
  const loginForm = document.getElementById('login-form');
  const uploadSection = document.getElementById('upload-section');

  const getAuth = () => {
    // Injected by PHP (see index.php)
    if (window.APP_AUTHENTICATED === true) return true;
    if (window.APP_AUTHENTICATED === false) return false;
    return false;
  };

  const show = (el) => el && el.classList.remove('hidden');
  const hide = (el) => el && el.classList.add('hidden');

  const applyAuthState = () => {
    const isAuthed = getAuth();

    if (isAuthed) {
      hide(registerForm);
      hide(loginForm);
      show(uploadSection);
      return;
    }

    // Not authed: hide upload, show buttons/forms only after click
    hide(uploadSection);
  };

  const wireToggles = () => {
    if (!registerButton || !loginButton) return;

    registerButton.addEventListener('click', () => {
      show(registerForm);
      hide(loginForm);
    });

    loginButton.addEventListener('click', () => {
      show(loginForm);
      hide(registerForm);
    });
  };

  document.addEventListener('DOMContentLoaded', () => {
    applyAuthState();
    wireToggles();
  });
})();

