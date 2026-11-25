<?php
function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$loginError = is_string($_GET['error'] ?? null) ? trim($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sign in to the Student Portal to manage your student information.">
    <title>Sign in | Student Portal</title>
    <link rel="stylesheet" href="style.css">
    <script src="login.js" defer></script>
</head>
<body class="auth-page">
    <div class="auth-shell">
        <aside class="auth-intro" aria-labelledby="portal-name">
            <a class="brand" href="index.php" aria-label="Student Portal home">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 32 32" role="img">
                        <path d="M16 3 28 9.5v2.3L16 18 4 11.8V9.5L16 3Z" fill="currentColor" opacity=".98"/>
                        <path d="M8 14.3v6.2c0 2.5 3.6 5 8 5s8-2.5 8-5v-6.2l-8 4.2-8-4.2Z" fill="currentColor" opacity=".72"/>
                        <path d="M28 11.1v7.4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"/>
                    </svg>
                </span>
                <span id="portal-name">Student Portal</span>
            </a>

            <div class="intro-copy">
                <p class="eyebrow">Your academic home base</p>
                <h1>Keep your next step in sight.</h1>
                <p>One simple place to stay connected to your student information, account, and campus updates.</p>
            </div>

            <ul class="intro-points" aria-label="Portal features">
                <li>
                    <span class="point-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"/></svg>
                    </span>
                    <span>View your student details at a glance</span>
                </li>
                <li>
                    <span class="point-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 3 5 6v5c0 4.4 2.8 8.3 7 10 4.2-1.7 7-5.6 7-10V6l-7-3Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8"/><path d="m9 12 2 2 4-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>
                    </span>
                    <span>A secure, focused experience</span>
                </li>
            </ul>

            <p class="intro-footnote">Designed for students, wherever learning takes you.</p>
        </aside>

        <main class="auth-panel" aria-labelledby="login-heading">
            <div class="auth-content">
                <div class="mobile-brand">
                    <span class="brand-mark">
                        <svg viewBox="0 0 32 32"><path d="M16 3 28 9.5v2.3L16 18 4 11.8V9.5L16 3Z" fill="currentColor"/><path d="M8 14.3v6.2c0 2.5 3.6 5 8 5s8-2.5 8-5v-6.2l-8 4.2-8-4.2Z" fill="currentColor" opacity=".72"/><path d="M28 11.1v7.4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"/></svg>
                    </span>
                    <span>Student Portal</span>
                </div>

                <div class="form-heading">
                    <p class="eyebrow">Welcome back</p>
                    <h2 id="login-heading">Sign in to your account</h2>
                    <p>Use your portal credentials to continue.</p>
                </div>

                <div id="registered" class="notice notice-success" role="status" aria-live="polite" hidden>
                    <span class="notice-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    </span>
                    <span>Your account is ready. Sign in to get started.</span>
                </div>

                <div id="message" class="notice notice-error" role="alert" aria-live="assertive"<?= $loginError === '' ? ' hidden' : '' ?>>
                    <span class="notice-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M10.3 4.4 2.7 18a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7 3L13.7 4.4a2 2 0 0 0-3.4 0Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>
                    </span>
                    <span class="notice-message"><?= escapeHtml($loginError) ?></span>
                </div>

                <form id="login-form" class="auth-form" action="login.php" method="post">
                    <div class="form-field">
                        <label for="username">Username</label>
                        <div class="input-shell">
                            <span class="input-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/></svg>
                            </span>
                            <input id="username" name="username" type="text" maxlength="50" autocomplete="username" placeholder="Enter your username" required autofocus>
                        </div>
                    </div>

                    <div class="form-field">
                        <div class="field-label-row">
                            <label for="password">Password</label>
                            <button class="password-toggle" type="button" data-password-toggle data-target="password" aria-pressed="false">Show</button>
                        </div>
                        <div class="input-shell">
                            <span class="input-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M8 10V7a4 4 0 0 1 8 0v3" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/></svg>
                            </span>
                            <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <button id="login-submit" class="primary-button" type="submit">
                        <span class="button-label">Sign in</span>
                        <span class="button-arrow" aria-hidden="true">→</span>
                    </button>
                </form>

                <div class="form-divider" aria-hidden="true"><span>New to the portal?</span></div>

                <button id="open-signup" class="secondary-button" type="button" aria-haspopup="dialog">Create an account</button>

                <p class="privacy-note">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 5 6v5c0 4.4 2.8 8.3 7 10 4.2-1.7 7-5.6 7-10V6l-7-3Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.7"/><path d="m9 12 2 2 4-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"/></svg>
                    Your information is handled securely.
                </p>
            </div>
        </main>
    </div>

    <dialog id="signup-dialog" class="signup-dialog" aria-labelledby="signup-heading">
        <div class="dialog-heading">
            <div>
                <p class="eyebrow">Get started</p>
                <h2 id="signup-heading">Create your account</h2>
                <p>Set up your student portal access in a minute.</p>
            </div>
            <button id="close-signup" class="dialog-close" type="button" aria-label="Close create account dialog">&times;</button>
        </div>

        <div id="signup-error" class="notice notice-error" role="alert" aria-live="assertive" hidden>
            <span class="notice-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M10.3 4.4 2.7 18a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7 3L13.7 4.4a2 2 0 0 0-3.4 0Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>
            </span>
            <span class="notice-message"></span>
        </div>

        <form id="signup-form" class="signup-form" action="signup.php" method="post">
            <div class="form-grid">
                <div class="form-field">
                    <label for="first-name">First name</label>
                    <input id="first-name" name="first_name" type="text" maxlength="100" autocomplete="given-name" placeholder="e.g. Alex" required>
                </div>
                <div class="form-field">
                    <label for="last-name">Last name</label>
                    <input id="last-name" name="last_name" type="text" maxlength="100" autocomplete="family-name" placeholder="e.g. Rivera" required>
                </div>
            </div>

            <div class="form-field">
                <label for="signup-username">Username</label>
                <input id="signup-username" name="username" type="text" maxlength="50" pattern="[a-zA-Z0-9_.\-]+" autocomplete="username" placeholder="Choose a username" required>
                <p class="field-help">Letters, numbers, dots, hyphens, and underscores only.</p>
            </div>

            <div class="form-field">
                <div class="field-label-row">
                    <label for="signup-password">Password</label>
                    <button class="password-toggle" type="button" data-password-toggle data-target="signup-password" aria-pressed="false">Show</button>
                </div>
                <input id="signup-password" name="password" type="password" minlength="8" maxlength="72" autocomplete="new-password" placeholder="Create a password" required>
                <div class="password-strength" aria-live="polite">
                    <div class="strength-track"><span id="strength-bar"></span></div>
                    <span id="strength-label">Use 8–72 characters</span>
                </div>
            </div>

            <div class="form-field">
                <div class="field-label-row">
                    <label for="confirm-password">Confirm password</label>
                    <button class="password-toggle" type="button" data-password-toggle data-target="confirm-password" aria-pressed="false">Show</button>
                </div>
                <input id="confirm-password" name="confirm_password" type="password" minlength="8" maxlength="72" autocomplete="new-password" placeholder="Repeat your password" required>
            </div>

            <button id="signup-submit" class="primary-button" type="submit">
                <span class="button-label">Create account</span>
                <span class="button-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </dialog>
</body>
</html>
