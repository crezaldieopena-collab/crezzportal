(() => {
    'use strict';

    const query = new URLSearchParams(window.location.search);
    const loginForm = document.querySelector('#login-form');
    const loginSubmit = document.querySelector('#login-submit');
    const usernameInput = document.querySelector('#username');
    const signupDialog = document.querySelector('#signup-dialog');
    const openSignupButton = document.querySelector('#open-signup');
    const closeSignupButton = document.querySelector('#close-signup');
    const signupForm = document.querySelector('#signup-form');
    const signupSubmit = document.querySelector('#signup-submit');
    const signupError = document.querySelector('#signup-error');
    const registeredNotice = document.querySelector('#registered');
    const loginError = document.querySelector('#message');
    const signupPassword = document.querySelector('#signup-password');
    const confirmPassword = document.querySelector('#confirm-password');
    const strengthMeter = document.querySelector('.password-strength');
    const strengthBar = document.querySelector('#strength-bar');
    const strengthLabel = document.querySelector('#strength-label');

    let dialogReturnFocus = openSignupButton;

    const setNotice = (notice, message) => {
        if (!notice) {
            return;
        }

        const messageElement = notice.querySelector('.notice-message');
        if (messageElement) {
            messageElement.textContent = message;
        }
        notice.hidden = !message;
    };

    const setButtonBusy = (button, busy, busyLabel) => {
        if (!button) {
            return;
        }

        const label = button.querySelector('.button-label');
        if (!button.dataset.defaultLabel && label) {
            button.dataset.defaultLabel = label.textContent;
        }

        button.disabled = busy;
        button.setAttribute('aria-busy', String(busy));
        if (label) {
            label.textContent = busy
                ? busyLabel
                : button.dataset.defaultLabel;
        }
    };

    const cleanQuery = () => {
        const url = new URL(window.location.href);
        ['error', 'registered', 'signup', 'signupError'].forEach((key) => url.searchParams.delete(key));
        const cleanUrl = `${url.pathname}${url.search}${url.hash}`;
        window.history.replaceState({}, document.title, cleanUrl);
    };

    const focusNotice = (notice) => {
        if (notice && !notice.hidden) {
            window.setTimeout(() => notice.focus(), 0);
        }
    };

    const openSignup = () => {
        if (!signupDialog) {
            return;
        }

        dialogReturnFocus = document.activeElement instanceof HTMLElement
            ? document.activeElement
            : openSignupButton;

        if (typeof signupDialog.showModal === 'function') {
            if (!signupDialog.open) {
                signupDialog.showModal();
            }
        } else {
            signupDialog.setAttribute('open', '');
        }

        window.setTimeout(() => {
            const firstInput = signupForm?.querySelector('input');
            firstInput?.focus();
        }, 0);
    };

    const closeSignup = () => {
        if (!signupDialog) {
            return;
        }

        if (typeof signupDialog.close === 'function' && signupDialog.open) {
            signupDialog.close();
        } else {
            signupDialog.removeAttribute('open');
            signupDialog.dispatchEvent(new Event('close'));
        }
    };

    const updatePasswordStrength = () => {
        if (!signupPassword || !strengthMeter || !strengthBar || !strengthLabel) {
            return;
        }

        const value = signupPassword.value;
        let level = 'empty';
        let width = '0%';
        let label = 'Use 8–72 characters';

        if (value.length > 0 && value.length < 8) {
            level = 'weak';
            width = '25%';
            label = 'Too short — use at least 8 characters';
        } else if (value.length >= 8) {
            const hasLowercase = /[a-z]/.test(value);
            const hasUppercase = /[A-Z]/.test(value);
            const hasNumber = /\d/.test(value);
            const hasSymbol = /[^A-Za-z0-9]/.test(value);
            const score = [
                value.length >= 12,
                hasLowercase && hasUppercase,
                hasNumber,
                hasSymbol,
            ].filter(Boolean).length;

            if (score >= 3) {
                level = 'strong';
                width = '100%';
                label = 'Strong password';
            } else {
                level = 'fair';
                width = '60%';
                label = 'Good start — add variety for a stronger password';
            }
        }

        strengthMeter.dataset.level = level;
        strengthBar.style.width = width;
        strengthLabel.textContent = label;
    };

    const validatePasswordConfirmation = () => {
        if (!confirmPassword || !signupPassword) {
            return;
        }

        const hasMismatch = confirmPassword.value !== ''
            && signupPassword.value !== confirmPassword.value;
        confirmPassword.setCustomValidity(hasMismatch ? 'The passwords do not match.' : '');
    };

    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        const input = document.getElementById(toggle.dataset.target);
        if (!input) {
            return;
        }

        toggle.addEventListener('click', () => {
            const showingPassword = input.type === 'password';
            input.type = showingPassword ? 'text' : 'password';
            toggle.textContent = showingPassword ? 'Hide' : 'Show';
            toggle.setAttribute('aria-pressed', String(showingPassword));
            toggle.setAttribute('aria-label', `${showingPassword ? 'Hide' : 'Show'} ${input.labels?.[0]?.textContent || 'password'}`);
            input.focus({ preventScroll: true });
        });
    });

    signupPassword?.addEventListener('input', () => {
        updatePasswordStrength();
        validatePasswordConfirmation();
    });

    confirmPassword?.addEventListener('input', validatePasswordConfirmation);

    openSignupButton?.addEventListener('click', openSignup);
    closeSignupButton?.addEventListener('click', closeSignup);

    signupDialog?.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeSignup();
    });

    signupDialog?.addEventListener('click', (event) => {
        const bounds = signupDialog.getBoundingClientRect();
        const clickedOutside = event.clientX < bounds.left
            || event.clientX > bounds.right
            || event.clientY < bounds.top
            || event.clientY > bounds.bottom;

        if (clickedOutside) {
            closeSignup();
        }
    });

    signupDialog?.addEventListener('close', () => {
        setNotice(signupError, '');
        signupForm?.reset();
        signupForm?.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
            toggle.textContent = 'Show';
            toggle.setAttribute('aria-pressed', 'false');
        });
        if (signupPassword) {
            signupPassword.type = 'password';
        }
        if (confirmPassword) {
            confirmPassword.type = 'password';
            confirmPassword.setCustomValidity('');
        }
        updatePasswordStrength();

        if (dialogReturnFocus instanceof HTMLElement) {
            dialogReturnFocus.focus();
        }
    });

    signupForm?.addEventListener('submit', async (event) => {
        validatePasswordConfirmation();

        if (!signupForm.checkValidity()) {
            event.preventDefault();
            signupForm.reportValidity();
            return;
        }

        event.preventDefault();
        setNotice(signupError, '');
        setButtonBusy(signupSubmit, true, 'Creating account…');

        try {
            const response = await fetch(signupForm.action, {
                method: 'POST',
                body: new FormData(signupForm),
                headers: { Accept: 'application/json' },
            });

            let payload = null;
            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                payload = await response.json();
            }

            if (!response.ok) {
                throw new Error(payload?.error || 'Account creation is unavailable right now. Please try again later.');
            }

            closeSignup();
            setNotice(registeredNotice, 'Your account is ready. Sign in to get started.');
            cleanQuery();
            usernameInput?.focus();
            focusNotice(registeredNotice);
        } catch (error) {
            const message = error instanceof Error
                ? error.message
                : 'We could not create your account. Please try again.';
            setNotice(signupError, message);
            focusNotice(signupError);
        } finally {
            setButtonBusy(signupSubmit, false);
        }
    });

    loginForm?.addEventListener('submit', () => {
        if (loginForm.checkValidity()) {
            setButtonBusy(loginSubmit, true, 'Signing in…');
        }
    });

    const queryError = query.get('error');
    const registrationComplete = query.get('registered') === '1';
    const signupMessage = query.get('signupError');

    if (queryError) {
        setNotice(loginError, queryError);
        focusNotice(loginError);
    }

    if (registrationComplete) {
        setNotice(registeredNotice, 'Your account is ready. Sign in to get started.');
        focusNotice(registeredNotice);
    }

    if (query.get('signup') === '1' || signupMessage) {
        if (signupMessage) {
            setNotice(signupError, signupMessage);
        }
        openSignup();
        if (signupMessage) {
            focusNotice(signupError);
        }
    }

    if (queryError || registrationComplete || query.has('signup') || signupMessage) {
        cleanQuery();
    }

    updatePasswordStrength();
})();
