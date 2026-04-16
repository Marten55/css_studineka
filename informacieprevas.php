<?php
$_dataDir = is_dir(__DIR__ . '/data') ? __DIR__ . '/data' : __DIR__ . '/studienka_admin_final/data';
$_cnFile = $_dataDir . '/cennik.json';
$_cn = file_exists($_cnFile) ? (json_decode(file_get_contents($_cnFile), true) ?: []) : [];
$_cnUrl = !empty($_cn['filename']) ? 'pdf/cennik/' . $_cn['filename'] : null;
?>
<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Informácie pre Vás – CSS Studienka</title>
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
    .logo-text .tagline { font-size: .8rem; color: #E07818; letter-spacing: .15em; text-transform: uppercase; font-weight: 700; display: block; margin-top: .25rem; }
    /* ── NAV s dropdown ── */
    nav { display: flex; align-items: center; gap: .1rem; flex-wrap: nowrap; position: relative; }
    nav > a, nav > .nav-item > a {
      font-size: .8rem; font-weight: 500; letter-spacing: .02em;
      padding: .45rem .5rem; border-radius: var(--radius);
      transition: background .2s, color .2s;
      color: rgba(255,255,255,.88); white-space: nowrap;
      display: flex; align-items: center; gap: .25rem;
    }
    nav > a:hover, nav > .nav-item > a:hover { background: rgba(255,255,255,.12); color: var(--white); }
    nav > a.active, nav > .nav-item > a.active { background: #E07818; color: var(--white); }
    .nav-item { position: relative; }
    .nav-item > a::after { content: '▾'; font-size: .65rem; opacity: .7; margin-left: .1rem; }
    .dropdown {
      display: none; position: absolute; top: 100%; left: 0;
      background: var(--navy); border: 1px solid rgba(255,255,255,.12);
      border-radius: var(--radius); min-width: 230px;
      box-shadow: 0 8px 32px rgba(0,0,0,.35); z-index: 300;
      padding: .4rem 0 .4rem;
      margin-top: 0;
    }
    .dropdown::before {
      content: ''; position: absolute; top: -8px; left: 0;
      width: 100%; height: 8px; background: transparent;
    }
    .nav-item:hover .dropdown,
    .nav-item:focus-within .dropdown { display: block; }
    .dropdown a {
      display: block; padding: .55rem 1.1rem;
      font-size: .85rem; color: rgba(255,255,255,.82);
      transition: background .15s, color .15s; white-space: nowrap;
    }
    .dropdown a:hover { background: rgba(255,255,255,.09); color: var(--white); }
    .dropdown a.active { color: #E07818; font-weight: 600; }
    .dropdown-divider { height: 1px; background: rgba(255,255,255,.08); margin: .3rem 0; }
    /* ── HAMBURGER ── */
    .hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: .5rem; border: none; background: transparent; z-index: 200; }
    .hamburger span { display: block; width: 26px; height: 2px; background: var(--white); border-radius: 2px; transition: transform .3s, opacity .3s; }
    .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
    .hamburger.open span:nth-child(2) { opacity: 0; }
    .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
    /* ── MOBILE NAV ── */
    .mobile-nav { display: none; flex-direction: column; background: var(--navy); border-top: 1px solid rgba(255,255,255,.1); max-height: 85vh; overflow-y: auto; }
    .mobile-nav.open { display: flex; }
    .mobile-nav > a { font-size: .95rem; font-weight: 500; color: rgba(255,255,255,.85); padding: .75rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.06); transition: color .2s; display: block; }
    .mobile-nav > a:hover, .mobile-nav > a.active { color: #E07818; }
    .mob-group { border-bottom: 1px solid rgba(255,255,255,.06); }
    .mob-group-btn {
      width: 100%; background: none; border: none; cursor: pointer;
      display: flex; align-items: center; justify-content: space-between;
      padding: .75rem 1.5rem; color: rgba(255,255,255,.85);
      font-size: .95rem; font-weight: 500; font-family: inherit;
      transition: color .2s; text-align: left;
    }
    .mob-group-btn:hover { color: #E07818; }
    .mob-group-btn.open { color: #E07818; }
    .mob-group-btn .mob-arrow { font-size: .65rem; transition: transform .25s; opacity: .7; }
    .mob-group-btn.open .mob-arrow { transform: rotate(180deg); }
    .mob-sub-list { display: none; background: rgba(0,0,0,.2); }
    .mob-sub-list.open { display: block; }
    .mob-sub-list a { display: block; font-size: .88rem; color: rgba(255,255,255,.65); padding: .6rem 1.5rem .6rem 2.25rem; transition: color .2s; border-bottom: 1px solid rgba(255,255,255,.04); }
    .mob-sub-list a:last-child { border-bottom: none; }
    .mob-sub-list a:hover, .mob-sub-list a.active { color: #E07818; }

    /* ── BREADCRUMB ── */
    .breadcrumb {
      background: var(--white);
      border-bottom: 1px solid var(--gray-lt);
      padding: .75rem 2rem;
    }
    .breadcrumb-inner {
      max-width: 1100px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      gap: .5rem;
      font-size: .83rem;
      color: var(--gray-md);
    }
    .breadcrumb-inner a { color: var(--teal); transition: color .2s; }
    .breadcrumb-inner a:hover { color: var(--navy); }
    .breadcrumb-inner span { color: var(--gray-md); }

    /* ── HERO SEKCIA ── */
    .page-hero {
      background: linear-gradient(135deg, #13316E 0%, #2B57BB 55%, #3d5fad 100%);
      color: var(--white);
      padding: 3.5rem 2rem;
      text-align: center;
    }
    .page-hero h2 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.6rem, 4vw, 2.4rem);
      font-weight: 700;
      margin-bottom: .5rem;
    }
    .page-hero p { font-size: .97rem; color: rgba(255,255,255,.75); }

    /* ── HLAVNÝ OBSAH ── */
    .main-content {
      max-width: 1100px;
      margin: 0 auto;
      padding: 3rem 2rem 5rem;
      display: grid;
      grid-template-columns: 240px 1fr;
      gap: 2.5rem;
      align-items: start;
    }

    /* ── BOČNÝ PANEL (sidebar) ── */
    .sidebar {
      background: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
      position: sticky;
      top: 200px;
    }
    .pdf-category { scroll-margin-top: 210px; }
    .sidebar-title {
      background: var(--navy);
      color: var(--white);
      font-size: .78rem;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      padding: .85rem 1.25rem;
    }
    .sidebar nav { flex-direction: column; gap: 0; align-items: stretch; }
    .sidebar nav a {
      display: block;
      font-size: .87rem;
      font-weight: 500;
      color: var(--text);
      padding: .7rem 1.25rem;
      border-bottom: 1px solid var(--gray-lt);
      border-radius: 0;
      white-space: normal;
      transition: background .15s, color .15s, padding-left .15s;
    }
    .sidebar nav a:last-child { border-bottom: none; }
    .sidebar nav a:hover { background: var(--gray-lt); color: var(--navy); padding-left: 1.6rem; }
    .sidebar nav a.active { background: #E07818; color: var(--white); }
    .sidebar nav a.active:hover { padding-left: 1.25rem; }

    /* ── PDF SEKCIE ── */
    .content-area h2 {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem;
      color: var(--navy);
      margin-bottom: .4rem;
    }
    .content-area .section-line {
      width: 50px; height: 3px;
      background: var(--gold);
      border-radius: 2px;
      margin-bottom: 1.75rem;
    }

    .pdf-category { margin-bottom: 3rem; }
    .pdf-category h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.15rem;
      color: var(--navy);
      margin-bottom: 1rem;
      padding-bottom: .5rem;
      border-bottom: 2px solid var(--gray-lt);
    }

    .pdf-list { display: flex; flex-direction: column; gap: .6rem; }

    .pdf-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      background: var(--white);
      border: 1.5px solid var(--gray-lt);
      border-radius: var(--radius);
      padding: .9rem 1.25rem;
      transition: border-color .2s, box-shadow .2s, transform .2s;
      cursor: pointer;
    }
    .pdf-item:hover {
      border-color: var(--teal);
      box-shadow: 0 4px 16px rgba(45,125,111,.15);
      transform: translateX(3px);
    }

    .pdf-icon {
      width: 40px; height: 40px;
      background: #e8f4f2;
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .pdf-icon svg { width: 22px; height: 22px; fill: var(--teal); }

    .pdf-info { flex: 1; }
    .pdf-info .pdf-name { font-size: .93rem; font-weight: 600; color: var(--navy); line-height: 1.3; }
    .pdf-info .pdf-meta { font-size: .78rem; color: var(--gray-md); margin-top: .15rem; }

    .pdf-arrow { color: var(--teal); opacity: .6; flex-shrink: 0; transition: opacity .2s, transform .2s; }
    .pdf-item:hover .pdf-arrow { opacity: 1; transform: translateX(3px); }
    .pdf-arrow svg { width: 18px; height: 18px; fill: var(--teal); }

    .pdf-empty {
      background: var(--gray-lt);
      border-radius: var(--radius);
      padding: 2rem;
      text-align: center;
      color: var(--gray-md);
      font-size: .9rem;
    }
    .pdf-empty svg { width: 36px; height: 36px; fill: #ccc; margin: 0 auto .75rem; display: block; }

    /* ── PÄTIČKA ── */
    footer { background: #1A3478; color: rgba(255,255,255,.8); }
    .footer-main { max-width: 1200px; margin: 0 auto; padding: 3rem 2rem 1.5rem; display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 3rem; }
    .footer-brand h3 { font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--white); margin-bottom: .4rem; }
    .footer-brand span { font-size: .75rem; color: #E07818; letter-spacing: .06em; text-transform: uppercase; display: block; margin-bottom: 1rem; }
    .footer-brand p { font-size: .88rem; line-height: 1.7; }
    .footer-col h4 { color: var(--white); font-size: .82rem; letter-spacing: .08em; text-transform: uppercase; font-weight: 700; margin-bottom: 1rem; padding-bottom: .5rem; border-bottom: 2px solid #E07818; display: inline-block; }
    .footer-col ul { list-style: none; }
    .footer-col ul li { margin-bottom: .55rem; }
    .footer-col ul li a { font-size: .88rem; transition: color .2s; }
    .footer-col ul li a:hover { color: #E07818; }
    .footer-bottom { border-top: 1px solid rgba(255,255,255,.1); text-align: center; padding: 1.25rem 2rem; font-size: .8rem; color: rgba(255,255,255,.45); }

    /* ── RESPONZÍVNOSŤ ── */
    @media (max-width: 900px) {
      .main-content { grid-template-columns: 1fr; }
      .sidebar { position: static; }
      .footer-main { grid-template-columns: 1fr 1fr; }
      .footer-brand { grid-column: 1 / -1; }
    }
    @media (max-width: 1024px) {
      nav { display: none; }
      .hamburger { display: flex; }
      .header-main { padding: .85rem 1.25rem; }
      .main-content { padding: 2rem 1.25rem 3rem; }
      .footer-main { grid-template-columns: 1fr; gap: 2rem; padding: 2.5rem 1.5rem 1.5rem; }
      .footer-brand { grid-column: auto; }
    }

    /* ── INFO SEKCIE ── */
    .info-text { font-size: .95rem; color: var(--text); line-height: 1.75; margin-bottom: 1.25rem; }
    .info-text p { margin-bottom: .85rem; }
    .info-text ul { margin: .5rem 0 1rem 1.25rem; }
    .info-text ul li { margin-bottom: .4rem; }
    .info-text strong { color: var(--navy); }
    .info-box {
      background: #f5f8ff; border-left: 3px solid var(--teal);
      border-radius: 0 var(--radius) var(--radius) 0;
      padding: 1rem 1.25rem; margin: 1rem 0 1.5rem; font-size: .92rem;
    }
    .info-box strong { color: var(--navy); }
    .cap-table { width: 100%; border-collapse: collapse; margin: 1rem 0 1.5rem; font-size: .9rem; }
    .cap-table th { background: var(--navy); color: var(--white); text-align: left; padding: .6rem .9rem; font-weight: 600; font-size: .82rem; letter-spacing: .04em; }
    .cap-table td { padding: .55rem .9rem; border-bottom: 1px solid var(--gray-lt); }
    .cap-table tr:last-child td { border-bottom: none; }
    .cap-table tr:nth-child(even) td { background: var(--gray-lt); }
    .cap-badge { display: inline-block; background: var(--teal); color: var(--white); font-size: .75rem; font-weight: 700; padding: .2rem .65rem; border-radius: 2rem; }
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
        <a href="informacieprevas.php" class="active">Pre záujemcov</a>
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
        <a href="aktuality.html">Aktuality</a>
        <div class="dropdown">
        <a href="aktuality.html">Všetky aktuality</a>
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
      <a href="informacieprevas.php" class="active">Ako začať</a>
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
      <a href="aktuality.html">Všetky aktuality</a>
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
    <span>Informácie pre Vás</span>
  </div>
</div>

<!-- ═══════ HERO ═══════ -->
<div class="page-hero">
  <h2>Informácie pre Vás</h2>
  <p>Všetky dôležité dokumenty a informácie na jednom mieste</p>
</div>

<!-- ═══════ HLAVNÝ OBSAH ═══════ -->
<div class="main-content">

  <!-- Bočný panel -->
  <aside class="sidebar">
    <div class="sidebar-title">Kategórie</div>
    <nav>
      <a href="#cennik" class="active">Cenník za poskytované služby</a>
      <a href="#ekonomicky">Ekonomicky oprávnené náklady</a>
      <a href="#volne-miesta">Informácie o voľných miestach</a>
      <a href="#pribuzni">Informácie pre príbuzných</a>
      <a href="#ziadatel">Informácie pre žiadateľa</a>
      <a href="#podmienky">Podmienky prijatia do zariadenia</a>
      <a href="#rozpocet">Rozpočet na rok</a>
      <a href="#hospodarenie">Hospodárenie</a>
      <a href="#majetok">Informácie o majetku ŽSK</a>
    </nav>
  </aside>

  <!-- Obsah -->
  <div class="content-area">
    <h2>Dokumenty a informácie</h2>
    <div class="section-line"></div>

    <!-- Cenník -->
    <div class="pdf-category" id="cennik">
      <h3>Cenník za poskytované služby</h3>
      <div class="info-text">
        <p>Výška úhrady za poskytované sociálne služby je stanovená v súlade so zákonom č. 448/2008 Z. z. o sociálnych službách a všeobecne záväzným nariadením Žilinského samosprávneho kraja.</p>
        <p><strong>Úhrada za sociálnu službu</strong> závisí od druhu poskytovanej služby, rozsahu úkonov a príjmu prijímateľa. Prijímateľ je povinný platiť úhradu maximálne do výšky 100 % príjmu. Zostatok príjmu nesmie klesnúť pod zákonom stanovenú hranicu.</p>
        <div class="info-box">
          <strong>Kontakt pre informácie o cenníku:</strong><br>
          Mgr. Daniela Kuchťáková — sociálny pracovník<br>
          Tel: 043/559 01 97 (kl. 105) · <a href="mailto:css.studienka@vucilina.sk">css.studienka@vucilina.sk</a>
        </div>
      </div>
      <div class="pdf-list">
        <?php if ($_cnUrl): ?>
        <a class="pdf-item" href="<?= htmlspecialchars($_cnUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name"><?= htmlspecialchars($_cn['label'] ?? 'Cenník za poskytované služby', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="pdf-meta">PDF dokument<?= !empty($_cn['year']) ? ' · ' . htmlspecialchars($_cn['year'], ENT_QUOTES, 'UTF-8') : '' ?></div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
        <?php else: ?>
        <div class="pdf-empty">
          <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
          <p>Cenník ešte nebol zverejnený.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Ekonomicky oprávnené náklady -->
    <div class="pdf-category" id="ekonomicky">
      <h3>Ekonomicky oprávnené náklady</h3>
      <div class="info-text">
        <p>Ekonomicky oprávnené náklady (EON) sú náklady, ktoré sú nevyhnutné na zabezpečenie sociálnej služby. Slúžia ako podklad pre určenie výšky úhrady a sú každoročne zverejňované v súlade so zákonom.</p>
      </div>
      <div class="pdf-list">
        <a class="pdf-item" href="pdf/ekonomicky-opravnene-naklady-2024.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">EON 2024</div>
            <div class="pdf-meta">PDF dokument</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
        <a class="pdf-item" href="pdf/ekonomicky-opravnene-naklady-2023.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">EON 2023</div>
            <div class="pdf-meta">PDF dokument</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
        <a class="pdf-item" href="pdf/ekonomicky-opravnene-naklady-archiv.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">Archív EON</div>
            <div class="pdf-meta">PDF dokument</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
      </div>
    </div>

    <!-- Voľné miesta -->
    <div class="pdf-category" id="volne-miesta">
      <h3>Informácie o voľných miestach</h3>
      <div class="info-text">
        <p>Aktuálny stav obsadenosti a voľných miest v jednotlivých typoch sociálnych služieb CSS STUDIENKA:</p>
        <table class="cap-table">
          <tr><th>Druh sociálnej služby</th><th>Kapacita</th><th>Stav</th></tr>
          <tr><td>Zariadenie pre seniorov (ZpS)</td><td>13 miest</td><td><span class="cap-badge">Aktuálne info v PDF</span></td></tr>
          <tr><td>Domov sociálnych služieb (DSS)</td><td>30 miest</td><td><span class="cap-badge">Aktuálne info v PDF</span></td></tr>
          <tr><td>Špecializované zariadenie (ŠZ)</td><td>59 miest</td><td><span class="cap-badge">Aktuálne info v PDF</span></td></tr>
          <tr><td>Denné centrum (DC)</td><td>10 miest</td><td><span class="cap-badge">Aktuálne info v PDF</span></td></tr>
          <tr><td>Jedáleň</td><td>15 miest</td><td><span class="cap-badge">Aktuálne info v PDF</span></td></tr>
          <tr><td>Prepravná služba</td><td>na požiadanie</td><td><span class="cap-badge">Aktuálne info v PDF</span></td></tr>
        </table>
        <div class="info-box">
          <strong>Záujem o umiestnenie?</strong> Kontaktujte nášho sociálneho pracovníka:<br>
          Mgr. Daniela Kuchťáková · Tel: 043/559 01 97 (kl. 105) · <a href="mailto:css.studienka@vucilina.sk">css.studienka@vucilina.sk</a>
        </div>
      </div>
      <div class="pdf-list">
        <a class="pdf-item" href="pdf/volne-miesta.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">Aktuálny stav voľných miest</div>
            <div class="pdf-meta">PDF dokument</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
      </div>
    </div>

    <!-- Príbuzní -->
    <div class="pdf-category" id="pribuzni">
      <h3>Informácie pre príbuzných</h3>
      <div class="info-text">
        <p>Vítame záujem príbuzných o život ich blízkych v CSS STUDIENKA. Udržiavame otvorený kontakt s rodinnými príslušníkmi a podporujeme pravidelné návštevy.</p>

        <p id="navstevy"><strong>Návštevy</strong></p>
        <p>Návštevy prijímateľov sú možné denne. Odporúčame rešpektovať denný program prijímateľov a vopred sa informovať o aktuálnom zdravotnom stave. V čase epidémií alebo zvýšeného rizika nákazy môžu byť návštevy obmedzené — aktuálne informácie poskytne ošetrujúci personál.</p>

        <p id="otvaracie"><strong>Otváracia a zatváracia doba zariadenia</strong></p>
        <div class="info-box">
          Zariadenie je otvorené <strong>nepretržite 24 hodín denne, 7 dní v týždni</strong>.<br>
          Úradné hodiny (riaditeľstvo, sociálny úsek): <strong>Pondelok – Piatok, 07:00 – 15:30</strong><br>
          Vstup pre návštevníkov: denne, odporúčaný čas 09:00 – 18:00
        </div>

        <p id="pobyt"><strong>Pobyt mimo zariadenia</strong></p>
        <p>Prijímatelia majú právo na pobyt mimo zariadenia. Pobyt mimo zariadenia je potrebné vopred dohodnúť s ošetrujúcim personálom a sociálnym pracovníkom. Počas pobytu mimo zariadenia sa primerane upravuje výška úhrady za sociálnu službu v zmysle platných predpisov.</p>

        <p id="vychadzky"><strong>Vychádzky mimo areál zariadenia</strong></p>
        <p>Prijímatelia majú právo na vychádzky do okolia zariadenia. Vychádzky prebiehajú v sprievode personálu alebo samostatne, v závislosti od zdravotného stavu a schopností prijímateľa. Areál zariadenia s parkom a altánkom je k dispozícii pre relaxáciu kedykoľvek.</p>
      </div>
      <div class="pdf-list">
        <a class="pdf-item" href="pdf/info-pribuzni.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">Informácie pre príbuzných Prijímateľov</div>
            <div class="pdf-meta">PDF dokument</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
      </div>
    </div>

    <!-- Žiadateľ -->
    <div class="pdf-category" id="ziadatel">
      <h3>Informácie pre žiadateľa</h3>
      <div class="info-text">
        <p>Záujem o sociálnu službu v CSS STUDIENKA je možné prejaviť podaním písomnej žiadosti. Pred podaním žiadosti odporúčame kontaktovať nášho sociálneho pracovníka, ktorý Vám poskytne podrobné informácie.</p>

        <p><strong>Kto môže požiadať o sociálnu službu?</strong></p>
        <ul>
          <li>Fyzická osoba, ktorej zdravotný stav alebo sociálna situácia si vyžaduje pomoc</li>
          <li>Zákonný zástupca alebo opatrovník fyzickej osoby</li>
          <li>Osoba blízka so súhlasom fyzickej osoby</li>
        </ul>

        <p><strong>Postup pri podaní žiadosti:</strong></p>
        <ul>
          <li>Kontaktujte sociálneho pracovníka CSS STUDIENKA</li>
          <li>Požiadajte príslušný mestský/obecný úrad o posúdenie odkázanosti na sociálnu službu</li>
          <li>Vyplňte žiadosť o zabezpečenie sociálnej služby a doručte ju do CSS STUDIENKA</li>
          <li>Priložte rozhodnutie o odkázanosti, potvrdenie o príjme a zdravotnú dokumentáciu</li>
        </ul>

        <div class="info-box">
          <strong>Kontakt pre žiadateľov:</strong><br>
          Mgr. Daniela Kuchťáková — sociálny pracovník<br>
          Tel: 043/559 01 97 (kl. 105) · <a href="mailto:css.studienka@vucilina.sk">css.studienka@vucilina.sk</a>
        </div>
      </div>
      <div class="pdf-list">
        <a class="pdf-item" href="pdf/info-ziadatel.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">Informácie pre žiadateľa o umiestnenie</div>
            <div class="pdf-meta">PDF dokument</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
        <a class="pdf-item" href="pdf/tlaciva/ziadost-o-ss.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">Žiadosť o zabezpečenie sociálnej služby</div>
            <div class="pdf-meta">PDF — tlačivo</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
      </div>
    </div>

    <!-- Podmienky prijatia -->
    <div class="pdf-category" id="podmienky">
      <h3>Podmienky prijatia do zariadenia</h3>
      <div class="info-text">
        <p>CSS STUDIENKA poskytuje sociálne služby na základe právoplatného rozhodnutia o odkázanosti a uzatvorenej zmluvy o poskytovaní sociálnej služby.</p>

        <p><strong>Základné podmienky prijatia:</strong></p>
        <ul>
          <li>Právoplatné rozhodnutie o odkázanosti na príslušný druh sociálnej služby vydané príslušným orgánom verejnej moci</li>
          <li>Voľné miesto v požadovanom type sociálnej služby</li>
          <li>Uzatvorenie zmluvy o poskytovaní sociálnej služby</li>
          <li>Predloženie požadovanej dokumentácie (zdravotná dokumentácia, potvrdenie o príjme)</li>
        </ul>

        <p><strong>Druhy poskytovaných sociálnych služieb a podmienky:</strong></p>
        <ul>
          <li><strong>Zariadenie pre seniorov</strong> — osoba v dôchodkovom veku odkázaná na pomoc inej osoby (stupeň IV–VI)</li>
          <li><strong>Domov sociálnych služieb</strong> — dospelá osoba s zdravotným postihnutím odkázaná na pomoc inej osoby (stupeň V–VI)</li>
          <li><strong>Špecializované zariadenie</strong> — osoba s Parkinsonovou, Alzheimerovou chorobou, demenciou, sklerózou multiplex a inými diagnózami (stupeň V–VI)</li>
          <li><strong>Denné centrum</strong> — osoba od 15 rokov s ťažkým zdravotným postihnutím alebo nepriaznivým zdravotným stavom</li>
          <li><strong>Jedáleň</strong> — osoba s ťažkým zdravotným postihnutím alebo v dôchodkovom veku</li>
          <li><strong>Prepravná služba</strong> — osoba s ťažkým zdravotným postihnutím odkázaná na individuálnu prepravu</li>
        </ul>
      </div>
      <div class="pdf-list">
        <a class="pdf-item" href="pdf/podmienky-prijatia.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">Podmienky prijatia do zariadenia</div>
            <div class="pdf-meta">PDF dokument</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
      </div>
    </div>

    <!-- Rozpočet -->
    <div class="pdf-category" id="rozpocet">
      <h3>Rozpočet na rok</h3>
      <div class="pdf-list">
        <a class="pdf-item" href="pdf/rozpocet-2024.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">Rozpočet na rok 2024</div>
            <div class="pdf-meta">PDF dokument</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
        <a class="pdf-item" href="pdf/rozpocet-2023.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">Rozpočet na rok 2023</div>
            <div class="pdf-meta">PDF dokument</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
      </div>
    </div>

    <!-- Hospodárenie -->
    <div class="pdf-category" id="hospodarenie">
      <h3>Hospodárenie</h3>
      <div class="pdf-list">
        <a class="pdf-item" href="pdf/hospodarenie-2024.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">Správa o hospodárení 2024</div>
            <div class="pdf-meta">PDF dokument</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
        <a class="pdf-item" href="pdf/hospodarenie-2023.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">Správa o hospodárení 2023</div>
            <div class="pdf-meta">PDF dokument</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
      </div>
    </div>

    <!-- Majetok ŽSK -->
    <div class="pdf-category" id="majetok">
      <h3>Informácie o majetku ŽSK</h3>
      <div class="pdf-list">
        <a class="pdf-item" href="pdf/majetok-zsk.pdf" target="_blank" rel="noopener">
          <div class="pdf-icon"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/></svg></div>
          <div class="pdf-info">
            <div class="pdf-name">Informácie o majetku ŽSK</div>
            <div class="pdf-meta">PDF dokument</div>
          </div>
          <div class="pdf-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></div>
        </a>
      </div>
    </div>

  </div><!-- /content-area -->
</div><!-- /main-content -->

<!-- ═══════ PÄTIČKA ═══════ -->
<script>
  // ── NAVIGÁCIA ───────────────────────────────
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
    // Mobilný akordeón — exkluzívny
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
    // Touch podpora pre desktop dropdown (tablety)
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

  // ── Scroll highlighting bočného panelu ──
  var sideLinks = document.querySelectorAll('.sidebar nav a[href^="#"]');
  var sections  = Array.from(sideLinks).map(function (a) {
    return document.querySelector(a.getAttribute('href'));
  }).filter(Boolean);

  function setActive(id) {
    sideLinks.forEach(function (a) {
      a.classList.toggle('active', a.getAttribute('href') === '#' + id);
    });
  }

  // Pri kliknutí — okamžité zvýraznenie
  sideLinks.forEach(function (a) {
    a.addEventListener('click', function () {
      setActive(a.getAttribute('href').slice(1));
    });
  });

  // Pri scrollovaní — IntersectionObserver
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        setActive(entry.target.id);
      }
    });
  }, { rootMargin: '-200px 0px -55% 0px', threshold: 0 });

  sections.forEach(function (s) { observer.observe(s); });
</script>

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
        <li><a href="aktuality.html">Aktuality</a></li>
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

  </div>
  <div class="footer-bottom">
    &copy; 2025 Centrum sociálnych služieb STUDIENKA Novoť &nbsp;·&nbsp; Všetky práva vyhradené
  </div>
</footer>
</body>
</html>
