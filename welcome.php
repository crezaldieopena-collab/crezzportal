<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function firstCharacter(string $value): string
{
    return function_exists('mb_substr') ? mb_substr($value, 0, 1, 'UTF-8') : substr($value, 0, 1);
}

function uppercase(string $value): string
{
    return function_exists('mb_strtoupper') ? mb_strtoupper($value, 'UTF-8') : strtoupper($value);
}

$sections = [
    'dashboard' => [
        'label' => 'Dashboard',
        'description' => 'A quick view of your portal account and next steps.',
    ],
    'student-information' => [
        'label' => 'Student Information',
        'description' => 'Review the personal information attached to your account.',
    ],
    'account' => [
        'label' => 'Account',
        'description' => 'Manage your account details and keep your access secure.',
    ],
    'about' => [
        'label' => 'About',
        'description' => 'Learn what you can do in your student portal.',
    ],
];

$section = $_GET['section'] ?? 'dashboard';
if (!is_string($section) || !isset($sections[$section])) {
    $section = 'dashboard';
}

$username = is_string($_SESSION['username'] ?? null) ? trim($_SESSION['username']) : '';
$firstName = is_string($_SESSION['first_name'] ?? null) ? trim($_SESSION['first_name']) : '';
$lastName = is_string($_SESSION['last_name'] ?? null) ? trim($_SESSION['last_name']) : '';
$username = $username !== '' ? $username : 'student';
$displayName = trim($firstName . ' ' . $lastName);
$displayName = $displayName !== '' ? $displayName : $username;

$initials = uppercase(firstCharacter($firstName) . firstCharacter($lastName));
if ($initials === '') {
    $initials = uppercase(firstCharacter($username));
}
$initials = $initials !== '' ? $initials : 'S';

$profileFieldCount = 1 + ($firstName !== '' ? 1 : 0) + ($lastName !== '' ? 1 : 0);
$profileCompletion = (int) round(($profileFieldCount / 3) * 100);
$profileComplete = $profileCompletion === 100;
$profileStatus = $profileComplete ? 'Complete' : 'In progress';
$greetingHour = (int) date('G');
$greeting = $greetingHour < 12 ? 'Good morning' : ($greetingHour < 18 ? 'Good afternoon' : 'Good evening');
$today = date('F j, Y');
$sectionLabel = $sections[$section]['label'];
$sectionDescription = $sections[$section]['description'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= escapeHtml($sectionDescription) ?>">
    <title><?= escapeHtml($sectionLabel) ?> | Student Portal</title>
    <link rel="stylesheet" href="style.css">
    <script src="portal.js" defer></script>
</head>
<body class="welcome-page" data-theme="light">
    <a class="skip-link" href="#portal-main">Skip to main content</a>

    <aside class="portal-sidebar" aria-label="Student Portal navigation">
        <div class="portal-sidebar-top">
            <a class="portal-brand" href="welcome.php" aria-label="Student Portal dashboard">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 32 32"><path d="M16 3 28 9.5v2.3L16 18 4 11.8V9.5L16 3Z" fill="currentColor"/><path d="M8 14.3v6.2c0 2.5 3.6 5 8 5s8-2.5 8-5v-6.2l-8 4.2-8-4.2Z" fill="currentColor" opacity=".72"/><path d="M28 11.1v7.4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"/></svg>
                </span>
                <span>Student Portal</span>
            </a>
            <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Use dark theme" aria-pressed="false">
                <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M18 6.3l1.4-1.4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/></svg>
                <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 15.4A8.4 8.4 0 0 1 8.6 4 8.4 8.4 0 1 0 20 15.4Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8"/></svg>
                <span class="theme-toggle-label">Theme</span>
            </button>
        </div>

        <div class="portal-profile-preview">
            <span class="portal-avatar" aria-hidden="true"><?= escapeHtml($initials) ?></span>
            <span class="portal-profile-preview-copy">
                <strong><?= escapeHtml($displayName) ?></strong>
                <span>@<?= escapeHtml($username) ?></span>
            </span>
        </div>

        <nav class="portal-nav" aria-label="Portal sections">
            <?php foreach ($sections as $key => $details): ?>
                <a href="welcome.php?section=<?= escapeHtml($key) ?>"<?= $key === $section ? ' aria-current="page"' : '' ?>>
                    <span class="nav-icon" aria-hidden="true">
                        <?php if ($key === 'dashboard'): ?>
                            <svg viewBox="0 0 24 24"><rect x="4" y="4" width="6" height="6" rx="1.2" fill="none" stroke="currentColor" stroke-width="1.8"/><rect x="14" y="4" width="6" height="6" rx="1.2" fill="none" stroke="currentColor" stroke-width="1.8"/><rect x="4" y="14" width="6" height="6" rx="1.2" fill="none" stroke="currentColor" stroke-width="1.8"/><rect x="14" y="14" width="6" height="6" rx="1.2" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                        <?php elseif ($key === 'student-information'): ?>
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/></svg>
                        <?php elseif ($key === 'account'): ?>
                            <svg viewBox="0 0 24 24"><path d="M12 3 5 6v5c0 4.4 2.8 8.3 7 10 4.2-1.7 7-5.6 7-10V6l-7-3Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8"/><path d="m9 12 2 2 4-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>
                        <?php else: ?>
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M9.8 9a2.4 2.4 0 1 1 3.9 1.8c-1 .8-1.7 1.2-1.7 2.7m0 3.1h.01" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/></svg>
                        <?php endif; ?>
                    </span>
                    <span><?= escapeHtml($details['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="portal-sidebar-footer">
            <p class="portal-session-status"><span class="portal-status-dot" aria-hidden="true"></span>Session active</p>
            <a class="portal-logout" href="logout.php">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 5H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4M14 8l4 4-4 4m4-4H9" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>
                <span>Sign out</span>
            </a>
        </div>
    </aside>

    <main id="portal-main" class="portal-content" tabindex="-1" aria-labelledby="page-heading">
        <header class="portal-topbar">
            <div>
                <p class="portal-kicker">Student workspace</p>
                <h1 id="page-heading"><?= escapeHtml($sectionLabel) ?></h1>
                <p><?= escapeHtml($sectionDescription) ?></p>
            </div>
            <div class="portal-topbar-actions">
                <time class="portal-date" datetime="<?= date('Y-m-d') ?>"><?= escapeHtml($today) ?></time>
                <div class="portal-user-chip">
                    <span class="portal-avatar" aria-hidden="true"><?= escapeHtml($initials) ?></span>
                    <span class="portal-user-chip-copy">
                        <strong><?= escapeHtml($displayName) ?></strong>
                        <small>@<?= escapeHtml($username) ?></small>
                    </span>
                </div>
            </div>
        </header>

        <div class="portal-layout">
            <?php if ($section === 'dashboard'): ?>
                <section class="portal-card portal-welcome-card" aria-labelledby="welcome-heading">
                    <div class="portal-welcome-content">
                        <p class="portal-welcome-label"><?= escapeHtml($greeting) ?>, <?= escapeHtml($displayName) ?>.</p>
                        <h2 id="welcome-heading">Your student hub is ready.</h2>
                        <p>Keep your account details close, review your next steps, and move through your portal with confidence.</p>
                        <div class="portal-welcome-actions">
                            <a class="portal-button portal-button-light" href="welcome.php?section=student-information">Review my details <span aria-hidden="true">→</span></a>
                            <a class="portal-text-button portal-text-button-light" href="welcome.php?section=account">Account security <span aria-hidden="true">↗</span></a>
                        </div>
                    </div>
                    <div class="portal-welcome-visual" aria-hidden="true">
                        <span class="portal-visual-orbit portal-visual-orbit-large"></span>
                        <span class="portal-visual-orbit portal-visual-orbit-small"></span>
                        <span class="portal-visual-spark portal-visual-spark-one"></span>
                        <span class="portal-visual-spark portal-visual-spark-two"></span>
                    </div>
                </section>

                <section class="portal-stats" aria-label="Account overview">
                    <article class="portal-card stat-card">
                        <div class="stat-card-topline">
                            <span class="stat-icon stat-icon-primary" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/></svg>
                            </span>
                            <span class="stat-status <?= $profileComplete ? 'stat-status-success' : '' ?>"><?= escapeHtml($profileStatus) ?></span>
                        </div>
                        <span class="stat-label">Profile completion</span>
                        <strong class="stat-value"><?= $profileCompletion ?>%</strong>
                        <div class="stat-progress" aria-hidden="true"><span style="width: <?= $profileCompletion ?>%"></span></div>
                    </article>
                    <article class="portal-card stat-card">
                        <div class="stat-card-topline">
                            <span class="stat-icon stat-icon-success" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M12 3 5 6v5c0 4.4 2.8 8.3 7 10 4.2-1.7 7-5.6 7-10V6l-7-3Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8"/><path d="m9 12 2 2 4-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>
                            </span>
                            <span class="stat-status stat-status-success">Active</span>
                        </div>
                        <span class="stat-label">Account status</span>
                        <strong class="stat-value">Good to go</strong>
                        <p class="stat-caption">Your portal access is available.</p>
                    </article>
                    <article class="portal-card stat-card">
                        <div class="stat-card-topline">
                            <span class="stat-icon stat-icon-warm" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M7 10V7a5 5 0 0 1 10 0v3M5 10h14v10H5V10Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8"/><path d="M12 14v2" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/></svg>
                            </span>
                            <span class="stat-status stat-status-success">Protected</span>
                        </div>
                        <span class="stat-label">Portal access</span>
                        <strong class="stat-value">Secure sign-in</strong>
                        <p class="stat-caption">Remember to sign out on shared devices.</p>
                    </article>
                </section>

                <div class="portal-dashboard-grid">
                    <section class="portal-card" aria-labelledby="quick-actions-heading">
                        <div class="card-heading">
                            <div>
                                <p class="card-kicker">Shortcuts</p>
                                <h2 id="quick-actions-heading">Quick actions</h2>
                            </div>
                            <span class="card-heading-mark" aria-hidden="true">✦</span>
                        </div>
                        <div class="quick-actions">
                            <a class="quick-action" href="welcome.php?section=student-information">
                                <span class="quick-action-icon quick-action-icon-blue" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/></svg></span>
                                <span><strong>View student details</strong><small>Check your name and username</small></span>
                                <span class="quick-action-arrow" aria-hidden="true">→</span>
                            </a>
                            <a class="quick-action" href="welcome.php?section=account">
                                <span class="quick-action-icon quick-action-icon-green" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3 5 6v5c0 4.4 2.8 8.3 7 10 4.2-1.7 7-5.6 7-10V6l-7-3Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8"/><path d="m9 12 2 2 4-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg></span>
                                <span><strong>Review account security</strong><small>Keep your access protected</small></span>
                                <span class="quick-action-arrow" aria-hidden="true">→</span>
                            </a>
                            <a class="quick-action" href="welcome.php?section=about">
                                <span class="quick-action-icon quick-action-icon-gold" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M9.8 9a2.4 2.4 0 1 1 3.9 1.8c-1 .8-1.7 1.2-1.7 2.7m0 3.1h.01" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/></svg></span>
                                <span><strong>Explore the portal</strong><small>Learn what is available here</small></span>
                                <span class="quick-action-arrow" aria-hidden="true">→</span>
                            </a>
                        </div>
                    </section>

                    <section class="portal-card" aria-labelledby="getting-started-heading">
                        <div class="card-heading">
                            <div>
                                <p class="card-kicker">Next steps</p>
                                <h2 id="getting-started-heading">Getting started</h2>
                            </div>
                            <span class="card-heading-count"><?= $profileCompletion ?>%</span>
                        </div>
                        <ol class="checklist">
                            <li class="checklist-item <?= $profileComplete ? 'is-complete' : '' ?>">
                                <span class="checklist-indicator" aria-hidden="true"><?= $profileComplete ? '✓' : '1' ?></span>
                                <span class="checklist-copy"><strong>Complete your details</strong><small><?= $profileComplete ? 'Your personal information is ready.' : 'Add your first and last name.' ?></small></span>
                                <a href="welcome.php?section=student-information" aria-label="Review student details">View</a>
                            </li>
                            <li class="checklist-item is-complete">
                                <span class="checklist-indicator" aria-hidden="true">✓</span>
                                <span class="checklist-copy"><strong>Secure your access</strong><small>Your password is stored securely.</small></span>
                                <a href="welcome.php?section=account" aria-label="Review account security">View</a>
                            </li>
                            <li class="checklist-item">
                                <span class="checklist-indicator" aria-hidden="true">3</span>
                                <span class="checklist-copy"><strong>Explore your workspace</strong><small>See what your portal can do.</small></span>
                                <a href="welcome.php?section=about" aria-label="Explore the portal">Open</a>
                            </li>
                        </ol>
                    </section>
                </div>

                <section class="portal-card portal-tip-card" aria-label="Security reminder">
                    <span class="portal-tip-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M9 18h6m-5 3h4M8.4 14.8a6 6 0 1 1 7.2 0c-.9.7-1.6 1.5-1.6 2.2h-4.6c0-.7-.7-1.5-1.6-2.2Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg></span>
                    <span><strong>A small security habit goes a long way.</strong> Sign out whenever you use a shared or public computer.</span>
                    <a class="portal-text-button" href="welcome.php?section=account">View account tips <span aria-hidden="true">→</span></a>
                </section>

            <?php elseif ($section === 'student-information'): ?>
                <section class="portal-card portal-profile-card" aria-labelledby="profile-heading">
                    <div class="portal-profile-heading">
                        <span class="portal-profile-avatar" aria-hidden="true"><?= escapeHtml($initials) ?></span>
                        <div>
                            <p class="card-kicker">Personal profile</p>
                            <h2 id="profile-heading"><?= escapeHtml($displayName) ?></h2>
                            <p>@<?= escapeHtml($username) ?></p>
                        </div>
                        <span class="portal-badge <?= $profileComplete ? 'portal-badge-success' : '' ?>"><?= escapeHtml($profileStatus) ?></span>
                    </div>
                </section>

                <div class="portal-info-grid">
                    <section class="portal-card" aria-labelledby="personal-details-heading">
                        <div class="card-heading">
                            <div>
                                <p class="card-kicker">Your record</p>
                                <h2 id="personal-details-heading">Personal details</h2>
                            </div>
                        </div>
                        <dl class="info-list">
                            <div class="info-row"><dt>First name</dt><dd><?= escapeHtml($firstName !== '' ? $firstName : 'Not provided') ?></dd></div>
                            <div class="info-row"><dt>Last name</dt><dd><?= escapeHtml($lastName !== '' ? $lastName : 'Not provided') ?></dd></div>
                            <div class="info-row"><dt>Username</dt><dd><?= escapeHtml($username) ?></dd></div>
                        </dl>
                    </section>

                    <section class="portal-card" aria-labelledby="profile-progress-heading">
                        <div class="card-heading">
                            <div>
                                <p class="card-kicker">Profile health</p>
                                <h2 id="profile-progress-heading">Profile completeness</h2>
                            </div>
                            <strong class="profile-progress-value"><?= $profileCompletion ?>%</strong>
                        </div>
                        <progress class="profile-progress" value="<?= $profileCompletion ?>" max="100" aria-label="Profile completeness: <?= $profileCompletion ?> percent"></progress>
                        <p class="profile-progress-copy"><?= $profileComplete ? 'Everything currently available is filled in.' : 'Add the missing name details to make your profile easier to identify.' ?></p>
                        <a class="portal-button portal-button-secondary" href="welcome.php?section=account">Review account settings <span aria-hidden="true">→</span></a>
                    </section>
                </div>

            <?php elseif ($section === 'account'): ?>
                <div class="portal-info-grid">
                    <section class="portal-card" aria-labelledby="account-details-heading">
                        <div class="card-heading">
                            <div>
                                <p class="card-kicker">Access details</p>
                                <h2 id="account-details-heading">Your account</h2>
                            </div>
                            <span class="portal-badge portal-badge-success">Active</span>
                        </div>
                        <dl class="info-list">
                            <div class="info-row"><dt>Username</dt><dd class="copy-field"><span><?= escapeHtml($username) ?></span><button class="copy-button" type="button" data-copy-value="<?= escapeHtml($username) ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="8" y="8" width="11" height="11" rx="2" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.7"/></svg><span class="copy-button-label">Copy</span></button></dd></div>
                            <div class="info-row"><dt>Password</dt><dd><span class="inline-status"><span class="portal-status-dot" aria-hidden="true"></span>Protected</span></dd></div>
                            <div class="info-row"><dt>Session</dt><dd><span class="inline-status"><span class="portal-status-dot" aria-hidden="true"></span>Signed in now</span></dd></div>
                        </dl>
                        <p id="copy-status" class="copy-status" role="status" aria-live="polite"></p>
                    </section>

                    <section class="portal-card" aria-labelledby="security-heading">
                        <div class="card-heading">
                            <div>
                                <p class="card-kicker">Stay protected</p>
                                <h2 id="security-heading">Security checklist</h2>
                            </div>
                            <span class="card-heading-mark card-heading-mark-success" aria-hidden="true">✓</span>
                        </div>
                        <ul class="security-list">
                            <li><span class="security-list-icon" aria-hidden="true">✓</span><span><strong>Use a private password</strong><small>Never share your password with anyone.</small></span></li>
                            <li><span class="security-list-icon" aria-hidden="true">✓</span><span><strong>Check the address bar</strong><small>Only sign in from your trusted portal URL.</small></span></li>
                            <li><span class="security-list-icon" aria-hidden="true">✓</span><span><strong>Sign out on shared devices</strong><small>End your session before you leave.</small></span></li>
                        </ul>
                    </section>
                </div>

                <section class="portal-card portal-action-card" aria-label="Sign out">
                    <div>
                        <p class="card-kicker">Finished for now?</p>
                        <h2>Leave your account safely.</h2>
                        <p>Sign out to close your current portal session.</p>
                    </div>
                    <a class="portal-button portal-button-secondary" href="logout.php">Sign out <span aria-hidden="true">→</span></a>
                </section>

            <?php else: ?>
                <section class="portal-card portal-about-hero" aria-labelledby="about-heading">
                    <span class="about-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3 5 6v5c0 4.4 2.8 8.3 7 10 4.2-1.7 7-5.6 7-10V6l-7-3Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8"/><path d="m9 12 2 2 4-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg></span>
                    <p class="card-kicker">Built for your next step</p>
                    <h2 id="about-heading">A focused home for your student account.</h2>
                    <p>The Student Portal keeps your core account information in one clear, easy-to-use place.</p>
                </section>

                <section class="portal-feature-grid" aria-label="Portal features">
                    <article class="portal-card portal-feature-card">
                        <span class="feature-card-icon feature-card-icon-blue" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/></svg></span>
                        <h2>Your information</h2>
                        <p>Review the personal details connected to your account whenever you need them.</p>
                    </article>
                    <article class="portal-card portal-feature-card">
                        <span class="feature-card-icon feature-card-icon-green" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3 5 6v5c0 4.4 2.8 8.3 7 10 4.2-1.7 7-5.6 7-10V6l-7-3Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8"/><path d="m9 12 2 2 4-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg></span>
                        <h2>Secure access</h2>
                        <p>Use your own sign-in and remember to sign out when you use a shared device.</p>
                    </article>
                    <article class="portal-card portal-feature-card">
                        <span class="feature-card-icon feature-card-icon-gold" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/><path d="M6.5 3H20v18H6.5A2.5 2.5 0 0 1 4 18.5v-13A2.5 2.5 0 0 1 6.5 3Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.8"/></svg></span>
                        <h2>Clear next steps</h2>
                        <p>Use the dashboard shortcuts to find the information you need with fewer clicks.</p>
                    </article>
                </section>

                <section class="portal-card portal-help-card" aria-labelledby="help-heading">
                    <div>
                        <p class="card-kicker">Need help?</p>
                        <h2 id="help-heading">Does something look incorrect?</h2>
                        <p>If your details need updating, contact your school or portal administrator for assistance.</p>
                    </div>
                    <a class="portal-button portal-button-secondary" href="welcome.php?section=student-information">Review details <span aria-hidden="true">→</span></a>
                </section>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
