<?php
// Načítanie jedálneho lístka z JSON
$_dataDir = is_dir(__DIR__ . '/data') ? __DIR__ . '/data' : __DIR__ . '/studienka_admin_final/data';
$_jdFile = $_dataDir . '/jedalen.json';
$_jd = file_exists($_jdFile) ? (json_decode(file_get_contents($_jdFile), true) ?: []) : [];

// PDF URL pre zobrazenie (relatívna cesta z rootu webu)
$_pdfUrl = (!empty($_jd['filename']))
    ? 'pdf/jedalnelistky/' . rawurlencode($_jd['filename'])
    : null;

function _jd_size(int $b): string {
    if ($b >= 1048576) return round($b / 1048576, 1) . ' MB';
    if ($b >= 1024)    return round($b / 1024) . ' kB';
    return $b . ' B';
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jedálny lístok – CSS Studienka</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <style>
    :root {
      --navy:    #1A4A96;
      --teal:    #4A6DB8;
      --teal-lt: #7A9ED4;
      --gold:    #E07818;
      --green:   #7AB020;
      --cream:   #FAFAF7;
      --white:   #ffffff;
      --gray-lt: #F2F0EB;
      --gray-md: #8A8A82;
      --text:    #252520;
      --radius:  8px;
      --shadow:  0 4px 24px rgba(30,48,85,.09);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: 'Source Sans 3', sans-serif; color: var(--text); background: var(--cream); line-height: 1.7; }
    a { color: inherit; text-decoration: none; }

    /* ── HLAVIČKA ── */
    header { background: #2B6CC8; color: var(--white); position: sticky; top: 0; z-index: 200; box-shadow: 0 4px 20px rgba(0,0,0,.3); }
    .header-top { background: #7AB020; text-align: center; font-size: .85rem; font-weight: 500; letter-spacing: .04em; padding: .45rem 1rem; color: rgba(255,255,255,.95); }
    .header-top a { color: inherit; }
    .header-main { max-width: 1400px; margin: 0 auto; display: flex; align-items: center; justify-content: flex-start; gap: 1rem; padding: .5rem 2rem; }
    .logo { display: flex; align-items: center; gap: 1.1rem; flex-shrink: 0; text-decoration: none; }
    .logo-icon { width: 110px; height: 110px; flex-shrink: 0; overflow: hidden; }
    .logo-text h1 { font-family: 'Playfair Display', serif; font-size: 2.1rem; font-weight: 800; line-height: 1.15; color: var(--white); }
    .logo-text .logo-sub { font-size: 1.3rem; font-weight: 600; color: var(--white); display: block; }
    nav { display: flex; align-items: center; gap: .1rem; flex-wrap: nowrap; position: relative; }
    nav > a, nav > .nav-item > a { font-size: .8rem; font-weight: 500; letter-spacing: .02em; padding: .45rem .5rem; border-radius: var(--radius); transition: background .2s, color .2s; color: rgba(255,255,255,.88); white-space: nowrap; display: flex; align-items: center; gap: .25rem; }
    nav > a:hover, nav > .nav-item > a:hover { background: rgba(255,255,255,.12); color: var(--white); }
    nav > a.active, nav > .nav-item > a.active { background: #E07818; color: var(--white); }
    .nav-item { position: relative; }
    .nav-item > a::after { content: '▾'; font-size: .65rem; opacity: .7; margin-left: .1rem; }
    .dropdown { display: none; position: absolute; top: 100%; left: 0; background: var(--navy); border: 1px solid rgba(255,255,255,.12); border-radius: var(--radius); min-width: 230px; box-shadow: 0 8px 32px rgba(0,0,0,.35); z-index: 300; padding: .4rem 0; margin-top: 0; }
    .dropdown::before { content: ''; position: absolute; top: -8px; left: 0; width: 100%; height: 8px; background: transparent; }
    .nav-item:hover .dropdown, .nav-item:focus-within .dropdown { display: block; }
    .dropdown a { display: block; padding: .55rem 1.1rem; font-size: .85rem; color: rgba(255,255,255,.82); transition: background .15s, color .15s; white-space: nowrap; }
    .dropdown a:hover { background: rgba(255,255,255,.09); color: var(--white); }
    .dropdown a.active { color: #E07818; font-weight: 600; }
    .hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: .5rem; border: none; background: transparent; z-index: 200; }
    .hamburger span { display: block; width: 26px; height: 2px; background: var(--white); border-radius: 2px; transition: transform .3s, opacity .3s; }
    .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
    .hamburger.open span:nth-child(2) { opacity: 0; }
    .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
    .mobile-nav { display: none; flex-direction: column; background: var(--navy); border-top: 1px solid rgba(255,255,255,.1); max-height: 85vh; overflow-y: auto; }
    .mobile-nav.open { display: flex; }
    .mobile-nav > a { font-size: .95rem; font-weight: 500; color: rgba(255,255,255,.85); padding: .75rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.06); transition: color .2s; display: block; }
    .mobile-nav > a:hover, .mobile-nav > a.active { color: #E07818; }
    .mob-group { border-bottom: 1px solid rgba(255,255,255,.06); }
    .mob-group-btn { width: 100%; background: none; border: none; cursor: pointer; display: flex; align-items: center; justify-content: space-between; padding: .75rem 1.5rem; color: rgba(255,255,255,.85); font-size: .95rem; font-weight: 500; font-family: inherit; transition: color .2s; text-align: left; }
    .mob-group-btn:hover, .mob-group-btn.open { color: #E07818; }
    .mob-group-btn .mob-arrow { font-size: .65rem; transition: transform .25s; opacity: .7; }
    .mob-group-btn.open .mob-arrow { transform: rotate(180deg); }
    .mob-sub-list { display: none; background: rgba(0,0,0,.2); }
    .mob-sub-list.open { display: block; }
    .mob-sub-list a { display: block; font-size: .88rem; color: rgba(255,255,255,.65); padding: .6rem 1.5rem .6rem 2.25rem; transition: color .2s; border-bottom: 1px solid rgba(255,255,255,.04); }
    .mob-sub-list a:last-child { border-bottom: none; }
    .mob-sub-list a:hover, .mob-sub-list a.active { color: #E07818; }

    /* ── PÄTIČKA ── */
    footer { background: #1A3478; color: rgba(255,255,255,.8); }
    .footer-main { max-width: 1200px; margin: 0 auto; padding: 3.5rem 2rem 2rem; display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 3rem; }
    .footer-brand h3 { font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--white); margin-bottom: .4rem; }
    .footer-brand p { font-size: .88rem; line-height: 1.7; }
    .footer-col h4 { color: var(--white); font-size: .82rem; letter-spacing: .08em; text-transform: uppercase; font-weight: 700; margin-bottom: 1rem; padding-bottom: .5rem; border-bottom: 2px solid #E07818; display: inline-block; }
    .footer-col ul { list-style: none; }
    .footer-col ul li { margin-bottom: .55rem; }
    .footer-col ul li a { font-size: .88rem; transition: color .2s; }
    .footer-col ul li a:hover { color: #E07818; }
    .footer-bottom { border-top: 1px solid rgba(255,255,255,.1); text-align: center; padding: 1.25rem 2rem; font-size: .8rem; color: rgba(255,255,255,.4); }

    /* ── BREADCRUMB ── */
    .breadcrumb { background: var(--white); border-bottom: 1px solid var(--gray-lt); padding: .75rem 2rem; }
    .breadcrumb-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; gap: .5rem; font-size: .83rem; color: var(--gray-md); }
    .breadcrumb-inner a { color: var(--teal); transition: color .2s; }
    .breadcrumb-inner a:hover { color: var(--navy); }

    /* ── PAGE HERO ── */
    .page-hero { background: linear-gradient(135deg, #13316E 0%, #2B57BB 55%, #3d5fad 100%); color: var(--white); padding: 3.5rem 2rem; text-align: center; position: relative; overflow: hidden; }
    .page-hero::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
    .page-hero-inner { position: relative; }
    .page-hero h2 { font-family: 'Playfair Display', serif; font-size: clamp(1.6rem, 4vw, 2.4rem); font-weight: 700; margin-bottom: .5rem; }
    .page-hero p { font-size: .97rem; color: rgba(255,255,255,.75); }

    /* ── OBSAH JEDÁLNE ── */
    .page-body { max-width: 900px; margin: 3rem auto 5rem; padding: 0 2rem; }

    .jedalen-card { background: var(--white); border-radius: 12px; box-shadow: var(--shadow); overflow: hidden; }

    .jedalen-card-header { background: var(--navy); color: var(--white); padding: 1.25rem 1.75rem; display: flex; align-items: center; gap: .85rem; }
    .jedalen-card-header svg { width: 28px; height: 28px; fill: var(--gold); flex-shrink: 0; }
    .jedalen-card-header h3 { font-family: 'Playfair Display', serif; font-size: 1.15rem; font-weight: 600; }

    .jedalen-card-body { padding: 2rem 1.75rem; }

    /* Prázdny stav */
    .jedalen-empty { text-align: center; padding: 3rem 2rem; }
    .jedalen-empty svg { width: 56px; height: 56px; fill: #ccc; margin: 0 auto 1.25rem; display: block; }
    .jedalen-empty h4 { font-family: 'Playfair Display', serif; font-size: 1.15rem; color: var(--navy); margin-bottom: .5rem; }
    .jedalen-empty p { color: var(--gray-md); font-size: .93rem; }

    /* Aktívny lístok — info pruh */
    .pdf-meta { display: flex; align-items: center; gap: 1rem; background: var(--gray-lt); border-radius: var(--radius); padding: .85rem 1.25rem; margin-bottom: 1.75rem; flex-wrap: wrap; }
    .pdf-meta-icon { width: 44px; height: 44px; background: #fdecea; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .pdf-meta-icon svg { width: 24px; height: 24px; fill: #c0392b; }
    .pdf-meta-info { flex: 1; min-width: 0; }
    .pdf-meta-info strong { display: block; font-size: .93rem; font-weight: 600; color: var(--navy); }
    .pdf-meta-info small { font-size: .78rem; color: var(--gray-md); }
    .pdf-meta-actions { display: flex; gap: .6rem; flex-wrap: wrap; }

    .btn-primary { display: inline-flex; align-items: center; gap: .45rem; background: var(--teal); color: var(--white); font-size: .88rem; font-weight: 600; padding: .55rem 1.25rem; border-radius: var(--radius); transition: background .2s; }
    .btn-primary:hover { background: var(--navy); }
    .btn-primary svg { width: 16px; height: 16px; fill: var(--white); }
    .btn-secondary { display: inline-flex; align-items: center; gap: .45rem; background: var(--white); color: var(--navy); font-size: .88rem; font-weight: 600; padding: .55rem 1.25rem; border-radius: var(--radius); border: 1.5px solid #ddd; transition: border-color .2s, color .2s; }
    .btn-secondary:hover { border-color: var(--teal); color: var(--teal); }
    .btn-secondary svg { width: 16px; height: 16px; fill: currentColor; }

    /* iframe wrapper */
    .pdf-viewer { border: 1.5px solid var(--gray-lt); border-radius: var(--radius); overflow: hidden; }
    .pdf-viewer iframe { display: block; width: 100%; height: 70vh; min-height: 500px; border: none; }

    /* Info banner */
    .info-banner { background: linear-gradient(90deg, #3d5fad 0%, #6b8fd6 100%); color: var(--white); border-radius: 12px; padding: 1.1rem 1.5rem; display: flex; align-items: center; gap: 1rem; margin-top: 1.5rem; }
    .info-banner svg { width: 22px; height: 22px; fill: rgba(255,255,255,.85); flex-shrink: 0; }
    .info-banner p { font-size: .87rem; opacity: .95; }

    @media (max-width: 1100px) { nav > .nav-item > a, nav > a { font-size: .8rem; padding: .45rem .55rem; } }
    @media (max-width: 900px) { nav { display: none; } .hamburger { display: flex; } .header-main { padding: .85rem 1.25rem; } }
    @media (max-width: 1024px) {
      nav { display: none; } .hamburger { display: flex; } .header-main { padding: .85rem 1.25rem; }
      .page-body { padding: 0 1.25rem; margin: 2rem auto 3.5rem; }
      .footer-main { grid-template-columns: 1fr 1fr; } .footer-brand { grid-column: 1 / -1; }
    }
    @media (max-width: 640px) {
      .footer-main { grid-template-columns: 1fr; gap: 2rem; padding: 2.5rem 1.5rem 1.5rem; }
      .pdf-meta { flex-direction: column; align-items: flex-start; }
      .info-banner { flex-direction: column; gap: .5rem; }
    }
  </style>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

<!-- ═══════ HLAVIČKA ═══════ -->
<header>
  <div class="header-top">
    Otvorené: Po–Pia 07:00–15:30 &nbsp;|&nbsp; Tel: 043/559 01 97 &nbsp;|&nbsp;
    <a href="mailto:css.studienka@vucilina.sk">css.studienka@vucilina.sk</a>
  </div>
  <div class="header-main">
    <a href="domov.html" class="logo">
      <div class="logo-icon">
        <img src="logo/logo.png" alt="CSS Studienka" style="width:110px;height:110px;object-fit:contain;display:block;mix-blend-mode:screen;" />
      </div>
      <div class="logo-text">
        <h1>Centrum sociálnych služieb<br><span class="logo-sub">STUDIENKA Novoť</span></h1>
      </div>
    </a>
    <nav>
      <a href="domov.html">Domov</a>
      <div class="nav-item">
        <a href="onas.html">O nás</a>
        <div class="dropdown">
          <a href="onas.html">O zariadení</a>
          <a href="onas.html#historia">História</a>
          <a href="onas.html#nastim">Náš tím</a>
          <a href="onas.html#poslanie">Poslanie a vízia</a>
        </div>
      </div>
      <div class="nav-item">
        <a href="nasesluzby.php">Naše služby</a>
        <div class="dropdown">
          <a href="nasesluzby.php">Ponúkame</a>
          <a href="nasesluzby.php#zariadenie">Zariadenie pre seniorov</a>
          <a href="nasesluzby.php#dss">Domov soc. služieb</a>
          <a href="nasesluzby.php#specializovane">Špecializované zariadenie</a>
          <a href="nasesluzby.php#denne">Denné centrum</a>
          <a href="nasesluzby.php#jedalen">Jedáleň</a>
          <a href="nasesluzby.php#preprava">Prepravná služba</a>
          <a href="nasesluzby.php#odlahcovacia">Odľahčovacia služba</a>
        </div>
      </div>
      <div class="nav-item">
        <a href="informacieprevas.php">Pre záujemcov</a>
        <div class="dropdown">
          <a href="informacieprevas.php">Ako začať</a>
          <a href="informacieprevas.php#podmienky">Podmienky prijatia</a>
          <a href="informacieprevas.php#cennik">Cenník služieb</a>
          <a href="informacieprevas.php#volne-miesta">Voľné miesta</a>
          <a href="informacieprevas.php#ziadatel">Ako podať žiadosť</a>
          <a href="informacieprevas.php#ekonomicky">Ekon. oprávnené náklady</a>
        </div>
      </div>
      <div class="nav-item">
        <a href="prerodiny.html">Pre rodiny</a>
        <div class="dropdown">
          <a href="prerodiny.html">Informácie pre príbuzných</a>
          <a href="prerodiny.html#navstevy">Návštevy</a>
          <a href="prerodiny.html#otvaracie">Otvárací čas</a>
          <a href="prerodiny.html#vychadzky">Vychádzky</a>
          <a href="prerodiny.html#pobyt">Pobyt mimo zariadenia</a>
        </div>
      </div>
      <div class="nav-item">
        <a href="aktuality.php">Aktuality</a>
        <div class="dropdown">
          <a href="aktuality.php">Všetky aktuality</a>
          <a href="galeria.php">Fotogaléria</a>
        </div>
      </div>
      <a href="nastiahnutie.php">Na stiahnutie</a>
      <a href="povinnezverejnovanie.php">Povinné zverejňovanie</a>
      <a href="oz-pelikan.php">OZ Pelikán</a>
      <a href="kontakt.html">Kontakt</a>
    </nav>
    <button class="hamburger" id="hamburger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
  <div class="mobile-nav" id="mobileNav">
    <a href="domov.html">Domov</a>
    <div class="mob-group">
      <button class="mob-group-btn">O nás<span class="mob-arrow">▾</span></button>
      <div class="mob-sub-list">
        <a href="onas.html">O zariadení</a>
        <a href="onas.html#historia">História</a>
        <a href="onas.html#nastim">Náš tím</a>
        <a href="onas.html#poslanie">Poslanie a vízia</a>
      </div>
    </div>
    <div class="mob-group">
      <button class="mob-group-btn">Naše služby<span class="mob-arrow">▾</span></button>
      <div class="mob-sub-list">
        <a href="nasesluzby.php">Ponúkame</a>
        <a href="nasesluzby.php#zariadenie">Zariadenie pre seniorov</a>
        <a href="nasesluzby.php#dss">Domov soc. služieb</a>
        <a href="nasesluzby.php#specializovane">Špecializované zariadenie</a>
        <a href="nasesluzby.php#denne">Denné centrum</a>
        <a href="nasesluzby.php#jedalen">Jedáleň</a>
        <a href="nasesluzby.php#preprava">Prepravná služba</a>
        <a href="nasesluzby.php#odlahcovacia">Odľahčovacia služba</a>
      </div>
    </div>
    <div class="mob-group">
      <button class="mob-group-btn">Pre záujemcov<span class="mob-arrow">▾</span></button>
      <div class="mob-sub-list">
        <a href="informacieprevas.php">Ako začať</a>
        <a href="informacieprevas.php#podmienky">Podmienky prijatia</a>
        <a href="informacieprevas.php#cennik">Cenník služieb</a>
        <a href="informacieprevas.php#volne-miesta">Voľné miesta</a>
        <a href="informacieprevas.php#ziadatel">Ako podať žiadosť</a>
        <a href="informacieprevas.php#ekonomicky">Ekon. oprávnené náklady</a>
      </div>
    </div>
    <div class="mob-group">
      <button class="mob-group-btn">Pre rodiny<span class="mob-arrow">▾</span></button>
      <div class="mob-sub-list">
        <a href="prerodiny.html">Informácie pre príbuzných</a>
        <a href="prerodiny.html#navstevy">Návštevy</a>
        <a href="prerodiny.html#otvaracie">Otvárací čas</a>
        <a href="prerodiny.html#vychadzky">Vychádzky</a>
        <a href="prerodiny.html#pobyt">Pobyt mimo zariadenia</a>
      </div>
    </div>
    <div class="mob-group">
      <button class="mob-group-btn">Aktuality<span class="mob-arrow">▾</span></button>
      <div class="mob-sub-list">
        <a href="aktuality.php">Všetky aktuality</a>
        <a href="galeria.php">Fotogaléria</a>
      </div>
    </div>
    <a href="nastiahnutie.php">Na stiahnutie</a>
    <a href="povinnezverejnovanie.php">Povinné zverejňovanie</a>
    <a href="oz-pelikan.php">OZ Pelikán</a>
    <a href="kontakt.html">Kontakt</a>
  </div>
</header>

<!-- ═══════ BREADCRUMB ═══════ -->
<div class="breadcrumb">
  <div class="breadcrumb-inner">
    <a href="domov.html">Domov</a>
    <span>›</span>
    <a href="nasesluzby.php">Naše služby</a>
    <span>›</span>
    <span>Jedálny lístok</span>
  </div>
</div>

<!-- ═══════ HERO ═══════ -->
<div class="page-hero">
  <div class="page-hero-inner">
    <h2>Jedálny lístok</h2>
    <p>Týždenný jedálny lístok našej jedálne</p>
  </div>
</div>

<!-- ═══════ OBSAH ═══════ -->
<div class="page-body">
  <div class="jedalen-card">
    <div class="jedalen-card-header">
      <svg viewBox="0 0 24 24"><path d="M18.06 22.99h1.66c.84 0 1.53-.64 1.63-1.46L23 5.05h-5V1h-1.97v4.05h-4.97l.3 2.34c1.71.47 3.31 1.32 4.27 2.26 1.44 1.42 2.43 2.89 2.43 5.29v8.05zM1 21.99V21h15.03v.99c0 .55-.45 1-1.01 1H2.01c-.56 0-1.01-.45-1.01-1zm15.03-7c0-8-15.03-8-15.03 0h15.03zM1.02 17h15v2h-15z"/></svg>
      <h3>Týždenný jedálny lístok</h3>
    </div>
    <div class="jedalen-card-body">
<?php if ($_pdfUrl): ?>
      <!-- Metainformácie o lístku -->
      <div class="pdf-meta">
        <div class="pdf-meta-icon">
          <svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg>
        </div>
        <div class="pdf-meta-info">
          <strong><?= htmlspecialchars($_jd['label'] ?? 'Jedálny lístok') ?></strong>
          <small>
            <?php if (!empty($_jd['week'])): ?>Týždeň: <?= htmlspecialchars($_jd['week']) ?> &nbsp;·&nbsp; <?php endif; ?>
            Nahraté: <?= htmlspecialchars(substr($_jd['uploaded'] ?? '', 0, 10)) ?>
            <?php if (!empty($_jd['size'])): ?>&nbsp;·&nbsp; <?= _jd_size((int)$_jd['size']) ?><?php endif; ?>
          </small>
        </div>
        <div class="pdf-meta-actions">
          <a href="<?= htmlspecialchars($_pdfUrl) ?>" target="_blank" rel="noopener" class="btn-primary">
            <svg viewBox="0 0 24 24"><path d="M19 19H5V5h7V3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
            Otvoriť PDF
          </a>
          <a href="<?= htmlspecialchars($_pdfUrl) ?>" download class="btn-secondary">
            <svg viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
            Stiahnuť
          </a>
        </div>
      </div>

      <!-- PDF Viewer -->
      <div class="pdf-viewer">
        <iframe src="<?= htmlspecialchars($_pdfUrl) ?>#toolbar=0" title="Jedálny lístok"></iframe>
      </div>

      <div class="info-banner">
        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        <p>Ak sa PDF nezobrazuje priamo, použite tlačidlo <strong>Otvoriť PDF</strong> alebo <strong>Stiahnuť</strong>.</p>
      </div>

<?php else: ?>
      <!-- Prázdny stav -->
      <div class="jedalen-empty">
        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
        <h4>Jedálny lístok ešte nie je zverejnený</h4>
        <p>Jedálny lístok pre tento týždeň ešte nebol zverejnený.<br>Skúste neskôr alebo nás kontaktujte telefonicky.</p>
      </div>
<?php endif; ?>
    </div>
  </div>
</div>

<!-- ═══════ PÄTIČKA ═══════ -->
<footer>
  <div class="footer-main">
    <div class="footer-brand">
      <h3>Centrum sociálnych služieb STUDIENKA Novoť</h3>
      <p>Sme v zriaďovateľskej pôsobnosti Žilinského samosprávneho kraja. Poskytujeme sociálne služby v Novoti od roku 1989 s rešpektom k dôstojnosti každého prijímateľa.</p>
      <div style="display:flex;align-items:center;gap:1.25rem;margin-top:13px;"><img src="foto/erb.png" alt="Erb ZSK" style="height:130px;opacity:.85;vertical-align:middle;" /><img src="logo/logo.png" alt="Logo CSS Studienka" style="height:130px;mix-blend-mode:screen;opacity:.85;vertical-align:middle;" /></div>
    </div>
    <div class="footer-col">
      <h4>Navigácia</h4>
      <ul>
        <li><a href="domov.html">Domov</a></li>
        <li><a href="nasesluzby.php">Ponúkame</a></li>
        <li><a href="informacieprevas.php">Pre záujemcov</a></li>
        <li><a href="prerodiny.html">Pre rodiny</a></li>
        <li><a href="aktuality.php">Aktuality</a></li>
        <li><a href="povinnezverejnovanie.php">Povinné zverejňovanie</a></li>
        <li><a href="onas.html">O nás</a></li>
        <li><a href="nastiahnutie.php">Na stiahnutie</a></li>
        <li><a href="kontakt.html">Kontakt</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Kontakt</h4>
      <ul>
        <li>Novoť 976, 029 55</li>
        <li><a href="tel:+421435590197">043 / 559 01 97</a></li>
        <li><a href="tel:+421435590287">043 / 559 02 87</a></li>
        <li><a href="tel:+421948985019">0948 985 019</a></li>
        <li><a href="mailto:css.studienka@vucilina.sk">css.studienka@vucilina.sk</a></li>
        <li style="margin-top:.75rem; padding-top:.75rem; border-top:1px solid rgba(255,255,255,.1);"><a href="https://www.osobnyudaj.sk/informovanie/00632830" target="_blank" rel="noopener">Ochrana osobných údajov</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    &copy; 2025 Centrum sociálnych služieb STUDIENKA Novoť &nbsp;·&nbsp; Všetky práva vyhradené
  </div>
</footer>
<script>
(function() {
  var hbg = document.getElementById('hamburger');
  var mNav = document.getElementById('mobileNav');
  if (hbg && mNav) {
    hbg.addEventListener('click', function(e) {
      e.stopPropagation();
      hbg.classList.toggle('open');
      mNav.classList.toggle('open');
    });
    mNav.querySelectorAll('a').forEach(function(a) {
      a.addEventListener('click', function() {
        hbg.classList.remove('open');
        mNav.classList.remove('open');
      });
    });
    document.addEventListener('click', function(e) {
      if (!hbg.contains(e.target) && !mNav.contains(e.target)) {
        hbg.classList.remove('open');
        mNav.classList.remove('open');
      }
    });
  }
  document.querySelectorAll('.mob-group-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var isOpen = btn.classList.contains('open');
      document.querySelectorAll('.mob-group-btn').forEach(function(b) {
        b.classList.remove('open');
        b.nextElementSibling.classList.remove('open');
      });
      if (!isOpen) {
        btn.classList.add('open');
        btn.nextElementSibling.classList.add('open');
      }
    });
  });
  if ('ontouchstart' in window) {
    document.querySelectorAll('.nav-item > a').forEach(function(a) {
      a.addEventListener('click', function(e) {
        var dd = a.parentElement.querySelector('.dropdown');
        if (!dd) return;
        var visible = dd.style.display === 'block';
        document.querySelectorAll('.nav-item .dropdown').forEach(function(d) { d.style.display = ''; });
        if (!visible) { e.preventDefault(); dd.style.display = 'block'; }
      });
    });
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.nav-item'))
        document.querySelectorAll('.nav-item .dropdown').forEach(function(d) { d.style.display = ''; });
    });
  }
})();
</script>
</body>
</html>
