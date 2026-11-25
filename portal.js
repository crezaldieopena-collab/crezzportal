(() => {
    'use strict';

    const body = document.body;
    const themeToggle = document.querySelector('#theme-toggle');
    const themeLabel = themeToggle?.querySelector('.theme-toggle-label');
    const copyStatus = document.querySelector('#copy-status');

    const getStoredTheme = () => {
        try {
            const storedTheme = window.localStorage.getItem('student-portal-theme');
            return storedTheme === 'dark' || storedTheme === 'light' ? storedTheme : 'light';
        } catch {
            return 'light';
        }
    };

    const storeTheme = (theme) => {
        try {
            window.localStorage.setItem('student-portal-theme', theme);
        } catch {
            // The visual setting still applies when storage is unavailable.
        }
    };

    const applyTheme = (theme) => {
        const isDark = theme === 'dark';
        body.dataset.theme = isDark ? 'dark' : 'light';
        themeToggle?.setAttribute('aria-pressed', String(isDark));
        themeToggle?.setAttribute('aria-label', isDark ? 'Use light theme' : 'Use dark theme');
        if (themeLabel) {
            themeLabel.textContent = isDark ? 'Light mode' : 'Dark mode';
        }
    };

    if (themeToggle) {
        applyTheme(getStoredTheme());
        themeToggle.addEventListener('click', () => {
            const nextTheme = body.dataset.theme === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
            storeTheme(nextTheme);
        });
    }

    const showCopyStatus = (message) => {
        if (!copyStatus) {
            return;
        }

        copyStatus.textContent = message;
        window.setTimeout(() => {
            if (copyStatus.textContent === message) {
                copyStatus.textContent = '';
            }
        }, 2400);
    };

    const copyText = async (value) => {
        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(value);
            return;
        }

        const helper = document.createElement('textarea');
        helper.value = value;
        helper.setAttribute('readonly', '');
        helper.style.position = 'fixed';
        helper.style.opacity = '0';
        document.body.appendChild(helper);
        helper.select();
        const copied = document.execCommand('copy');
        helper.remove();

        if (!copied) {
            throw new Error('Copy unavailable');
        }
    };

    document.querySelectorAll('[data-copy-value]').forEach((button) => {
        button.addEventListener('click', async () => {
            const value = button.dataset.copyValue || '';
            if (!value) {
                return;
            }

            const label = button.querySelector('.copy-button-label');
            const defaultLabel = label?.textContent || 'Copy';
            button.disabled = true;

            try {
                await copyText(value);
                if (label) {
                    label.textContent = 'Copied';
                }
                showCopyStatus('Username copied to your clipboard.');
            } catch {
                showCopyStatus('Copy is unavailable in this browser.');
            } finally {
                window.setTimeout(() => {
                    button.disabled = false;
                    if (label) {
                        label.textContent = defaultLabel;
                    }
                }, 1800);
            }
        });
    });
})();
