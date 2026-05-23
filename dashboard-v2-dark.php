<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Energy Monitoring & CO₂ Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;500;600;700;800;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #0a0e1a;
    --bg2: #0d1220;
    --bg3: #111827;
    --card: #111827;
    --card2: #161d2e;
    --border: #1e2d47;
    --border2: #243352;
    --text: #e2e8f0;
    --text2: #8fa3bf;
    --text3: #4a6080;
    --accent: #00d4ff;
    --accent2: #0099cc;
    --green: #00e676;
    --green2: #00b359;
    --yellow: #ffd740;
    --orange: #ff9800;
    --red: #ff3d71;
    --purple: #b44aff;
    --blue: #4488ff;
    --mono: 'Share Tech Mono', monospace;
    --font: 'Exo 2', sans-serif;
    --r: 10px;
    --r-sm: 6px;
    --glow-green: 0 0 12px rgba(0,230,118,0.35);
    --glow-cyan: 0 0 12px rgba(0,212,255,0.35);
    --glow-red: 0 0 12px rgba(255,61,113,0.35);
    --glow-yellow: 0 0 12px rgba(255,215,64,0.35);
  }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    font-family: var(--font);
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    font-size: 13px;
  }

  /* ── TOPBAR ── */
  .topbar {
    background: var(--bg2);
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center;
    padding: 0 20px; height: 58px; gap: 0;
    flex-shrink: 0;
  }
  .logo-wrap {
    width: 52px; display: flex; align-items: center; justify-content: center; margin-right: 14px;
  }
  .logo-bolt {
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg,#ffd740,#ff9800);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; box-shadow: 0 0 16px rgba(255,215,64,0.4);
  }
  .topbar-title { flex: 1; }
  .topbar-title h1 {
    font-size: 17px; font-weight: 800; letter-spacing: -0.01em;
    color: var(--text);
    display: flex; align-items: baseline; gap: 6px;
  }
  .topbar-title h1 .sub-co2 { font-size: 11px; font-weight: 500; color: var(--text2); }
  .topbar-title p { font-size: 11px; color: var(--text2); margin-top: 2px; }
  .topbar-right { display: flex; align-items: center; gap: 10px; }
  .topbar-meta {
    display: flex; align-items: center; gap: 6px;
    background: var(--bg3); border: 1px solid var(--border);
    border-radius: var(--r-sm); padding: 5px 10px;
    font-size: 11px; color: var(--text2);
  }
  .topbar-meta strong { color: var(--text); font-size: 12px; }
  .topbar-meta .icon { font-size: 13px; }
  .notif-btn {
    width: 36px; height: 36px; border-radius: var(--r-sm);
    background: var(--bg3); border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; position: relative; font-size: 16px;
    transition: border-color 0.2s;
  }
  .notif-btn:hover { border-color: var(--accent); }
  .notif-dot {
    position: absolute; top: 3px; right: 3px;
    background: var(--red); color: #fff;
    width: 15px; height: 15px; border-radius: 50%;
    font-size: 9px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    box-shadow: var(--glow-red);
  }
  .admin-pill {
    display: flex; align-items: center; gap: 8px;
    background: var(--bg3); border: 1px solid var(--border);
    border-radius: var(--r-sm); padding: 5px 12px 5px 6px; cursor: pointer;
    transition: border-color 0.2s;
  }
  .admin-pill:hover { border-color: var(--accent); }
  .admin-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    background: linear-gradient(135deg,var(--blue),var(--purple));
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700;
  }
  .admin-name { font-size: 12px; font-weight: 600; line-height: 1.3; }
  .admin-role { font-size: 10px; color: var(--text2); }

  /* ── BODY LAYOUT ── */
  .body-wrap { display: flex; flex: 1; overflow: hidden; }

  /* ── SIDEBAR ── */
  .sidebar {
    width: 110px; background: var(--bg2);
    border-right: 1px solid var(--border);
    display: flex; flex-direction: column;
    padding: 12px 0; flex-shrink: 0;
  }
  .nav-item {
    display: flex; flex-direction: column; align-items: center;
    gap: 4px; padding: 10px 8px; cursor: pointer;
    color: var(--text3); font-size: 10.5px; font-weight: 600;
    text-align: center; transition: color 0.2s, background 0.2s;
    position: relative; border-radius: 0;
  }
  .nav-item:hover { color: var(--text2); background: rgba(255,255,255,0.03); }
  .nav-item.active { color: var(--accent); }
  .nav-item.active::before {
    content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%);
    height: 32px; width: 3px; background: var(--accent);
    border-radius: 0 3px 3px 0;
  }
  .nav-icon { font-size: 18px; }
  .nav-badge {
    position: absolute; top: 6px; right: 14px;
    background: var(--red); color: #fff;
    font-size: 9px; font-weight: 700; width: 15px; height: 15px;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    box-shadow: var(--glow-red);
  }
  .sys-status {
    margin-top: auto; padding: 14px 10px;
    border-top: 1px solid var(--border);
  }
  .sys-label { font-size: 9px; color: var(--text3); font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; text-align: center; margin-bottom: 8px; }
  .sys-normal { text-align: center; color: var(--green); font-size: 11px; font-weight: 700; margin-bottom: 4px; }
  .sys-nodes { text-align: center; font-size: 10px; color: var(--text2); }

  /* ── MAIN ── */
  .main { flex: 1; overflow-y: auto; padding: 14px 16px; display: flex; flex-direction: column; gap: 12px; }

  /* ── STAT STRIP ── */
  .stat-strip { display: grid; grid-template-columns: repeat(5,1fr); gap: 10px; }
  .stat-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: var(--r); padding: 14px 16px;
    display: flex; align-items: center; gap: 12px;
    transition: border-color 0.2s, transform 0.2s;
  }
  .stat-card:hover { border-color: var(--border2); transform: translateY(-1px); }
  .stat-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 20px;
    flex-shrink: 0;
  }
  .si-blue { background: rgba(68,136,255,0.15); box-shadow: 0 0 10px rgba(68,136,255,0.2); }
  .si-yellow { background: rgba(255,215,64,0.15); box-shadow: 0 0 10px rgba(255,215,64,0.2); }
  .si-orange { background: rgba(255,152,0,0.15); box-shadow: 0 0 10px rgba(255,152,0,0.2); }
  .si-purple { background: rgba(180,74,255,0.15); box-shadow: 0 0 10px rgba(180,74,255,0.2); }
  .si-green { background: rgba(0,230,118,0.15); box-shadow: 0 0 10px rgba(0,230,118,0.2); }
  .stat-content {}
  .stat-label { font-size: 9.5px; font-weight: 700; color: var(--text3); text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 3px; }
  .stat-value { font-size: 22px; font-weight: 800; color: var(--text); font-family: var(--mono); letter-spacing: -0.02em; line-height: 1; }
  .stat-value .unit { font-size: 12px; color: var(--text2); font-weight: 500; }
  .stat-sub { font-size: 10.5px; color: var(--text2); margin-top: 3px; }
  .stat-change { font-size: 10.5px; font-weight: 700; }
  .up { color: var(--green); }
  .down { color: var(--red); }
  .stat-good { color: var(--green); font-size: 11px; font-weight: 700; }

  /* ── GRID SECTION ── */
  .mid-row { display: grid; grid-template-columns: 1fr 380px; gap: 12px; }

  /* ── UNITS GRID ── */
  .units-panel {
    background: var(--card); border: 1px solid var(--border); border-radius: var(--r);
    padding: 14px;
  }
  .panel-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; }
  .panel-title { font-size: 13px; font-weight: 700; color: var(--text); letter-spacing: 0.01em; }
  .status-pills { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
  .s-pill { font-size: 10px; font-weight: 600; display: flex; align-items: center; gap: 4px; color: var(--text2); }
  .s-dot { width: 8px; height: 8px; border-radius: 50%; }
  .sd-green { background: var(--green); box-shadow: 0 0 6px rgba(0,230,118,0.6); }
  .sd-yellow { background: var(--yellow); box-shadow: 0 0 6px rgba(255,215,64,0.6); }
  .sd-red { background: var(--red); box-shadow: 0 0 6px rgba(255,61,113,0.6); }
  .view-toggle { display: flex; gap: 0; border: 1px solid var(--border2); border-radius: var(--r-sm); overflow: hidden; }
  .vtab {
    padding: 5px 12px; font-size: 11px; font-weight: 600; cursor: pointer;
    background: transparent; border: none; color: var(--text3);
    transition: background 0.2s, color 0.2s;
  }
  .vtab.active { background: var(--blue); color: #fff; }

  .units-grid { display: grid; grid-template-columns: repeat(5,1fr); gap: 8px; }
  .unit-card {
    background: var(--bg3); border: 1px solid var(--border);
    border-radius: var(--r-sm); padding: 10px;
    transition: border-color 0.2s, transform 0.2s;
    cursor: pointer;
  }
  .unit-card:hover { transform: translateY(-1px); border-color: var(--border2); }
  .unit-card.warn { border-color: rgba(255,215,64,0.4); }
  .unit-card.alert { border-color: rgba(255,61,113,0.4); }
  .unit-card.warn:hover { border-color: var(--yellow); }
  .unit-card.alert:hover { border-color: var(--red); }
  .uc-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
  .uc-name { font-size: 11px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 5px; }
  .uc-name .uc-icon { font-size: 12px; opacity: 0.7; }
  .uc-status { font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 10px; }
  .st-online { background: rgba(0,230,118,0.15); color: var(--green); }
  .st-warn { background: rgba(255,215,64,0.15); color: var(--yellow); }
  .st-alert { background: rgba(255,61,113,0.15); color: var(--red); }
  .uc-donut-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
  .donut-wrap { position: relative; width: 44px; height: 44px; flex-shrink: 0; }
  .donut-svg { width: 44px; height: 44px; transform: rotate(-90deg); }
  .donut-pct {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 800; font-family: var(--mono);
  }
  .uc-stats { flex: 1; }
  .uc-stat-row { display: flex; justify-content: space-between; }
  .uc-stat-label { font-size: 9px; color: var(--text3); }
  .uc-stat-val { font-size: 9.5px; font-weight: 700; color: var(--text); font-family: var(--mono); }
  .uc-stat-val.alert-val { color: var(--red); }
  .uc-today { font-size: 9px; color: var(--text2); margin-top: 2px; }

  /* ── MAP PANEL ── */
  .map-panel {
    background: var(--card); border: 1px solid var(--border); border-radius: var(--r);
    padding: 14px; display: flex; flex-direction: column; gap: 10px;
  }
  .map-container {
    background: #1a2030; border-radius: var(--r-sm); position: relative;
    overflow: hidden; flex: 1; min-height: 280px;
  }
  .map-grid {
    position: absolute; inset: 0;
    background-image:
      linear-gradient(rgba(68,136,255,0.06) 1px, transparent 1px),
      linear-gradient(90deg,rgba(68,136,255,0.06) 1px, transparent 1px);
    background-size: 32px 32px;
  }
  /* Road-like structures */
  .map-road {
    position: absolute; background: rgba(100,120,160,0.2);
  }
  .map-building {
    position: absolute; background: rgba(50,70,100,0.5);
    border: 1px solid rgba(80,100,140,0.4); border-radius: 3px;
  }
  .map-node {
    position: absolute; width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 800; font-family: var(--mono);
    cursor: pointer; transition: transform 0.2s;
    transform: translate(-50%,-50%);
    border: 2px solid;
  }
  .map-node:hover { transform: translate(-50%,-50%) scale(1.2); z-index: 10; }
  .mn-green { background: rgba(0,230,118,0.25); border-color: var(--green); color: var(--green); box-shadow: 0 0 10px rgba(0,230,118,0.4); }
  .mn-yellow { background: rgba(255,215,64,0.25); border-color: var(--yellow); color: var(--yellow); box-shadow: 0 0 10px rgba(255,215,64,0.4); }
  .mn-red { background: rgba(255,61,113,0.35); border-color: var(--red); color: #fff; box-shadow: 0 0 12px rgba(255,61,113,0.6); }
  .map-label { font-size: 10px; color: var(--text3); text-align: center; padding: 4px; }
  .map-legend { display: flex; gap: 14px; justify-content: center; }
  .ml-item { font-size: 10px; color: var(--text2); display: flex; align-items: center; gap: 5px; }
  .selected-unit-panel {
    background: var(--bg3); border: 1px solid var(--border2); border-radius: var(--r-sm);
    padding: 12px;
  }
  .su-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
  .su-title { font-size: 11px; font-weight: 700; color: var(--text3); text-transform: uppercase; letter-spacing: 0.08em; }
  .su-name { font-size: 15px; font-weight: 800; color: var(--red); font-family: var(--mono); }
  .su-badge { background: rgba(255,61,113,0.2); color: var(--red); font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 6px; }
  .su-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-bottom: 10px; }
  .su-stat { text-align: center; }
  .su-stat-label { font-size: 9px; color: var(--text3); text-transform: uppercase; letter-spacing: 0.05em; }
  .su-stat-val { font-size: 18px; font-weight: 800; font-family: var(--mono); color: var(--text); margin-top: 2px; }
  .su-stat-val.alert-c { color: var(--red); }
  .su-stat-unit { font-size: 10px; color: var(--text2); }
  .view-details-btn {
    width: 100%; padding: 9px; background: var(--red); color: #fff;
    border: none; border-radius: var(--r-sm); font-size: 12px; font-weight: 700;
    cursor: pointer; font-family: var(--font); letter-spacing: 0.02em;
    transition: opacity 0.2s; box-shadow: var(--glow-red);
  }
  .view-details-btn:hover { opacity: 0.85; }

  /* ── BOTTOM ROW ── */
  .bottom-row { display: grid; grid-template-columns: 1fr 1fr 280px 260px; gap: 12px; }

  /* ── CHART CARD ── */
  .chart-card {
    background: var(--card); border: 1px solid var(--border); border-radius: var(--r); padding: 14px;
  }
  .chart-tabs { display: flex; gap: 4px; }
  .ctab {
    font-size: 10px; font-weight: 600; padding: 4px 9px; border-radius: var(--r-sm);
    border: 1px solid transparent; cursor: pointer;
    background: transparent; color: var(--text3); font-family: var(--font);
    transition: all 0.2s;
  }
  .ctab.active { background: var(--blue); color: #fff; border-color: var(--blue); }
  .ctab:not(.active):hover { color: var(--text2); border-color: var(--border); }
  .chart-area { height: 130px; margin-top: 12px; position: relative; }
  .chart-legend { display: flex; gap: 14px; margin-top: 8px; flex-wrap: wrap; }
  .cl-item { font-size: 10px; color: var(--text2); display: flex; align-items: center; gap: 5px; }
  .cl-line { width: 16px; height: 2px; border-radius: 2px; }
  .tooltip-box {
    position: absolute; top: 10px; left: 120px;
    background: var(--bg3); border: 1px solid var(--border2);
    border-radius: var(--r-sm); padding: 8px 12px;
    font-size: 10.5px; color: var(--text); z-index: 5;
    pointer-events: none;
  }
  .tooltip-box .tdate { font-weight: 700; color: var(--accent); margin-bottom: 3px; }
  .tooltip-box .tval { color: var(--text2); }
  .tooltip-box .tval span { color: var(--text); font-weight: 600; font-family: var(--mono); }

  /* ── CO2 IMPACT ── */
  .co2-card {
    background: var(--card); border: 1px solid var(--border); border-radius: var(--r); padding: 14px;
  }
  .co2-donut-center { display: flex; align-items: center; gap: 16px; margin-bottom: 12px; }
  .big-donut-wrap { position: relative; width: 90px; height: 90px; flex-shrink: 0; }
  .big-donut-svg { width: 90px; height: 90px; transform: rotate(-90deg); }
  .big-donut-label {
    position: absolute; inset: 0; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
  }
  .big-donut-val { font-size: 18px; font-weight: 800; font-family: var(--mono); color: var(--green); line-height: 1; }
  .big-donut-unit { font-size: 9px; color: var(--text2); }
  .big-donut-sub { font-size: 9px; color: var(--text3); text-align: center; margin-top: 2px; }
  .equiv-label { font-size: 9.5px; font-weight: 700; color: var(--text3); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px; }
  .equiv-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
  .equiv-icon { font-size: 16px; }
  .equiv-text { }
  .equiv-name { font-size: 10px; color: var(--text2); }
  .equiv-val { font-size: 12px; font-weight: 700; color: var(--text); font-family: var(--mono); }
  .co2-lifetime { margin-top: 8px; padding-top: 8px; border-top: 1px solid var(--border); }
  .co2-lt-row { display: flex; justify-content: space-between; align-items: center; }
  .co2-lt-label { font-size: 10px; color: var(--text2); }
  .co2-lt-val { font-size: 13px; font-weight: 800; font-family: var(--mono); color: var(--accent); }

  /* ── SMART INSIGHTS ── */
  .insights-card {
    background: var(--card); border: 1px solid var(--border); border-radius: var(--r); padding: 14px;
  }
  .insight-item { padding: 8px 0; border-bottom: 1px solid var(--border); }
  .insight-item:last-child { border-bottom: none; }
  .ii-header { display: flex; align-items: center; gap: 7px; margin-bottom: 3px; }
  .ii-icon { width: 26px; height: 26px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
  .ii-green { background: rgba(0,230,118,0.15); }
  .ii-blue { background: rgba(68,136,255,0.15); }
  .ii-yellow { background: rgba(255,215,64,0.15); }
  .ii-purple { background: rgba(180,74,255,0.15); }
  .ii-label { font-size: 10px; color: var(--text2); font-weight: 500; }
  .ii-val { font-size: 12px; font-weight: 700; color: var(--text); }
  .ii-sub { font-size: 9.5px; color: var(--text3); margin-left: 33px; }
  .insights-btn {
    width: 100%; margin-top: 10px; padding: 9px;
    background: linear-gradient(135deg,#1565c0,#0288d1);
    color: #fff; border: none; border-radius: var(--r-sm);
    font-size: 11px; font-weight: 700; cursor: pointer;
    font-family: var(--font); letter-spacing: 0.02em;
    box-shadow: 0 0 12px rgba(2,136,209,0.3);
    transition: opacity 0.2s;
  }
  .insights-btn:hover { opacity: 0.85; }

  /* ── ALERTS SECTION ── */
  .alerts-section {
    background: var(--card); border: 1px solid var(--border); border-radius: var(--r); padding: 14px;
  }
  .alerts-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
  .alert-table { width: 100%; border-collapse: collapse; }
  .alert-table th {
    text-align: left; padding: 6px 10px;
    font-size: 9.5px; font-weight: 700; color: var(--text3);
    letter-spacing: 0.07em; text-transform: uppercase;
    border-bottom: 1px solid var(--border);
  }
  .alert-table td { padding: 8px 10px; border-bottom: 1px solid var(--border); vertical-align: middle; }
  .alert-table tr:last-child td { border-bottom: none; }
  .alert-table tr:hover td { background: rgba(255,255,255,0.02); }
  .at-time { font-size: 11px; color: var(--text3); font-family: var(--mono); white-space: nowrap; }
  .at-unit { font-size: 11px; font-weight: 600; color: var(--text); }
  .at-type { font-size: 10px; font-weight: 700; }
  .atype-battery { color: var(--yellow); }
  .atype-output { color: var(--orange); }
  .atype-highload { color: var(--red); }
  .at-msg { font-size: 11px; color: var(--text2); }
  .badge-active { background: rgba(255,61,113,0.15); color: var(--red); font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 6px; }
  .badge-resolved { background: rgba(0,230,118,0.12); color: var(--green); font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 6px; }
  .view-all-link { font-size: 11px; color: var(--accent); font-weight: 600; cursor: pointer; text-decoration: none; }
  .view-all-link:hover { text-decoration: underline; }

  /* ── EFFICIENCY RANKING ── */
  .eff-card {
    background: var(--card); border: 1px solid var(--border); border-radius: var(--r); padding: 14px;
  }
  .eff-row { display: flex; align-items: center; gap: 8px; padding: 7px 0; border-bottom: 1px solid var(--border); }
  .eff-row:last-child { border-bottom: none; }
  .eff-rank { font-size: 11px; font-weight: 800; color: var(--text3); width: 20px; text-align: center; font-family: var(--mono); }
  .eff-medal { font-size: 14px; width: 20px; text-align: center; }
  .eff-name { font-size: 11px; font-weight: 600; color: var(--text); flex: 1; }
  .eff-bar-wrap { flex: 2; height: 6px; background: var(--border); border-radius: 3px; overflow: hidden; }
  .eff-bar { height: 100%; border-radius: 3px; }
  .eff-score { font-size: 10.5px; font-weight: 700; font-family: var(--mono); color: var(--text2); width: 48px; text-align: right; }
  .more-dots { text-align: center; color: var(--text3); font-size: 11px; padding: 6px; }

  /* ── ALERTS-RANK ROW ── */
  .bottom-bot-row { display: grid; grid-template-columns: 1fr 300px; gap: 12px; }

  /* ── SCANLINES EFFECT ── */
  body::after {
    content: ''; position: fixed; inset: 0; pointer-events: none; z-index: 999;
    background: repeating-linear-gradient(
      0deg,
      transparent, transparent 2px,
      rgba(0,0,0,0.03) 2px, rgba(0,0,0,0.03) 4px
    );
  }

  /* scrollbar */
  ::-webkit-scrollbar { width: 5px; }
  ::-webkit-scrollbar-track { background: var(--bg); }
  ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 3px; }
</style>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
  <div class="logo-wrap">
    <div class="logo-bolt">⚡</div>
  </div>
  <div class="topbar-title">
    <h1>SMART ENERGY MONITORING &amp; CO<sub style="font-size:10px">₂</sub> DASHBOARD</h1>
    <p>15 Portable Power Stations – Dataran Niaga MBIP</p>
  </div>
  <div class="topbar-right">
    <div class="topbar-meta">
      <span class="icon">🔄</span>
      <div>
        <div style="font-size:9px;color:var(--text3)">Last Updated</div>
        <strong>20 May 2025, 10:30:45 PM</strong>
      </div>
    </div>
    <button class="notif-btn">🔔<span class="notif-dot">5</span></button>
    <div class="topbar-meta">
      <span class="icon">📅</span>
      <strong>20 May 2025</strong>
    </div>
    <div class="admin-pill">
      <div class="admin-avatar">A</div>
      <div>
        <div class="admin-name">Admin</div>
        <div class="admin-role">MBIP</div>
      </div>
    </div>
  </div>
</header>

<!-- BODY -->
<div class="body-wrap">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="nav-item active">
      <span class="nav-icon">📊</span>Overview
    </div>
    <div class="nav-item">
      <span class="nav-icon">📡</span>All Units
    </div>
    <div class="nav-item">
      <span class="nav-icon">🗺️</span>Map View
    </div>
    <div class="nav-item">
      <span class="nav-icon">📈</span>Analytics
    </div>
    <div class="nav-item">
      <span class="nav-icon">🌿</span>CO₂ Dash
    </div>
    <div class="nav-item" style="position:relative">
      <span class="nav-icon">🔔</span>Alerts
      <span class="nav-badge">5</span>
    </div>
    <div class="nav-item">
      <span class="nav-icon">📋</span>Reports
    </div>
    <div class="nav-item">
      <span class="nav-icon">⚙️</span>Settings
    </div>
    <div class="nav-item">
      <span class="nav-icon">👥</span>Users
    </div>
    <div class="sys-status">
      <div class="sys-label">System Status</div>
      <div style="text-align:center;font-size:18px;margin-bottom:4px">🛡️</div>
      <div class="sys-normal">All Systems<br>Normal</div>
      <div class="sys-nodes" style="margin-top:6px">15 / 15 Units Online</div>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">

    <!-- STAT STRIP -->
    <div class="stat-strip">
      <div class="stat-card">
        <div class="stat-icon si-blue">⚡</div>
        <div class="stat-content">
          <div class="stat-label">Total Units</div>
          <div class="stat-value">15</div>
          <div class="stat-sub">Online: 15 &nbsp; Offline: 0</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon si-yellow">⚡</div>
        <div class="stat-content">
          <div class="stat-label">Total Energy Today</div>
          <div class="stat-value">78.62<span class="unit"> kWh</span></div>
          <div class="stat-sub">Yesterday: 72.31 kWh</div>
          <div class="stat-change up">▲ 8.72%</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon si-orange">💰</div>
        <div class="stat-content">
          <div class="stat-label">Total Cost Saved Today</div>
          <div class="stat-value">RM <span style="font-size:18px">138.45</span></div>
          <div class="stat-sub">Yesterday: RM 127.20</div>
          <div class="stat-change up">▲ 8.83%</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon si-purple">🌿</div>
        <div class="stat-content">
          <div class="stat-label">Total CO₂ Reduced Today</div>
          <div class="stat-value">78.45<span class="unit"> kg</span></div>
          <div class="stat-sub">All Time: 1,562.34 kg</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon si-green">📊</div>
        <div class="stat-content">
          <div class="stat-label">Avg. Efficiency Score</div>
          <div class="stat-value">86<span class="unit">/100</span></div>
          <div class="stat-good">✅ Good</div>
        </div>
      </div>
    </div>

    <!-- MID ROW: Units + Map -->
    <div class="mid-row">
      <!-- Units Panel -->
      <div class="units-panel">
        <div class="panel-header">
          <div class="panel-title">ALL UNITS (15)</div>
          <div class="status-pills">
            <span class="s-pill"><span class="s-dot sd-green"></span>Normal (12)</span>
            <span class="s-pill"><span class="s-dot sd-yellow"></span>Warning (2)</span>
            <span class="s-pill"><span class="s-dot sd-red"></span>Alert (1)</span>
          </div>
          <div class="view-toggle">
            <button class="vtab active">Grid View</button>
            <button class="vtab">Table View</button>
          </div>
        </div>
        <div class="units-grid" id="unitsGrid"></div>
      </div>

      <!-- Map + Selected Unit -->
      <div class="map-panel">
        <div class="panel-header">
          <div class="panel-title">UNIT LOCATION (MAP VIEW)</div>
          <a class="view-all-link" href="#">⛶ Full Screen</a>
        </div>
        <div class="map-container" style="height:220px">
          <div class="map-grid"></div>
          <!-- Buildings -->
          <div class="map-building" style="left:10%;top:12%;width:18%;height:22%"></div>
          <div class="map-building" style="left:32%;top:12%;width:18%;height:22%"></div>
          <div class="map-building" style="left:54%;top:12%;width:18%;height:22%"></div>
          <div class="map-building" style="left:76%;top:12%;width:16%;height:22%"></div>
          <div class="map-building" style="left:10%;top:42%;width:18%;height:22%"></div>
          <div class="map-building" style="left:32%;top:42%;width:18%;height:22%"></div>
          <div class="map-building" style="left:54%;top:42%;width:18%;height:22%"></div>
          <div class="map-building" style="left:76%;top:42%;width:16%;height:22%"></div>
          <div class="map-building" style="left:10%;top:72%;width:18%;height:22%"></div>
          <div class="map-building" style="left:32%;top:72%;width:18%;height:22%"></div>
          <div class="map-building" style="left:54%;top:72%;width:18%;height:22%"></div>
          <!-- Nodes -->
          <div class="map-node mn-green" style="left:19%;top:23%">1</div>
          <div class="map-node mn-green" style="left:41%;top:23%">2</div>
          <div class="map-node mn-green" style="left:63%;top:23%">3</div>
          <div class="map-node mn-green" style="left:84%;top:23%">4</div>
          <div class="map-node mn-yellow" style="left:19%;top:53%">5</div>
          <div class="map-node mn-green" style="left:41%;top:53%">6</div>
          <div class="map-node mn-red" style="left:63%;top:53%">7</div>
          <div class="map-node mn-green" style="left:84%;top:53%">8</div>
          <div class="map-node mn-green" style="left:19%;top:83%">9</div>
          <div class="map-node mn-green" style="left:41%;top:83%">10</div>
          <div class="map-node mn-green" style="left:63%;top:83%">11</div>
          <div class="map-node mn-yellow" style="left:84%;top:83%">12</div>
          <div class="map-node mn-green" style="left:30%;top:93%">13</div>
          <div class="map-node mn-green" style="left:52%;top:93%">15</div>
          <div style="position:absolute;bottom:6px;left:50%;transform:translateX(-50%);font-size:9px;color:var(--text3);white-space:nowrap">Jalan Dataran Niaga MBIP</div>
        </div>
        <div class="map-legend">
          <div class="ml-item"><span class="s-dot sd-green" style="width:9px;height:9px"></span> Normal</div>
          <div class="ml-item"><span class="s-dot sd-yellow" style="width:9px;height:9px"></span> Warning</div>
          <div class="ml-item"><span class="s-dot sd-red" style="width:9px;height:9px"></span> Alert</div>
          <div class="ml-item"><span style="width:9px;height:9px;border-radius:50%;background:transparent;border:2px solid var(--text3);display:inline-block"></span> Offline</div>
        </div>
        <!-- Selected Unit -->
        <div class="selected-unit-panel">
          <div class="su-header">
            <div>
              <div class="su-title">Selected Unit</div>
              <div class="su-name">Unit 7</div>
            </div>
            <span class="su-badge">Alert</span>
          </div>
          <div class="su-grid">
            <div class="su-stat">
              <div class="su-stat-label">Battery</div>
              <div class="su-stat-val alert-c">18<span class="su-stat-unit">%</span></div>
            </div>
            <div class="su-stat">
              <div class="su-stat-label">Power</div>
              <div class="su-stat-val alert-c">95<span class="su-stat-unit"> W</span></div>
            </div>
            <div class="su-stat">
              <div class="su-stat-label">Today</div>
              <div class="su-stat-val alert-c">1.02<span class="su-stat-unit"> kWh</span></div>
            </div>
          </div>
          <button class="view-details-btn">View Details</button>
        </div>
      </div>
    </div>

    <!-- BOTTOM ROW 1: Charts + CO2 + Insights -->
    <div class="bottom-row">
      <!-- Energy Trend -->
      <div class="chart-card">
        <div class="panel-header">
          <div class="panel-title">ENERGY TREND (ALL UNITS)</div>
          <div class="chart-tabs">
            <button class="ctab">Today</button>
            <button class="ctab active">7 Days</button>
            <button class="ctab">30 Days</button>
          </div>
        </div>
        <div class="chart-area">
          <div class="tooltip-box">
            <div class="tdate">20 May</div>
            <div class="tval">Energy: <span>78.62 kWh</span></div>
            <div class="tval">Cost: <span>RM 138.45</span></div>
          </div>
          <svg width="100%" height="130" viewBox="0 0 380 130" preserveAspectRatio="none">
            <defs>
              <linearGradient id="eg" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#4488ff" stop-opacity="0.2"/>
                <stop offset="100%" stop-color="#4488ff" stop-opacity="0"/>
              </linearGradient>
              <linearGradient id="cg" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#00e676" stop-opacity="0.15"/>
                <stop offset="100%" stop-color="#00e676" stop-opacity="0"/>
              </linearGradient>
            </defs>
            <!-- Grid -->
            <line x1="0" y1="30" x2="380" y2="30" stroke="rgba(255,255,255,0.05)" stroke-width="1"/>
            <line x1="0" y1="60" x2="380" y2="60" stroke="rgba(255,255,255,0.05)" stroke-width="1"/>
            <line x1="0" y1="90" x2="380" y2="90" stroke="rgba(255,255,255,0.05)" stroke-width="1"/>
            <line x1="0" y1="120" x2="380" y2="120" stroke="rgba(255,255,255,0.05)" stroke-width="1"/>
            <!-- Energy area -->
            <path d="M0,80 C30,75 60,65 95,55 C130,45 160,42 190,38 C220,34 250,50 285,45 C320,40 350,35 380,28 L380,130 L0,130 Z" fill="url(#eg)"/>
            <!-- Energy line -->
            <path d="M0,80 C30,75 60,65 95,55 C130,45 160,42 190,38 C220,34 250,50 285,45 C320,40 350,35 380,28" fill="none" stroke="#4488ff" stroke-width="2" stroke-linecap="round"/>
            <!-- Cost area -->
            <path d="M0,100 C30,95 60,88 95,82 C130,76 160,72 190,68 C220,64 250,70 285,66 C320,62 350,58 380,52 L380,130 L0,130 Z" fill="url(#cg)"/>
            <!-- Cost line -->
            <path d="M0,100 C30,95 60,88 95,82 C130,76 160,72 190,68 C220,64 250,70 285,66 C320,62 350,58 380,52" fill="none" stroke="#00e676" stroke-width="2" stroke-linecap="round" stroke-dasharray="5,3"/>
            <!-- X labels -->
            <text x="0" y="128" fill="rgba(255,255,255,0.3)" font-size="9">14 May</text>
            <text x="54" y="128" fill="rgba(255,255,255,0.3)" font-size="9">15 May</text>
            <text x="110" y="128" fill="rgba(255,255,255,0.3)" font-size="9">16 May</text>
            <text x="166" y="128" fill="rgba(255,255,255,0.3)" font-size="9">17 May</text>
            <text x="222" y="128" fill="rgba(255,255,255,0.3)" font-size="9">18 May</text>
            <text x="278" y="128" fill="rgba(255,255,255,0.3)" font-size="9">19 May</text>
            <text x="334" y="128" fill="rgba(255,255,255,0.3)" font-size="9">20 May</text>
            <!-- Highlight dot -->
            <circle cx="380" cy="28" r="5" fill="#4488ff" opacity="0.8"/>
            <circle cx="380" cy="52" r="5" fill="#00e676" opacity="0.8"/>
          </svg>
        </div>
        <div class="chart-legend">
          <div class="cl-item"><div class="cl-line" style="background:#4488ff"></div> Total Energy (kWh)</div>
          <div class="cl-item"><div class="cl-line" style="background:#00e676;border-top:2px dashed #00e676;height:0;margin-top:1px"></div> Total Cost (RM)</div>
        </div>
      </div>

      <!-- Cost & CO2 Trend -->
      <div class="chart-card">
        <div class="panel-header">
          <div class="panel-title">COST &amp; CO₂ TREND (ALL UNITS)</div>
          <div class="chart-tabs">
            <button class="ctab active">7 Days</button>
            <button class="ctab">30 Days</button>
          </div>
        </div>
        <div class="chart-area">
          <svg width="100%" height="130" viewBox="0 0 380 130" preserveAspectRatio="none">
            <defs>
              <linearGradient id="costg" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#b44aff" stop-opacity="0.6"/>
                <stop offset="100%" stop-color="#b44aff" stop-opacity="0.1"/>
              </linearGradient>
            </defs>
            <!-- Grid -->
            <line x1="0" y1="30" x2="380" y2="30" stroke="rgba(255,255,255,0.05)" stroke-width="1"/>
            <line x1="0" y1="60" x2="380" y2="60" stroke="rgba(255,255,255,0.05)" stroke-width="1"/>
            <line x1="0" y1="90" x2="380" y2="90" stroke="rgba(255,255,255,0.05)" stroke-width="1"/>
            <!-- Bars -->
            <rect x="10" y="75" width="38" height="45" rx="3" fill="url(#costg)"/>
            <rect x="64" y="60" width="38" height="60" rx="3" fill="url(#costg)"/>
            <rect x="118" y="50" width="38" height="70" rx="3" fill="url(#costg)"/>
            <rect x="172" y="65" width="38" height="55" rx="3" fill="url(#costg)"/>
            <rect x="226" y="45" width="38" height="75" rx="3" fill="url(#costg)"/>
            <rect x="280" y="55" width="38" height="65" rx="3" fill="url(#costg)"/>
            <rect x="334" y="38" width="38" height="82" rx="3" fill="#b44aff" opacity="0.8"/>
            <!-- CO2 line overlay -->
            <path d="M29,70 C64,60 118,50 172,55 C226,50 280,45 353,42" fill="none" stroke="#00e676" stroke-width="2" stroke-linecap="round"/>
            <circle cx="353" cy="42" r="4" fill="#00e676"/>
            <!-- X labels -->
            <text x="10" y="128" fill="rgba(255,255,255,0.3)" font-size="9">14 May</text>
            <text x="64" y="128" fill="rgba(255,255,255,0.3)" font-size="9">15 May</text>
            <text x="118" y="128" fill="rgba(255,255,255,0.3)" font-size="9">16 May</text>
            <text x="172" y="128" fill="rgba(255,255,255,0.3)" font-size="9">17 May</text>
            <text x="226" y="128" fill="rgba(255,255,255,0.3)" font-size="9">18 May</text>
            <text x="280" y="128" fill="rgba(255,255,255,0.3)" font-size="9">19 May</text>
            <text x="334" y="128" fill="rgba(255,255,255,0.3)" font-size="9">20 May</text>
          </svg>
        </div>
        <div class="chart-legend">
          <div class="cl-item"><div style="width:14px;height:10px;background:rgba(180,74,255,0.6);border-radius:2px"></div> Cost (RM)</div>
          <div class="cl-item"><div class="cl-line" style="background:#00e676"></div> CO₂ Reduced (kg)</div>
        </div>
      </div>

      <!-- CO2 Impact -->
      <div class="co2-card">
        <div class="panel-header">
          <div class="panel-title">CO₂ IMPACT SUMMARY</div>
        </div>
        <div class="co2-donut-center">
          <div class="big-donut-wrap">
            <svg class="big-donut-svg" viewBox="0 0 90 90">
              <circle cx="45" cy="45" r="36" fill="none" stroke="var(--border2)" stroke-width="10"/>
              <circle cx="45" cy="45" r="36" fill="none" stroke="var(--green)" stroke-width="10"
                stroke-dasharray="200 226" stroke-linecap="round"
                style="filter:drop-shadow(0 0 6px rgba(0,230,118,0.5))"/>
            </svg>
            <div class="big-donut-label">
              <div class="big-donut-val">78.45</div>
              <div class="big-donut-unit">kg</div>
            </div>
          </div>
          <div>
            <div style="font-size:12px;font-weight:700;color:var(--text)">CO₂ Reduced<br>Today</div>
          </div>
        </div>
        <div class="equiv-label">Equivalent To:</div>
        <div class="equiv-row">
          <div class="equiv-icon">🌳</div>
          <div class="equiv-text">
            <div class="equiv-name">Trees Planted</div>
            <div class="equiv-val">33 Trees</div>
          </div>
        </div>
        <div class="equiv-row">
          <div class="equiv-icon">🚗</div>
          <div class="equiv-text">
            <div class="equiv-name">Car Not Driven</div>
            <div class="equiv-val">345 km</div>
          </div>
        </div>
        <div class="equiv-row">
          <div class="equiv-icon">⛽</div>
          <div class="equiv-text">
            <div class="equiv-name">Fuel Saved</div>
            <div class="equiv-val">28.7 Litres</div>
          </div>
        </div>
        <div class="co2-lifetime">
          <div class="co2-lt-row">
            <div class="co2-lt-label">All Time CO₂ Reduced</div>
            <div class="co2-lt-val">1,562.34 kg</div>
          </div>
        </div>
      </div>

      <!-- Smart Insights -->
      <div class="insights-card">
        <div class="panel-header">
          <div class="panel-title">💡 SMART INSIGHTS</div>
        </div>
        <div class="insight-item">
          <div class="ii-header">
            <div class="ii-icon ii-green">📈</div>
            <div>
              <div class="ii-label">Highest Energy Unit</div>
              <div class="ii-val">Unit 3 (5.20 kWh)</div>
            </div>
          </div>
          <div class="ii-sub">Today</div>
        </div>
        <div class="insight-item">
          <div class="ii-header">
            <div class="ii-icon ii-blue">⏰</div>
            <div>
              <div class="ii-label">Peak Usage Period</div>
              <div class="ii-val">8:00 PM – 10:00 PM</div>
            </div>
          </div>
          <div class="ii-sub">Today</div>
        </div>
        <div class="insight-item">
          <div class="ii-header">
            <div class="ii-icon ii-yellow">🏆</div>
            <div>
              <div class="ii-label">Most Efficient Unit</div>
              <div class="ii-val">Unit 9 (92/100)</div>
            </div>
          </div>
          <div class="ii-sub">Efficiency Score</div>
        </div>
        <div class="insight-item">
          <div class="ii-header">
            <div class="ii-icon ii-purple">🌿</div>
            <div>
              <div class="ii-label">CO₂ Saved This Month</div>
              <div class="ii-val">562.30 kg</div>
            </div>
          </div>
          <div class="ii-sub" style="color:var(--green)">▲ 38% higher vs last month</div>
        </div>
        <button class="insights-btn">View Full Insights Report</button>
      </div>
    </div>

    <!-- BOTTOM ROW 2: Alerts + Efficiency -->
    <div class="bottom-bot-row">
      <!-- Alerts -->
      <div class="alerts-section">
        <div class="alerts-header">
          <div class="panel-title">RECENT ALERTS <span style="display:inline-block;background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:10px;margin-left:6px;box-shadow:var(--glow-red)">5</span></div>
          <a class="view-all-link" href="#">View All Alerts →</a>
        </div>
        <table class="alert-table">
          <thead>
            <tr>
              <th>Time</th><th>Unit</th><th>Alert Type</th><th>Message</th><th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="at-time">20 May 2025, 10:28 PM</td>
              <td class="at-unit">Unit 7</td>
              <td class="at-type atype-battery">🔋 Low Battery</td>
              <td class="at-msg">Battery level critically low (18%). Please charge.</td>
              <td><span class="badge-active">Active</span></td>
            </tr>
            <tr>
              <td class="at-time">20 May 2025, 10:20 PM</td>
              <td class="at-unit">Unit 5</td>
              <td class="at-type atype-battery">🔋 Low Battery</td>
              <td class="at-msg">Battery level is low (32%). Consider charging.</td>
              <td><span class="badge-active">Active</span></td>
            </tr>
            <tr>
              <td class="at-time">20 May 2025, 10:18 PM</td>
              <td class="at-unit">Unit 12</td>
              <td class="at-type atype-battery">🔋 Low Battery</td>
              <td class="at-msg">Battery level is low (25%). Consider charging.</td>
              <td><span class="badge-active">Active</span></td>
            </tr>
            <tr>
              <td class="at-time">20 May 2025, 09:50 PM</td>
              <td class="at-unit">Unit 7</td>
              <td class="at-type atype-output">⚠️ Low Output</td>
              <td class="at-msg">Output power is below expected range.</td>
              <td><span class="badge-active">Active</span></td>
            </tr>
            <tr>
              <td class="at-time">20 May 2025, 09:15 PM</td>
              <td class="at-unit">Unit 3</td>
              <td class="at-type atype-highload">⚡ High Load</td>
              <td class="at-msg">High power usage detected (310 W).</td>
              <td><span class="badge-resolved">Resolved</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Efficiency Ranking -->
      <div class="eff-card">
        <div class="panel-header" style="margin-bottom:8px">
          <div class="panel-title">UNIT EFFICIENCY RANKING</div>
          <a class="view-all-link" href="#">View All</a>
        </div>
        <div style="font-size:9.5px;color:var(--text3);margin-bottom:8px">Today</div>
        <div class="eff-row">
          <div class="eff-rank">1</div>
          <div class="eff-medal">🥇</div>
          <div class="eff-name">Unit 9</div>
          <div class="eff-bar-wrap"><div class="eff-bar" style="width:92%;background:linear-gradient(90deg,#00e676,#00b359)"></div></div>
          <div class="eff-score" style="color:var(--green)">92/100</div>
        </div>
        <div class="eff-row">
          <div class="eff-rank">2</div>
          <div class="eff-medal">🥈</div>
          <div class="eff-name">Unit 14</div>
          <div class="eff-bar-wrap"><div class="eff-bar" style="width:88%;background:linear-gradient(90deg,#4488ff,#0055cc)"></div></div>
          <div class="eff-score" style="color:var(--blue)">88/100</div>
        </div>
        <div class="eff-row">
          <div class="eff-rank">3</div>
          <div class="eff-medal">🥉</div>
          <div class="eff-name">Unit 11</div>
          <div class="eff-bar-wrap"><div class="eff-bar" style="width:83%;background:linear-gradient(90deg,#ffd740,#ff9800)"></div></div>
          <div class="eff-score" style="color:var(--yellow)">83/100</div>
        </div>
        <div class="more-dots">· · ·</div>
        <div class="eff-row">
          <div class="eff-rank">15</div>
          <div class="eff-medal"></div>
          <div class="eff-name">Unit 7</div>
          <div class="eff-bar-wrap"><div class="eff-bar" style="width:42%;background:linear-gradient(90deg,#ff3d71,#cc1144)"></div></div>
          <div class="eff-score" style="color:var(--red)">42/100</div>
        </div>
      </div>
    </div>

  </main>
</div>

<script>
// Unit data
const units = [
  { id:1, status:'online', pct:78, power:235, today:4.32 },
  { id:2, status:'online', pct:65, power:180, today:3.21 },
  { id:3, status:'online', pct:92, power:310, today:5.20 },
  { id:4, status:'online', pct:81, power:245, today:4.15 },
  { id:5, status:'warn',   pct:32, power:120, today:2.11 },
  { id:6, status:'online', pct:73, power:210, today:4.01 },
  { id:7, status:'alert',  pct:18, power:95,  today:1.02 },
  { id:8, status:'online', pct:67, power:175, today:3.10 },
  { id:9, status:'online', pct:90, power:299, today:5.05 },
  { id:10,status:'online', pct:76, power:220, today:4.00 },
  { id:11,status:'online', pct:83, power:255, today:4.55 },
  { id:12,status:'warn',   pct:25, power:110, today:1.85 },
  { id:13,status:'online', pct:71, power:205, today:3.80 },
  { id:14,status:'online', pct:88, power:265, today:4.90 },
  { id:15,status:'online', pct:69, power:190, today:3.35 },
];

function getColor(status, pct) {
  if(status==='alert') return { stroke:'#ff3d71', text:'#ff3d71' };
  if(status==='warn')  return { stroke:'#ffd740', text:'#ffd740' };
  return { stroke:'#00e676', text:'#00e676' };
}

function buildDonut(pct, color) {
  const r = 17; const c = 22; const circ = 2*Math.PI*r;
  const dash = (pct/100)*circ;
  return `<svg class="donut-svg" viewBox="0 0 44 44">
    <circle cx="${c}" cy="${c}" r="${r}" fill="none" stroke="rgba(255,255,255,0.07)" stroke-width="5"/>
    <circle cx="${c}" cy="${c}" r="${r}" fill="none" stroke="${color.stroke}" stroke-width="5"
      stroke-dasharray="${dash.toFixed(1)} ${circ.toFixed(1)}" stroke-linecap="round"
      style="filter:drop-shadow(0 0 4px ${color.stroke}66)"/>
  </svg>`;
}

const grid = document.getElementById('unitsGrid');
units.forEach(u => {
  const col = getColor(u.status, u.pct);
  const statusClass = u.status==='alert' ? 'st-alert' : u.status==='warn' ? 'st-warn' : 'st-online';
  const statusLabel = u.status==='alert' ? 'Alert' : u.status==='warn' ? 'Warning' : 'Online';
  const cardClass = u.status==='alert' ? 'unit-card alert' : u.status==='warn' ? 'unit-card warn' : 'unit-card';
  const powerClass = u.status==='alert' ? 'uc-stat-val alert-val' : 'uc-stat-val';
  const donut = buildDonut(u.pct, col);

  grid.innerHTML += `
  <div class="${cardClass}">
    <div class="uc-header">
      <div class="uc-name"><span class="uc-icon">🔋</span>Unit ${u.id}</div>
      <span class="uc-status ${statusClass}">${statusLabel}</span>
    </div>
    <div class="uc-donut-row">
      <div class="donut-wrap">
        ${donut}
        <div class="donut-pct" style="color:${col.text}">${u.pct}%</div>
      </div>
      <div class="uc-stats">
        <div class="uc-stat-row">
          <span class="uc-stat-label">Power</span>
          <span class="${powerClass}">${u.power} W</span>
        </div>
        <div class="uc-today">Today: ${u.today} kWh</div>
      </div>
    </div>
  </div>`;
});

// Tab interactions
document.querySelectorAll('.vtab').forEach(t=>{
  t.addEventListener('click',()=>{
    t.closest('.view-toggle').querySelectorAll('.vtab').forEach(x=>x.classList.remove('active'));
    t.classList.add('active');
  });
});
document.querySelectorAll('.chart-tabs').forEach(tabs=>{
  tabs.querySelectorAll('.ctab').forEach(t=>{
    t.addEventListener('click',()=>{
      tabs.querySelectorAll('.ctab').forEach(x=>x.classList.remove('active'));
      t.classList.add('active');
    });
  });
});
document.querySelectorAll('.nav-item').forEach(item=>{
  item.addEventListener('click',()=>{
    document.querySelectorAll('.nav-item').forEach(i=>i.classList.remove('active'));
    item.classList.add('active');
  });
});

// Map node click → update selected unit
document.querySelectorAll('.map-node').forEach(node=>{
  node.addEventListener('click',()=>{
    const id = parseInt(node.textContent);
    const u = units.find(x=>x.id===id);
    if(!u) return;
    const col = getColor(u.status, u.pct);
    const s = u.status==='alert'?'Alert':u.status==='warn'?'Warning':'Online';
    document.querySelector('.su-name').textContent = 'Unit '+u.id;
    const badge = document.querySelector('.su-badge');
    badge.textContent = s;
    badge.style.background = u.status==='alert'?'rgba(255,61,113,0.2)':u.status==='warn'?'rgba(255,215,64,0.2)':'rgba(0,230,118,0.2)';
    badge.style.color = u.status==='alert'?'var(--red)':u.status==='warn'?'var(--yellow)':'var(--green)';
    const vals = document.querySelectorAll('.su-stat-val');
    vals[0].textContent = u.pct+'%';
    vals[1].textContent = u.power+' W';
    vals[2].textContent = u.today+' kWh';
    const ac = u.status==='alert'?'alert-c':'';
    vals.forEach(v=>{v.className='su-stat-val'+(ac?' '+ac:'');});
  });
});
</script>
</body>
</html>