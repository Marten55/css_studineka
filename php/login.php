<?php
require_once __DIR__ . '/auth.php';

if (isLoggedIn()) { header('Location: index.php'); exit; }

$error = '';
$locked = false;
$timeout = isset($_GET['timeout']);
$loggedOut = isset($_GET['logout']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Neplatný bezpečnostný token. Skúste znova.';
    } elseif (isLockedOut()) {
        $locked = true;
        $error = 'Príliš veľa neúspešných pokusov. Skúste znova za ' . getRemainingLockout() . ' minút.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (login($username, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $attempts = recordFailedAttempt();
            $remaining = MAX_LOGIN_ATTEMPTS - $attempts;
            if ($remaining <= 0) {
                $locked = true;
                $error = 'Príliš veľa neúspešných pokusov. Účet zablokovaný na ' . (LOCKOUT_TIME/60) . ' minút.';
            } else {
                $error = 'Nesprávne meno alebo heslo. Zostáva ' . $remaining . ' ' . ($remaining === 1 ? 'pokus' : ($remaining < 5 ? 'pokusy' : 'pokusov')) . '.';
            }
        }
    }
}
$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="sk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Prihlásenie — Správa webu CSS Studienka</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --primary:      #2a6049;
  --primary-dark: #1a3d2e;
  --accent:       #4a9e6e;
  --accent-light: #c8e6d8;
  --bg:           #f7f3ee;
  --white:        #ffffff;
  --text:         #1c1c1c;
  --muted:        #7a7a7a;
  --border:       #ddd8d0;
  --error:        #b91c1c;
}

body {
  font-family: 'Inter', sans-serif;
  background: var(--bg);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-image:
    radial-gradient(ellipse at 15% 60%, rgba(42,96,73,0.07) 0%, transparent 55%),
    radial-gradient(ellipse at 85% 20%, rgba(74,158,110,0.05) 0%, transparent 50%);
}

.wrap { width: 100%; max-width: 400px; padding: 1.5rem; }

.brand {
  text-align: center;
  margin-bottom: 2rem;
}

.brand-logo {
  width: 72px;
  height: 72px;
  object-fit: contain;
  margin-bottom: 0.9rem;
  filter: drop-shadow(0 4px 12px rgba(42,96,73,0.15));
}

.brand h1 {
  font-family: 'Lora', serif;
  font-size: 1.25rem;
  color: var(--primary-dark);
  line-height: 1.25;
}

.brand p {
  font-size: 0.8rem;
  color: var(--muted);
  margin-top: 0.25rem;
}

.card {
  background: white;
  border-radius: 14px;
  padding: 2.2rem;
  box-shadow: 0 4px 30px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.04);
  border: 1px solid var(--border);
}

.card h2 {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text);
  margin-bottom: 1.5rem;
}

.notice {
  padding: 0.7rem 0.9rem;
  border-radius: 8px;
  font-size: 0.82rem;
  margin-bottom: 1.2rem;
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
}
.notice svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; }
.notice-error   { background: #fef2f2; color: var(--error); border: 1px solid #fca5a5; }
.notice-success { background: #f0fdf4; color: #15803d; border: 1px solid #86efac; }
.notice-info    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

.field { margin-bottom: 1.1rem; }

label {
  display: block;
  font-size: 0.72rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: var(--muted);
  margin-bottom: 0.4rem;
}

input[type=text], input[type=password] {
  width: 100%;
  padding: 0.65rem 0.9rem;
  border: 1.5px solid var(--border);
  border-radius: 8px;
  font-family: 'Inter', sans-serif;
  font-size: 0.9rem;
  color: var(--text);
  background: var(--bg);
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
}

input:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(74,158,110,0.12);
  background: white;
}

input:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-login {
  width: 100%;
  padding: 0.75rem;
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 8px;
  font-family: 'Inter', sans-serif;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  margin-top: 0.4rem;
  box-shadow: 0 3px 10px rgba(42,96,73,0.28);
}
.btn-login:hover:not(:disabled) { background: var(--primary-dark); }
.btn-login:disabled { opacity: 0.5; cursor: not-allowed; }

.back {
  display: block;
  text-align: center;
  margin-top: 1.4rem;
  font-size: 0.82rem;
  color: var(--muted);
  text-decoration: none;
  transition: color 0.15s;
}
.back:hover { color: var(--primary); }

.footer {
  text-align: center;
  margin-top: 1.8rem;
  font-size: 0.74rem;
  color: var(--border);
}
</style>
</head>
<body>
<div class="wrap">
  <div class="brand">
    <img src="<?= LOGO_URL ?>" alt="CSS Studienka" class="brand-logo">
    <h1>Centrum sociálnych služieb<br>STUDIENKA Novoť</h1>
    <p>Správa obsahu webu</p>
  </div>

  <div class="card">
    <h2>Prihlásenie do administrácie</h2>

    <?php if ($timeout): ?>
    <div class="notice notice-info">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
      Relácia vypršala po nečinnosti. Prihláste sa znova.
    </div>
    <?php elseif ($loggedOut): ?>
    <div class="notice notice-success">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
      Boli ste úspešne odhlásení.
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="notice notice-error">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <div class="field">
        <label for="username">Používateľské meno</label>
        <input type="text" id="username" name="username"
               value="<?= sanitize($_POST['username'] ?? '') ?>"
               <?= $locked ? 'disabled' : 'autofocus' ?> required>
      </div>
      <div class="field">
        <label for="password">Heslo</label>
        <input type="password" id="password" name="password"
               <?= $locked ? 'disabled' : '' ?> required>
      </div>
      <button type="submit" class="btn-login" <?= $locked ? 'disabled' : '' ?>>
        Prihlásiť sa
      </button>
    </form>
  </div>

  <a href="../domov.html" class="back">← Späť na web</a>
  <p class="footer">© <?= date('Y') ?> CSS Studienka Novoť</p>
</div>
</body>
</html>
