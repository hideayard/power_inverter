<!DOCTYPE html>
<html lang="ms" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Projek Pasar Malam Hijau – Power Inverter 2.0</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #f4f7f2;
    --sidebar-bg: #1a2e1a;
    --sidebar-text: #c8e6c9;
    --sidebar-active: #4caf50;
    --card-bg: #ffffff;
    --text-primary: #1a2e1a;
    --text-secondary: #5a7a5a;
    --text-muted: #8fa88f;
    --border: #ddeedd;
    --green-main: #2e7d32;
    --green-light: #4caf50;
    --green-pale: #e8f5e9;
    --amber: #f59e0b;
    --red: #ef4444;
    --blue: #3b82f6;
    --purple: #8b5cf6;
    --orange: #f97316;
    --shadow: 0 2px 12px rgba(46,125,50,0.08);
    --shadow-lg: 0 8px 32px rgba(46,125,50,0.12);
    --radius: 14px;
    --radius-sm: 8px;
    --header-bg: #ffffff;
    --topbar-text: #1a2e1a;
    --stat-border: #ddeedd;
  }
  [data-theme="dark"] {
    --bg: #0d1a0d;
    --sidebar-bg: #0a120a;
    --sidebar-text: #7bab7b;
    --sidebar-active: #4caf50;
    --card-bg: #132213;
    --text-primary: #e8f5e9;
    --text-secondary: #8aba8a;
    --text-muted: #567856;
    --border: #1e3a1e;
    --green-main: #66bb6a;
    --green-light: #81c784;
    --green-pale: #1a2e1a;
    --shadow: 0 2px 12px rgba(0,0,0,0.4);
    --shadow-lg: 0 8px 32px rgba(0,0,0,0.5);
    --header-bg: #0f1f0f;
    --topbar-text: #e8f5e9;
    --stat-border: #1e3a1e;
  }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--bg);
    color: var(--text-primary);
    display: flex;
    min-height: 100vh;
    transition: background 0.3s, color 0.3s;
  }

  /* ── SIDEBAR ── */
  .sidebar {
    width: 220px;
    background: var(--sidebar-bg);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 100;
    transition: background 0.3s;
  }
  .sidebar-logo {
    padding: 20px 16px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
  }
  .logo-badge {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .logo-icon {
    width: 42px; height: 42px;
    background: linear-gradient(135deg,#2e7d32,#66bb6a);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
  }
  .logo-text { color: #e8f5e9; font-size: 10.5px; font-weight: 700; line-height: 1.3; }
  .logo-text span { color: #66bb6a; display: block; font-size: 9px; font-weight: 500; margin-top: 2px; }
  .sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }
  .nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 16px; cursor: pointer;
    color: var(--sidebar-text); font-size: 13px; font-weight: 500;
    border-radius: 0; position: relative;
    transition: background 0.2s, color 0.2s;
  }
  .nav-item:hover { background: rgba(76,175,80,0.1); color: #a5d6a7; }
  .nav-item.active {
    background: rgba(76,175,80,0.18);
    color: #a5d6a7;
  }
  .nav-item.active::before {
    content: ''; position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px; background: var(--green-light); border-radius: 0 3px 3px 0;
  }
  .nav-item .nav-icon { font-size: 16px; width: 20px; text-align: center; }
  .nav-badge {
    margin-left: auto; background: #ef4444;
    color: #fff; font-size: 10px; font-weight: 700;
    padding: 1px 6px; border-radius: 10px;
  }
  .sidebar-status {
    padding: 16px;
    border-top: 1px solid rgba(255,255,255,0.06);
  }
  .status-label { color: #567856; font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 10px; }
  .status-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
  .status-dot { width: 8px; height: 8px; border-radius: 50%; background: #4caf50; box-shadow: 0 0 6px #4caf50; }
  .status-text { color: #a5d6a7; font-size: 12px; font-weight: 600; }
  .status-nodes { color: #66bb6a; font-size: 22px; font-weight: 800; font-family: 'JetBrains Mono', monospace; }
  .status-sub { color: #567856; font-size: 11px; }
  .sidebar-partners { padding: 12px 16px; border-top: 1px solid rgba(255,255,255,0.06); }
  .partners-label { color: #567856; font-size: 9px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 8px; }
  .partner-logos { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
  .partner-pill {
    background: rgba(76,175,80,0.12); color: #7bab7b;
    font-size: 9px; font-weight: 700; padding: 3px 7px; border-radius: 6px;
    letter-spacing: 0.04em;
  }

  /* ── MAIN ── */
  .main {
    margin-left: 220px;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  /* ── TOPBAR ── */
  .topbar {
    background: var(--header-bg);
    border-bottom: 1px solid var(--border);
    padding: 0 28px;
    height: 60px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 50;
    transition: background 0.3s;
  }
  .topbar-title { display: flex; align-items: center; gap: 10px; }
  .topbar-title h1 {
    font-size: 15px; font-weight: 600; color: var(--topbar-text);
    letter-spacing: -0.01em;
  }
  .topbar-title h1 strong { color: var(--green-main); font-weight: 800; }
  .topbar-subtitle {
    font-size: 10.5px; color: var(--text-muted); margin-top: 1px;
    display: flex; align-items: center; gap: 6px;
  }
  .topbar-subtitle svg { opacity: 0.6; }
  .topbar-right { display: flex; align-items: center; gap: 12px; }
  .online-badge {
    background: #dcfce7; color: #16a34a;
    font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px;
    display: flex; align-items: center; gap: 5px;
  }
  [data-theme="dark"] .online-badge { background: rgba(22,163,74,0.2); color: #4ade80; }
  .online-badge::before { content: ''; width: 6px; height: 6px; background: #16a34a; border-radius: 50%; animation: pulse 2s infinite; }
  @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
  .notif-btn {
    width: 34px; height: 34px; border-radius: 8px;
    background: var(--green-pale); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    position: relative; font-size: 15px; transition: background 0.2s;
  }
  .notif-btn:hover { background: var(--border); }
  .notif-dot {
    position: absolute; top: 4px; right: 4px;
    width: 14px; height: 14px; background: #ef4444; border-radius: 50%;
    font-size: 9px; font-weight: 700; color: #fff;
    display: flex; align-items: center; justify-content: center;
  }
  .admin-pill {
    display: flex; align-items: center; gap: 7px;
    background: var(--green-pale); border-radius: 20px; padding: 4px 12px 4px 5px;
    cursor: pointer; transition: background 0.2s;
  }
  .admin-pill:hover { background: var(--border); }
  .admin-avatar {
    width: 26px; height: 26px; border-radius: 50%;
    background: linear-gradient(135deg,#2e7d32,#66bb6a);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 11px; font-weight: 700;
  }
  .admin-name { font-size: 12px; font-weight: 600; color: var(--topbar-text); }
  .theme-toggle {
    width: 34px; height: 34px; border-radius: 8px;
    background: var(--green-pale); border: 1px solid var(--border);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: 16px; transition: background 0.2s, transform 0.2s;
  }
  .theme-toggle:hover { transform: rotate(20deg); }
  .last-updated { font-size: 10px; color: var(--text-muted); white-space: nowrap; }

  /* ── CONTENT ── */
  .content { padding: 24px 28px; flex: 1; }

  /* ── HERO BANNER ── */
  .hero-banner {
    background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 50%, #388e3c 100%);
    border-radius: var(--radius);
    padding: 24px 32px;
    margin-bottom: 20px;
    display: flex; align-items: center; justify-content: space-between;
    overflow: hidden; position: relative;
    box-shadow: var(--shadow-lg);
  }
  .hero-banner::before {
    content: '';
    position: absolute; top: -40px; right: 200px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,0.04); border-radius: 50%;
  }
  .hero-text h2 {
    color: #fff; font-size: 22px; font-weight: 800;
    line-height: 1.25; letter-spacing: -0.02em;
  }
  .hero-text h2 span { color: #a5d6a7; }
  .hero-text p { color: rgba(255,255,255,0.65); font-size: 11px; margin-top: 6px; }
  .hero-pills { display: flex; gap: 8px; margin-top: 14px; flex-wrap: wrap; }
  .hero-pill {
    background: rgba(255,255,255,0.12); color: #c8e6c9;
    font-size: 10.5px; font-weight: 600; padding: 5px 12px; border-radius: 20px;
    display: flex; align-items: center; gap: 5px;
    backdrop-filter: blur(4px);
  }
  .hero-icon { font-size: 64px; opacity: 0.9; }

  /* ── STAT CARDS ── */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4,1fr);
    gap: 16px;
    margin-bottom: 20px;
  }
  .stat-card {
    background: var(--card-bg);
    border: 1px solid var(--stat-border);
    border-radius: var(--radius);
    padding: 18px 20px;
    box-shadow: var(--shadow);
    transition: background 0.3s, transform 0.2s;
    position: relative; overflow: hidden;
  }
  .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
  .stat-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 10px; }
  .stat-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 18px;
  }
  .stat-icon.blue { background: #eff6ff; }
  .stat-icon.green { background: #f0fdf4; }
  .stat-icon.orange { background: #fff7ed; }
  .stat-icon.purple { background: #faf5ff; }
  [data-theme="dark"] .stat-icon.blue { background: rgba(59,130,246,0.15); }
  [data-theme="dark"] .stat-icon.green { background: rgba(76,175,80,0.15); }
  [data-theme="dark"] .stat-icon.orange { background: rgba(249,115,22,0.15); }
  [data-theme="dark"] .stat-icon.purple { background: rgba(139,92,246,0.15); }
  .stat-label { font-size: 10px; font-weight: 700; color: var(--text-muted); letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 4px; }
  .stat-value { font-size: 26px; font-weight: 800; color: var(--text-primary); font-family: 'JetBrains Mono', monospace; letter-spacing: -0.03em; }
  .stat-value .unit { font-size: 14px; font-weight: 600; color: var(--text-secondary); }
  .stat-sub { font-size: 11px; color: var(--text-muted); margin-top: 4px; }
  .stat-change {
    font-size: 11px; font-weight: 600; margin-top: 4px;
    display: flex; align-items: center; gap: 3px;
  }
  .stat-change.up { color: #16a34a; }
  .stat-change.down { color: #ef4444; }
  .stat-online { color: #16a34a; font-size: 11px; font-weight: 700; margin-top: 2px; }

  /* ── TWO COL SECTION ── */
  .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
  .three-col { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 20px; }

  /* ── CARD ── */
  .card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    box-shadow: var(--shadow);
    transition: background 0.3s;
  }
  .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
  .card-title { font-size: 13px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 7px; }
  .card-title .icon { font-size: 16px; }
  .card-badge {
    font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 10px;
  }
  .badge-green { background: #dcfce7; color: #15803d; }
  .badge-orange { background: #fff7ed; color: #c2410c; }
  [data-theme="dark"] .badge-green { background: rgba(21,128,61,0.2); color: #4ade80; }
  [data-theme="dark"] .badge-orange { background: rgba(194,65,12,0.2); color: #fb923c; }
  .card-action { font-size: 11px; color: var(--green-main); font-weight: 600; cursor: pointer; text-decoration: none; }
  .card-action:hover { text-decoration: underline; }

  /* ── CO2 SECTION ── */
  .co2-big { font-size: 32px; font-weight: 800; color: var(--text-primary); font-family: 'JetBrains Mono', monospace; letter-spacing: -0.04em; }
  .co2-sub { font-size: 11px; color: var(--text-muted); margin-top: 3px; display: flex; align-items: center; gap: 4px; }
  .co2-sub.down { color: #ef4444; }

  /* ── CHART PLACEHOLDER ── */
  .chart-wrap { width: 100%; height: 110px; position: relative; margin-top: 10px; }
  .chart-svg { width: 100%; height: 100%; }

  /* ── BAR CHART ── */
  .bar-chart { display: flex; align-items: flex-end; gap: 12px; height: 100px; margin-top: 12px; }
  .bar-group { display: flex; flex-direction: column; align-items: center; gap: 5px; flex: 1; }
  .bar {
    width: 100%; border-radius: 6px 6px 0 0;
    transition: opacity 0.2s;
    cursor: pointer;
  }
  .bar:hover { opacity: 0.8; }
  .bar-label { font-size: 10px; color: var(--text-muted); font-weight: 500; }
  .bar-val { font-size: 10px; color: var(--text-primary); font-weight: 700; font-family: 'JetBrains Mono', monospace; }

  /* ── MAP PLACEHOLDER ── */
  .map-wrap {
    background: linear-gradient(135deg,#e8f5e9 0%,#c8e6c9 100%);
    border-radius: 10px; height: 220px;
    position: relative; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
  }
  [data-theme="dark"] .map-wrap { background: linear-gradient(135deg,#1a2e1a 0%,#0d1a0d 100%); }
  .map-grid {
    position: absolute; inset: 0;
    background-image:
      linear-gradient(rgba(46,125,50,0.1) 1px, transparent 1px),
      linear-gradient(90deg,rgba(46,125,50,0.1) 1px, transparent 1px);
    background-size: 28px 28px;
  }
  .map-node {
    position: absolute;
    width: 24px; height: 24px; border-radius: 50%;
    background: #2e7d32; border: 3px solid #fff;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 9px; font-weight: 700;
    box-shadow: 0 2px 8px rgba(46,125,50,0.4);
    cursor: pointer; transition: transform 0.2s;
  }
  .map-node:hover { transform: scale(1.2); }
  .map-node.warn { background: #f59e0b; }
  .map-tip { font-size: 11px; color: var(--text-muted); margin-top: 10px; display: flex; align-items: center; gap: 5px; }

  /* ── NODE TABLE ── */
  .node-table { width: 100%; border-collapse: collapse; font-size: 12px; }
  .node-table th {
    text-align: left; padding: 8px 10px;
    font-size: 10px; font-weight: 700; color: var(--text-muted);
    letter-spacing: 0.06em; text-transform: uppercase;
    border-bottom: 1px solid var(--border);
  }
  .node-table td { padding: 9px 10px; border-bottom: 1px solid var(--border); color: var(--text-primary); }
  .node-table tr:last-child td { border-bottom: none; }
  .node-table tr:hover td { background: var(--green-pale); }
  .status-online { color: #16a34a; font-weight: 600; display: flex; align-items: center; gap: 4px; }
  .status-warn { color: #d97706; font-weight: 600; display: flex; align-items: center; gap: 4px; }
  .status-online::before { content:''; width:6px;height:6px;border-radius:50%;background:#16a34a; }
  .status-warn::before { content:''; width:6px;height:6px;border-radius:50%;background:#d97706; }
  .mono { font-family: 'JetBrains Mono', monospace; font-size: 11px; }
  .view-all-btn {
    width: 100%; margin-top: 12px; padding: 10px;
    background: linear-gradient(135deg,#2e7d32,#4caf50);
    color: #fff; font-size: 12px; font-weight: 700;
    border: none; border-radius: 8px; cursor: pointer;
    transition: opacity 0.2s;
  }
  .view-all-btn:hover { opacity: 0.9; }

  /* ── TREND CHARTS ── */
  .trend-tabs { display: flex; gap: 4px; }
  .trend-tab {
    font-size: 10px; font-weight: 600; padding: 4px 9px;
    border-radius: 6px; border: none; cursor: pointer;
    background: transparent; color: var(--text-muted); transition: background 0.2s, color 0.2s;
  }
  .trend-tab.active { background: var(--green-pale); color: var(--green-main); }
  [data-theme="dark"] .trend-tab.active { background: rgba(76,175,80,0.15); color: #81c784; }

  /* ── ALERTS ── */
  .alert-table { width: 100%; border-collapse: collapse; font-size: 12px; }
  .alert-table th { font-size: 10px; font-weight: 700; color: var(--text-muted); letter-spacing: 0.06em; text-transform: uppercase; padding: 6px 10px; border-bottom: 1px solid var(--border); text-align: left; }
  .alert-table td { padding: 8px 10px; border-bottom: 1px solid var(--border); color: var(--text-primary); vertical-align: middle; }
  .alert-table tr:last-child td { border-bottom: none; }
  .alert-type {
    font-size: 10px; font-weight: 600; padding: 3px 8px; border-radius: 6px;
    display: inline-flex; align-items: center; gap: 4px;
  }
  .at-battery { background: #fff7ed; color: #c2410c; }
  .at-highload { background: #fef2f2; color: #dc2626; }
  .at-output { background: #fffbeb; color: #d97706; }
  .at-voltage { background: #fefce8; color: #ca8a04; }
  .at-comm { background: #f0f9ff; color: #0369a1; }
  [data-theme="dark"] .at-battery { background: rgba(194,65,12,0.15); color: #fb923c; }
  [data-theme="dark"] .at-highload { background: rgba(220,38,38,0.15); color: #f87171; }
  [data-theme="dark"] .at-output { background: rgba(217,119,6,0.15); color: #fbbf24; }
  [data-theme="dark"] .at-voltage { background: rgba(202,138,4,0.15); color: #fde047; }
  [data-theme="dark"] .at-comm { background: rgba(3,105,161,0.15); color: #38bdf8; }
  .badge-active { background: #fef2f2; color: #dc2626; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 6px; }
  .badge-resolved { background: #f0fdf4; color: #16a34a; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 6px; }
  [data-theme="dark"] .badge-active { background: rgba(220,38,38,0.15); color: #f87171; }
  [data-theme="dark"] .badge-resolved { background: rgba(22,163,74,0.15); color: #4ade80; }

  /* ── INSIGHTS ── */
  .insight-row {
    display: flex; align-items: flex-start; gap: 10px; padding: 10px 0;
    border-bottom: 1px solid var(--border);
  }
  .insight-row:last-child { border-bottom: none; }
  .insight-icon-wrap {
    width: 32px; height: 32px; border-radius: 8px;
    background: var(--green-pale); display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
  }
  .insight-label { font-size: 11px; color: var(--text-muted); font-weight: 500; }
  .insight-val { font-size: 13px; color: var(--text-primary); font-weight: 700; margin-top: 1px; }
  .insight-sub { font-size: 10px; color: var(--text-muted); margin-top: 1px; }
  .insights-cta {
    width: 100%; margin-top: 12px; padding: 10px;
    background: var(--green-pale); color: var(--green-main);
    font-size: 12px; font-weight: 700;
    border: 1px solid var(--border); border-radius: 8px; cursor: pointer;
    transition: background 0.2s;
  }
  .insights-cta:hover { background: var(--border); }

  /* ── FOOTER ── */
  .footer {
    background: var(--card-bg); border-top: 1px solid var(--border);
    padding: 14px 28px; display: flex; justify-content: space-between; align-items: center;
    font-size: 10.5px; color: var(--text-muted);
    transition: background 0.3s;
  }

  /* ── SECTION DIVIDER ── */
  .section-header {
    font-size: 10px; font-weight: 700; color: var(--text-muted);
    letter-spacing: 0.1em; text-transform: uppercase;
    margin-bottom: 12px; margin-top: 4px;
    display: flex; align-items: center; gap: 8px;
  }
  .section-header::after {
    content: ''; flex: 1; height: 1px; background: var(--border);
  }
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-badge">
      <div class="logo-icon">🌿</div>
      <div class="logo-text">
        PASAR MALAM HIJAU
        <span>DIKUASAKAN POWER INVERTER 2.0</span>
      </div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-item active"><span class="nav-icon">📊</span> Dashboard</div>
    <div class="nav-item"><span class="nav-icon">🔍</span> Overview</div>
    <div class="nav-item"><span class="nav-icon">📡</span> All Nodes</div>
    <div class="nav-item"><span class="nav-icon">🗺️</span> Map View</div>
    <div class="nav-item"><span class="nav-icon">📈</span> Analytics</div>
    <div class="nav-item"><span class="nav-icon">🌱</span> CO₂ Dashboard</div>
    <div class="nav-item"><span class="nav-icon">🔔</span> Alerts <span class="nav-badge">5</span></div>
    <div class="nav-item"><span class="nav-icon">📋</span> Reports</div>
    <div class="nav-item"><span class="nav-icon">💾</span> Export Data</div>
    <div class="nav-item"><span class="nav-icon">⚙️</span> Settings</div>
    <div class="nav-item"><span class="nav-icon">👥</span> Users</div>
    <div class="nav-item"><span class="nav-icon">ℹ️</span> About Project</div>
  </nav>
  <div class="sidebar-status">
    <div class="status-label">System Status</div>
    <div class="status-row">
      <div class="status-dot"></div>
      <div class="status-text">All Systems Normal</div>
    </div>
    <div class="status-nodes">15 / 15</div>
    <div class="status-sub">Active Nodes · 100% Online</div>
  </div>
  <div class="sidebar-partners">
    <div class="partners-label">Dikuasakan oleh</div>
    <div class="partner-logos">
      <span class="partner-pill">UTM</span>
      <span class="partner-pill">MBIP</span>
      <span class="partner-pill">SAN ISKANDAR</span>
    </div>
  </div>
</aside>

<!-- MAIN -->
<div class="main">
  <!-- TOPBAR -->
  <header class="topbar">
    <div>
      <div class="topbar-title">
        <h1>PROJEK PASAR MALAM HIJAU <strong>DIKUASAKAN POWER INVERTER 2.0</strong></h1>
      </div>
      <div class="topbar-subtitle">
        🚴 DIBIAYAI OLEH GERAN KOMUNITI | ISKANDAR PUTERI RENDAH KARBON 5.0
      </div>
    </div>
    <div class="topbar-right">
      <div class="last-updated">Last Updated: 14 May 2025, 10:05 PM</div>
      <div class="online-badge">Online</div>
      <button class="notif-btn">🔔<span class="notif-dot">5</span></button>
      <div class="admin-pill">
        <div class="admin-avatar">A</div>
        <span class="admin-name">Admin MBIP</span>
      </div>
      <button class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">🌙</button>
    </div>
  </header>

  <!-- CONTENT -->
  <div class="content">

    <!-- HERO -->
    <div class="hero-banner">
      <div class="hero-text">
        <h2>PASAR MALAM HIJAU<br><span>DIKUASAKAN</span><br>POWER INVERTER</h2>
        <div class="hero-pills">
          <div class="hero-pill">⚡ Tenaga Bersih</div>
          <div class="hero-pill">🌿 Mesra Alam</div>
          <div class="hero-pill">💰 Jimat Kos</div>
        </div>
      </div>
      <div class="hero-icon">🌱</div>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-header">
          <div><div class="stat-label">Total Nodes</div><div class="stat-value">15</div></div>
          <div class="stat-icon blue">📡</div>
        </div>
        <div class="stat-online">✅ 100% Online · 15 / 15 Active</div>
      </div>
      <div class="stat-card">
        <div class="stat-header">
          <div><div class="stat-label">Total Energy Today</div><div class="stat-value">78.62 <span class="unit">kWh</span></div></div>
          <div class="stat-icon green">⚡</div>
        </div>
        <div class="stat-sub">Yesterday: 72.31 kWh</div>
        <div class="stat-change up">▲ 8.72%</div>
      </div>
      <div class="stat-card">
        <div class="stat-header">
          <div><div class="stat-label">Total Cost Today</div><div class="stat-value">RM <span class="unit">138.45</span></div></div>
          <div class="stat-icon orange">💰</div>
        </div>
        <div class="stat-sub">Yesterday: RM 127.20</div>
        <div class="stat-change up">▲ 8.83%</div>
      </div>
      <div class="stat-card">
        <div class="stat-header">
          <div><div class="stat-label">Total CO₂ Reduced</div><div class="stat-value">0.68 <span class="unit">kg</span></div></div>
          <div class="stat-icon purple">🌿</div>
        </div>
        <div class="stat-sub">Lifetime: 1562.34 kg</div>
      </div>
    </div>

    <!-- CO2 SECTION -->
    <div class="two-col">
      <!-- CO2 Line Chart -->
      <div class="card">
        <div class="card-header">
          <div class="card-title"><span class="icon">🌱</span> Total CO₂ Reduction</div>
          <span class="card-badge badge-orange">▼ 12.3% from last month</span>
        </div>
        <div class="co2-big">0.580 <span style="font-size:16px;color:var(--text-muted)">kg</span></div>
        <div class="chart-wrap">
          <svg class="chart-svg" viewBox="0 0 400 110" preserveAspectRatio="none">
            <defs>
              <linearGradient id="lineGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#4caf50" stop-opacity="0.3"/>
                <stop offset="100%" stop-color="#4caf50" stop-opacity="0"/>
              </linearGradient>
            </defs>
            <!-- Grid lines -->
            <line x1="0" y1="20" x2="400" y2="20" stroke="var(--border)" stroke-width="1"/>
            <line x1="0" y1="50" x2="400" y2="50" stroke="var(--border)" stroke-width="1"/>
            <line x1="0" y1="80" x2="400" y2="80" stroke="var(--border)" stroke-width="1"/>
            <!-- Area -->
            <path d="M0,70 C30,65 60,55 90,60 C120,65 150,50 180,45 C210,40 240,55 270,50 C300,45 330,48 360,42 L400,38 L400,110 L0,110 Z" fill="url(#lineGrad)"/>
            <!-- Line -->
            <path d="M0,70 C30,65 60,55 90,60 C120,65 150,50 180,45 C210,40 240,55 270,50 C300,45 330,48 360,42 L400,38" fill="none" stroke="#4caf50" stroke-width="2.5" stroke-linecap="round"/>
            <!-- Dots -->
            <circle cx="0" cy="70" r="4" fill="#4caf50"/>
            <circle cx="57" cy="55" r="4" fill="#4caf50"/>
            <circle cx="114" cy="60" r="4" fill="#4caf50"/>
            <circle cx="171" cy="47" r="4" fill="#4caf50"/>
            <circle cx="228" cy="52" r="4" fill="#4caf50"/>
            <circle cx="285" cy="45" r="4" fill="#4caf50"/>
            <circle cx="342" cy="40" r="4" fill="#4caf50"/>
            <circle cx="400" cy="38" r="4" fill="#4caf50"/>
            <!-- X labels -->
            <text x="0" y="108" fill="var(--text-muted)" font-size="9" font-family="Plus Jakarta Sans">8 May</text>
            <text x="57" y="108" fill="var(--text-muted)" font-size="9" font-family="Plus Jakarta Sans">9 May</text>
            <text x="114" y="108" fill="var(--text-muted)" font-size="9" font-family="Plus Jakarta Sans">10 May</text>
            <text x="171" y="108" fill="var(--text-muted)" font-size="9" font-family="Plus Jakarta Sans">11 May</text>
            <text x="228" y="108" fill="var(--text-muted)" font-size="9" font-family="Plus Jakarta Sans">12 May</text>
            <text x="285" y="108" fill="var(--text-muted)" font-size="9" font-family="Plus Jakarta Sans">13 May</text>
            <text x="342" y="108" fill="var(--text-muted)" font-size="9" font-family="Plus Jakarta Sans">14 May</text>
          </svg>
        </div>
      </div>

      <!-- CO2 Bar Chart -->
      <div class="card">
        <div class="card-header">
          <div class="card-title"><span class="icon">🌿</span> CO₂ Reduction</div>
          <a class="card-action" href="#">↗ Trend</a>
        </div>
        <div class="co2-big">0.68 <span style="font-size:16px;color:var(--text-muted)">kg</span></div>
        <div class="co2-sub">🌍 Lifetime savings</div>
        <div class="bar-chart">
          <div class="bar-group">
            <div class="bar-val">0.68</div>
            <div class="bar" style="height:24px;background:#9e9e9e;"></div>
            <div class="bar-label">Week</div>
          </div>
          <div class="bar-group">
            <div class="bar-val">5.42</div>
            <div class="bar" style="height:45px;background:#9e9e9e;"></div>
            <div class="bar-label">Month</div>
          </div>
          <div class="bar-group">
            <div class="bar-val">28.76</div>
            <div class="bar" style="height:65px;background:#9e9e9e;"></div>
            <div class="bar-label">Year</div>
          </div>
          <div class="bar-group">
            <div class="bar-val">1562</div>
            <div class="bar" style="height:90px;background:linear-gradient(180deg,#4caf50,#2e7d32);"></div>
            <div class="bar-label">All Time</div>
          </div>
        </div>
      </div>
    </div>

    <!-- MAP + NODE TABLE -->
    <div class="two-col">
      <!-- Map -->
      <div class="card">
        <div class="card-header">
          <div class="card-title"><span class="icon">📍</span> Inverter Locations (Johor, Malaysia)</div>
          <span class="card-badge badge-green">Minimize</span>
        </div>
        <div class="map-wrap">
          <div class="map-grid"></div>
          <div class="map-node" style="left:20%;top:35%">1</div>
          <div class="map-node" style="left:35%;top:20%">2</div>
          <div class="map-node" style="left:50%;top:28%">3</div>
          <div class="map-node" style="left:65%;top:38%">4</div>
          <div class="map-node warn" style="left:25%;top:58%">5</div>
          <div class="map-node" style="left:40%;top:50%">6</div>
          <div class="map-node" style="left:55%;top:62%">7</div>
          <div class="map-node" style="left:70%;top:55%">8</div>
          <div class="map-node" style="left:18%;top:75%">9</div>
          <div class="map-node" style="left:45%;top:72%">10</div>
          <div class="map-node" style="left:60%;top:78%">11</div>
          <div class="map-node" style="left:75%;top:70%">12</div>
          <div class="map-node" style="left:30%;top:85%">13</div>
          <div class="map-node" style="left:50%;top:85%">14</div>
          <div class="map-node" style="left:68%;top:85%">15</div>
        </div>
        <div class="map-tip">ℹ️ Click on any inverter marker to view details</div>
      </div>

      <!-- Node Table -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">⚙️ All Nodes Overview</div>
          <span class="card-badge badge-green">15 Nodes</span>
        </div>
        <table class="node-table">
          <thead>
            <tr>
              <th>ID</th><th>Name</th><th>Status</th>
              <th>Power (W)</th><th>Voltage (V)</th><th>Current (A)</th><th>Energy (kWh)</th>
            </tr>
          </thead>
          <tbody>
            <tr><td class="mono">1</td><td>Inverter 1</td><td><span class="status-online">Online</span></td><td class="mono">235.6</td><td class="mono">217.3</td><td class="mono">0.873</td><td class="mono">4.32</td></tr>
            <tr><td class="mono">2</td><td>Inverter 2</td><td><span class="status-online">Online</span></td><td class="mono">180.1</td><td class="mono">218.1</td><td class="mono">0.657</td><td class="mono">3.21</td></tr>
            <tr><td class="mono">3</td><td>Inverter 3</td><td><span class="status-online">Online</span></td><td class="mono">310.4</td><td class="mono">219.5</td><td class="mono">1.345</td><td class="mono">5.20</td></tr>
            <tr><td class="mono">4</td><td>Inverter 4</td><td><span class="status-online">Online</span></td><td class="mono">245.0</td><td class="mono">217.9</td><td class="mono">1.125</td><td class="mono">4.15</td></tr>
            <tr><td class="mono">5</td><td>Inverter 5</td><td><span class="status-warn">Warning</span></td><td class="mono">120.8</td><td class="mono">216.3</td><td class="mono">0.558</td><td class="mono">2.11</td></tr>
            <tr><td class="mono">6</td><td>Inverter 6</td><td><span class="status-online">Online</span></td><td class="mono">210.2</td><td class="mono">217.4</td><td class="mono">0.965</td><td class="mono">4.01</td></tr>
            <tr><td colspan="7" style="text-align:center;color:var(--text-muted);font-size:11px;padding:6px">· · · 9 more nodes · · ·</td></tr>
            <tr><td class="mono">15</td><td>Inverter 15</td><td><span class="status-online">Online</span></td><td class="mono">190.3</td><td class="mono">218.6</td><td class="mono">0.871</td><td class="mono">3.35</td></tr>
          </tbody>
        </table>
        <button class="view-all-btn">View All Nodes</button>
      </div>
    </div>

    <!-- TREND CHARTS -->
    <div class="section-header">Trend Charts</div>
    <div class="three-col">
      <!-- Energy Trend -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">⚡ Energy Trend (All Nodes)</div>
          <div class="trend-tabs">
            <button class="trend-tab active">Today</button>
            <button class="trend-tab">7 Days</button>
            <button class="trend-tab">30 Days</button>
          </div>
        </div>
        <div class="chart-wrap" style="height:90px">
          <svg class="chart-svg" viewBox="0 0 300 90" preserveAspectRatio="none">
            <defs>
              <linearGradient id="eg" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#4caf50" stop-opacity="0.25"/>
                <stop offset="100%" stop-color="#4caf50" stop-opacity="0"/>
              </linearGradient>
            </defs>
            <path d="M0,75 C25,70 50,60 75,55 C100,50 125,45 150,38 C175,30 200,22 225,28 C250,35 275,42 300,30" fill="none" stroke="#4caf50" stroke-width="2.5" stroke-linecap="round"/>
            <path d="M0,75 C25,70 50,60 75,55 C100,50 125,45 150,38 C175,30 200,22 225,28 C250,35 275,42 300,30 L300,90 L0,90 Z" fill="url(#eg)"/>
            <text x="5" y="88" fill="var(--text-muted)" font-size="8">00:00</text>
            <text x="75" y="88" fill="var(--text-muted)" font-size="8">06:00</text>
            <text x="145" y="88" fill="var(--text-muted)" font-size="8">12:00</text>
            <text x="215" y="88" fill="var(--text-muted)" font-size="8">18:00</text>
            <text x="270" y="88" fill="var(--text-muted)" font-size="8">24:00</text>
          </svg>
        </div>
        <div style="font-size:10px;color:var(--text-muted);margin-top:6px;display:flex;align-items:center;gap:5px"><span style="width:10px;height:3px;background:#4caf50;display:inline-block;border-radius:2px"></span> Total Energy (kWh)</div>
      </div>

      <!-- Cost Trend -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">💰 Cost Trend (All Nodes)</div>
          <div class="trend-tabs">
            <button class="trend-tab active">Today</button>
            <button class="trend-tab">7 Days</button>
            <button class="trend-tab">30 Days</button>
          </div>
        </div>
        <div class="chart-wrap" style="height:90px">
          <svg class="chart-svg" viewBox="0 0 300 90" preserveAspectRatio="none">
            <defs>
              <linearGradient id="cg" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#f97316" stop-opacity="0.25"/>
                <stop offset="100%" stop-color="#f97316" stop-opacity="0"/>
              </linearGradient>
            </defs>
            <path d="M0,80 C25,75 50,68 75,60 C100,52 125,48 150,42 C175,36 200,28 225,22 C250,18 275,25 300,20" fill="none" stroke="#f97316" stroke-width="2.5" stroke-linecap="round"/>
            <path d="M0,80 C25,75 50,68 75,60 C100,52 125,48 150,42 C175,36 200,28 225,22 C250,18 275,25 300,20 L300,90 L0,90 Z" fill="url(#cg)"/>
            <text x="5" y="88" fill="var(--text-muted)" font-size="8">00:00</text>
            <text x="75" y="88" fill="var(--text-muted)" font-size="8">06:00</text>
            <text x="145" y="88" fill="var(--text-muted)" font-size="8">12:00</text>
            <text x="215" y="88" fill="var(--text-muted)" font-size="8">18:00</text>
            <text x="270" y="88" fill="var(--text-muted)" font-size="8">24:00</text>
          </svg>
        </div>
        <div style="font-size:10px;color:var(--text-muted);margin-top:6px;display:flex;align-items:center;gap:5px"><span style="width:10px;height:3px;background:#f97316;display:inline-block;border-radius:2px"></span> Total Cost (RM)</div>
      </div>

      <!-- CO2 Trend -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">🌿 CO₂ Trend (All Nodes)</div>
          <div class="trend-tabs">
            <button class="trend-tab active">Today</button>
            <button class="trend-tab">7 Days</button>
            <button class="trend-tab">30 Days</button>
          </div>
        </div>
        <div class="chart-wrap" style="height:90px">
          <svg class="chart-svg" viewBox="0 0 300 90" preserveAspectRatio="none">
            <defs>
              <linearGradient id="co2g" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#2e7d32" stop-opacity="0.25"/>
                <stop offset="100%" stop-color="#2e7d32" stop-opacity="0"/>
              </linearGradient>
            </defs>
            <path d="M0,78 C25,72 50,65 75,58 C100,50 125,44 150,40 C175,35 200,30 225,26 C250,22 275,24 300,18" fill="none" stroke="#2e7d32" stroke-width="2.5" stroke-linecap="round"/>
            <path d="M0,78 C25,72 50,65 75,58 C100,50 125,44 150,40 C175,35 200,30 225,26 C250,22 275,24 300,18 L300,90 L0,90 Z" fill="url(#co2g)"/>
            <text x="5" y="88" fill="var(--text-muted)" font-size="8">00:00</text>
            <text x="75" y="88" fill="var(--text-muted)" font-size="8">06:00</text>
            <text x="145" y="88" fill="var(--text-muted)" font-size="8">12:00</text>
            <text x="215" y="88" fill="var(--text-muted)" font-size="8">18:00</text>
            <text x="270" y="88" fill="var(--text-muted)" font-size="8">24:00</text>
          </svg>
        </div>
        <div style="font-size:10px;color:var(--text-muted);margin-top:6px;display:flex;align-items:center;gap:5px"><span style="width:10px;height:3px;background:#2e7d32;display:inline-block;border-radius:2px"></span> CO₂ Reduced (kg)</div>
      </div>
    </div>

    <!-- ALERTS + INSIGHTS -->
    <div class="section-header">Alerts & Insights</div>
    <div class="two-col">
      <!-- Recent Alerts -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">🔔 Recent Alerts <span class="nav-badge" style="position:static;margin-left:4px">5</span></div>
          <a class="card-action" href="#">View All</a>
        </div>
        <table class="alert-table">
          <thead>
            <tr><th>Time</th><th>Node</th><th>Alert Type</th><th>Message</th><th>Status</th></tr>
          </thead>
          <tbody>
            <tr>
              <td style="font-size:10px;color:var(--text-muted)">14 May, 09:58 PM</td>
              <td>Inverter 5</td>
              <td><span class="alert-type at-battery">🔋 Low Battery</span></td>
              <td style="font-size:11px;color:var(--text-secondary)">Battery level is low (18%).</td>
              <td><span class="badge-active">Active</span></td>
            </tr>
            <tr>
              <td style="font-size:10px;color:var(--text-muted)">14 May, 09:47 PM</td>
              <td>Inverter 12</td>
              <td><span class="alert-type at-highload">⚡ High Load</span></td>
              <td style="font-size:11px;color:var(--text-secondary)">Power usage high (305 W).</td>
              <td><span class="badge-active">Active</span></td>
            </tr>
            <tr>
              <td style="font-size:10px;color:var(--text-muted)">14 May, 09:35 PM</td>
              <td>Inverter 7</td>
              <td><span class="alert-type at-output">⚠️ Low Output</span></td>
              <td style="font-size:11px;color:var(--text-secondary)">Output power below expected range.</td>
              <td><span class="badge-active">Active</span></td>
            </tr>
            <tr>
              <td style="font-size:10px;color:var(--text-muted)">14 May, 09:12 PM</td>
              <td>Inverter 3</td>
              <td><span class="alert-type at-voltage">🔌 Voltage Fluctuation</span></td>
              <td style="font-size:11px;color:var(--text-secondary)">Voltage unstable (219V).</td>
              <td><span class="badge-resolved">Resolved</span></td>
            </tr>
            <tr>
              <td style="font-size:10px;color:var(--text-muted)">14 May, 08:51 PM</td>
              <td>Inverter 11</td>
              <td><span class="alert-type at-comm">📡 Communication</span></td>
              <td style="font-size:11px;color:var(--text-secondary)">Connection intermittent.</td>
              <td><span class="badge-resolved">Resolved</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Smart Insights -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">💡 Smart Insights</div>
        </div>
        <div class="insight-row">
          <div class="insight-icon-wrap">⏰</div>
          <div>
            <div class="insight-label">Peak Usage Period</div>
            <div class="insight-val">9:00 PM – 10:00 PM</div>
            <div class="insight-sub">Today</div>
          </div>
        </div>
        <div class="insight-row">
          <div class="insight-icon-wrap">⚡</div>
          <div>
            <div class="insight-label">Highest Energy Node</div>
            <div class="insight-val">Inverter 3 (5.20 kWh)</div>
            <div class="insight-sub">Today</div>
          </div>
        </div>
        <div class="insight-row">
          <div class="insight-icon-wrap">🏆</div>
          <div>
            <div class="insight-label">Most Efficient Node</div>
            <div class="insight-val">Inverter 14 (92/100)</div>
            <div class="insight-sub">Efficiency Score</div>
          </div>
        </div>
        <div class="insight-row">
          <div class="insight-icon-wrap">🌿</div>
          <div>
            <div class="insight-label">CO₂ Saved This Month</div>
            <div class="insight-val">5.42 kg</div>
            <div class="insight-sub stat-change up">▲ 12.3% higher vs last month</div>
          </div>
        </div>
        <button class="insights-cta">📋 View Full Insights Report</button>
      </div>
    </div>

  </div><!-- /content -->

  <!-- FOOTER -->
  <footer class="footer">
    <span>© 2025 Projek Pasar Malam Hijau Dikuasakan Power Inverter 2.0 | Hakcipta Terpelihara</span>
    <span>Dibangunkan bersama komuniti untuk masa depan rendah karbon. 🌿</span>
  </footer>
</div>

<script>
  function toggleTheme() {
    const html = document.documentElement;
    const btn = document.querySelector('.theme-toggle');
    if (html.getAttribute('data-theme') === 'dark') {
      html.setAttribute('data-theme', 'light');
      btn.textContent = '🌙';
    } else {
      html.setAttribute('data-theme', 'dark');
      btn.textContent = '☀️';
    }
  }

  // Trend tab interaction
  document.querySelectorAll('.trend-tabs').forEach(tabs => {
    tabs.querySelectorAll('.trend-tab').forEach(tab => {
      tab.addEventListener('click', () => {
        tabs.querySelectorAll('.trend-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
      });
    });
  });

  // Nav item interaction
  document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', () => {
      document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
      item.classList.add('active');
    });
  });
</script>
</body>
</html>