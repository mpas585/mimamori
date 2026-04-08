@extends('layouts.partner')

@section('title', 'デバイス管理')

@section('styles')
<style>

    .status-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 20px; }
    .status-card { background: var(--white); border-radius: var(--radius-lg); padding: 16px; text-align: center; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-200); cursor: pointer; transition: all 0.2s; }
    .status-card:hover { box-shadow: var(--shadow); transform: translateY(-1px); }
    .status-card.active { border-color: var(--gray-800); box-shadow: 0 0 0 2px var(--gray-800); }
    .status-value { font-size: 28px; font-weight: 700; line-height: 1.2; }
    .status-value.green { color: var(--green-dark); }
    .status-value.yellow { color: var(--yellow); }
    .status-value.red { color: var(--red); }
    .status-value.gray { color: var(--gray-600); }
    .status-value.light { color: var(--gray-400); }
    .status-label { font-size: 11px; color: var(--gray-500); margin-top: 4px; display: flex; align-items: center; justify-content: center; gap: 4px; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .status-dot.green { background: var(--green); }
    .status-dot.yellow { background: var(--yellow); }
    .status-dot.red { background: var(--red); }
    .status-dot.gray { background: var(--gray-600); }
    .status-dot.light { background: var(--gray-300); }
    .status-legend { display: flex; gap: 16px; font-size: 11px; color: var(--gray-500); margin-bottom: 16px; flex-wrap: wrap; }
    .toolbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; gap: 12px; flex-wrap: wrap; }
    .toolbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .toolbar-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .search-box { display: flex; align-items: center; background: var(--white); border: 1px solid var(--gray-300); border-radius: var(--radius); padding: 0 12px; width: 240px; }
    .search-box:focus-within { border-color: var(--gray-500); box-shadow: 0 0 0 3px rgba(168,162,158,0.15); }
    .search-box input { flex: 1; padding: 8px; border: none; background: transparent; font-size: 13px; font-family: inherit; }
    .search-box input:focus { outline: none; }
    .search-box span { color: var(--gray-400); font-size: 14px; }
    .filter-select { padding: 8px 12px; font-size: 13px; font-family: inherit; border: 1px solid var(--gray-300); border-radius: var(--radius); background: var(--white); color: var(--gray-700); cursor: pointer; font-weight: 500; }
    .toolbar-btn { padding: 8px 14px; font-size: 13px; font-weight: 600; font-family: inherit; border: 1px solid var(--gray-300); border-radius: var(--radius); background: var(--white); color: var(--gray-700); cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; text-decoration: none; }
    .toolbar-btn:hover { background: var(--beige); border-color: var(--gray-400); }
    .toolbar-count { font-size: 13px; color: var(--gray-500); font-weight: 500; }
    .table-card { background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; border: 1px solid var(--gray-200); }
    .table-wrapper { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead { background: var(--beige); }
    th { padding: 12px 14px; text-align: left; font-weight: 600; color: var(--gray-700); white-space: nowrap; border-bottom: 2px solid var(--gray-300); border-right: 1px solid var(--gray-200); font-size: 12px; }
    th:last-child { border-right: none; }
    td { padding: 12px 14px; border-bottom: 1px solid var(--gray-200); border-right: 1px solid var(--gray-100); vertical-align: middle; }
    td:last-child { border-right: none; }
    tbody tr:nth-child(odd) { background: var(--white); }
    tbody tr:nth-child(even) { background: var(--cream); }
    tbody tr:hover { background: var(--gray-100); }
    tbody tr:last-child td { border-bottom: none; }
    .device-status { display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; font-size: 11px; font-weight: 600; border-radius: 4px; }
    .device-status.normal { background: var(--green-light); color: var(--green-dark); }
    .device-status.warning { background: var(--yellow-light); color: #a16207; }
    .device-status.alert { background: var(--red-light); color: var(--red); }
    .device-status.offline { background: var(--gray-100); color: var(--gray-600); }
    .device-status.vacant { background: #f8fafc; color: var(--gray-400); border: 1px solid var(--gray-200); }
    .clear-alert-btn { display: inline-flex; align-items: center; gap: 3px; padding: 2px 8px; font-size: 10px; font-weight: 600; font-family: inherit; color: var(--red); background: var(--white); border: 1px solid var(--red-light); border-radius: 4px; cursor: pointer; transition: all 0.2s; margin-left: 6px; white-space: nowrap; }
    .clear-alert-btn:hover { background: var(--red-light); border-color: var(--red); }
    .detail-clear-alert-btn { display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; font-size: 12px; font-weight: 600; font-family: inherit; color: var(--red); background: var(--white); border: 1px solid var(--red-light); border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-left: 10px; vertical-align: middle; }
    .detail-clear-alert-btn:hover { background: var(--red-light); border-color: var(--red); }
    .watch-toggle { position: relative; width: 44px; height: 24px; display: inline-block; }
    .watch-toggle input { opacity: 0; width: 0; height: 0; }
    .watch-slider { position: absolute; cursor: pointer; inset: 0; background: var(--gray-300); border-radius: 12px; transition: 0.3s; }
    .watch-slider::before { content: ''; position: absolute; height: 18px; width: 18px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.3s; }
    .watch-toggle input:checked + .watch-slider { background: var(--green); }
    .watch-toggle.away input:checked + .watch-slider { background: var(--red); }
    .watch-toggle input:checked + .watch-slider::before { transform: translateX(20px); }
    .watch-timer-icon { font-size: 12px; color: var(--orange); margin-left: 4px; }
    .mono { font-family: monospace; font-weight: 700; letter-spacing: 1px; }
    .battery-low { color: var(--red); font-weight: 600; }
    .signal-weak { color: var(--orange); }
    .action-btn { padding: 5px 10px; font-size: 11px; font-weight: 600; font-family: inherit; border: 1px solid var(--gray-300); border-radius: 4px; background: var(--white); color: var(--gray-700); cursor: pointer; transition: all 0.2s; margin-right: 4px; }
    .action-btn:hover { background: var(--beige); }
    .action-btn.danger { color: var(--red); border-color: var(--red-light); }
    .action-btn.danger:hover { background: var(--red-light); }
    .pagination-bar { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-top: 2px solid var(--gray-200); background: var(--cream); font-size: 13px; }
    .pagination-info { color: var(--gray-600); font-weight: 500; }
    .pagination-buttons { display: flex; gap: 4px; }
    .page-btn { min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; font-family: inherit; border: 1px solid var(--gray-300); border-radius: 6px; background: var(--white); color: var(--gray-700); cursor: pointer; text-decoration: none; }
    .page-btn:hover { background: var(--beige); }
    .page-btn.active { background: var(--gray-800); color: var(--white); border-color: var(--gray-800); }
    .page-btn.disabled { opacity: 0.5; cursor: not-allowed; }
    .detail-section { margin-bottom: 20px; }
    .detail-section-title { font-size: 14px; font-weight: 600; color: var(--gray-700); margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
    .detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .detail-item { padding: 10px 12px; background: var(--beige); border-radius: var(--radius); }
    .detail-item-label { font-size: 11px; color: var(--gray-500); margin-bottom: 4px; }
    .detail-item-value { font-size: 14px; font-weight: 600; color: var(--gray-800); }
    .detail-form-input { width: 100%; padding: 6px 8px; font-size: 13px; font-family: inherit; border: 1px solid var(--gray-300); border-radius: var(--radius); background: var(--white); color: var(--gray-800); box-sizing: border-box; }
    .detail-form-input:focus { outline: none; border-color: var(--gray-500); box-shadow: 0 0 0 2px rgba(168,162,158,0.15); }
    .detail-status-row { display: flex; align-items: center; margin-bottom: 16px; }
    .detail-status-badge { display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 600; border-radius: 6px; }
    .detail-status-badge.normal { background: var(--green-light); color: var(--green-dark); }
    .detail-status-badge.warning { background: var(--yellow-light); color: #a16207; }
    .detail-status-badge.alert { background: var(--red-light); color: var(--red); }
    .detail-status-badge.offline { background: var(--gray-100); color: var(--gray-600); }
    .detail-notify-note { font-size: 11px; color: var(--gray-500); margin-top: 6px; line-height: 1.5; }
    .cancel-link { font-size: 11px; color: var(--gray-400); text-decoration: underline; cursor: pointer; transition: color 0.2s; align-self: center; background: none; border: none; font-family: inherit; padding: 0; }
    .cancel-link:hover { color: var(--red); }
    .toast { position: fixed; bottom: 24px; right: 24px; padding: 14px 20px; border-radius: var(--radius); font-size: 13px; font-weight: 600; color: var(--white); z-index: 9999; transform: translateY(100px); opacity: 0; transition: all 0.3s; }
    .toast.show { transform: translateY(0); opacity: 1; }
    .toast.success { background: var(--green-dark); }
    .toast.error { background: var(--red); }
    .timer-list-loading, .timer-list-empty { padding: 40px 20px; text-align: center; color: var(--gray-400); font-size: 13px; }
    .timer-device-group { margin-bottom: 16px; border: 1px solid var(--gray-200); border-radius: var(--radius); overflow: hidden; }
    .timer-device-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: var(--beige); border-bottom: 1px solid var(--gray-200); }
    .timer-device-info { display: flex; align-items: center; gap: 10px; }
    .timer-device-room { font-size: 14px; font-weight: 700; color: var(--gray-800); }
    .timer-device-name { font-size: 12px; color: var(--gray-500); }
    .timer-device-id { font-family: monospace; font-size: 11px; font-weight: 600; color: var(--gray-500); background: var(--white); padding: 2px 8px; border-radius: 4px; border: 1px solid var(--gray-200); }
    .timer-away-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; font-size: 11px; font-weight: 600; border-radius: 4px; background: var(--yellow-light); color: #a16207; }
    .timer-schedule-item { display: flex; align-items: center; padding: 10px 14px; border-bottom: 1px solid var(--gray-100); font-size: 13px; }
    .timer-schedule-item:last-child { border-bottom: none; }
    .timer-schedule-item:nth-child(even) { background: var(--cream); }
    .timer-schedule-icon { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 14px; margin-right: 10px; flex-shrink: 0; }
    .timer-schedule-icon.oneshot { background: #eff6ff; }
    .timer-schedule-icon.recurring { background: #f0fdf4; }
    .timer-schedule-info { flex: 1; }
    .timer-schedule-main { font-size: 13px; font-weight: 600; color: var(--gray-800); margin-bottom: 2px; }
    .timer-schedule-sub { font-size: 11px; color: var(--gray-500); }
    .timer-schedule-type { font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 3px; margin-left: 8px; flex-shrink: 0; }
    .timer-schedule-type.oneshot { background: #eff6ff; color: #1d4ed8; }
    .timer-schedule-type.recurring { background: #f0fdf4; color: #15803d; }
    .timer-summary { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
    .timer-summary-item { background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 10px 14px; flex: 1; min-width: 120px; text-align: center; }
    .timer-summary-value { font-size: 20px; font-weight: 700; color: var(--gray-800); }
    .timer-summary-label { font-size: 11px; color: var(--gray-500); margin-top: 2px; }
    .timer-add-btn { display: flex; align-items: center; justify-content: center; gap: 4px; width: 100%; padding: 8px; font-size: 12px; font-weight: 600; font-family: inherit; color: var(--gray-500); background: var(--cream); border: 1px dashed var(--gray-300); border-radius: 0; cursor: pointer; transition: all 0.2s; }
    .timer-add-btn:hover { background: var(--beige); color: var(--gray-700); }
    .timer-delete-btn { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 14px; color: var(--gray-400); background: transparent; border: 1px solid transparent; border-radius: 4px; cursor: pointer; margin-left: 8px; flex-shrink: 0; transition: all 0.2s; }
    .timer-delete-btn:hover { color: var(--red); background: var(--red-light); border-color: var(--red-light); }
    .schedule-form-group { margin-bottom: 14px; }
    .schedule-form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--gray-700); margin-bottom: 4px; }
    .schedule-form-group input, .schedule-form-group select { width: 100%; padding: 8px 10px; font-size: 13px; font-family: inherit; border: 1px solid var(--gray-300); border-radius: var(--radius); background: var(--white); }
    .schedule-type-tabs { display: flex; gap: 8px; margin-bottom: 16px; }
    .schedule-type-tab { flex: 1; padding: 10px; text-align: center; font-size: 13px; font-weight: 600; font-family: inherit; border: 2px solid var(--gray-200); border-radius: var(--radius); background: var(--white); cursor: pointer; transition: all 0.2s; color: var(--gray-600); }
    .schedule-type-tab.active { border-color: var(--gray-800); background: var(--beige); color: var(--gray-800); }
    .schedule-days { display: flex; gap: 6px; flex-wrap: wrap; }
    .schedule-day-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; font-family: inherit; border: 2px solid var(--gray-200); border-radius: 50%; background: var(--white); cursor: pointer; transition: all 0.2s; color: var(--gray-600); }
    .schedule-day-btn.active { border-color: var(--gray-800); background: var(--gray-800); color: var(--white); }
    .schedule-time-row { display: flex; align-items: center; gap: 8px; }
    .schedule-time-row input { width: auto; flex: 1; }
    .schedule-time-row span { font-size: 13px; color: var(--gray-500); white-space: nowrap; }
    .schedule-nextday-check { display: flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 12px; color: var(--gray-600); }
    .schedule-device-label { font-size: 12px; color: var(--gray-500); margin-bottom: 12px; padding: 8px 12px; background: var(--beige); border-radius: var(--radius); }
    .schedule-device-label strong { color: var(--gray-800); }
    .detail-schedule-list { border: 1px solid var(--gray-200); border-radius: var(--radius); overflow: hidden; margin-bottom: 10px; }
    .detail-schedule-item { display: flex; align-items: center; padding: 8px 12px; border-bottom: 1px solid var(--gray-100); font-size: 13px; }
    .detail-schedule-item:last-child { border-bottom: none; }
    .detail-schedule-item:nth-child(even) { background: var(--cream); }
    .detail-schedule-icon { width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 13px; margin-right: 8px; flex-shrink: 0; }
    .detail-schedule-icon.oneshot { background: #eff6ff; }
    .detail-schedule-icon.recurring { background: #f0fdf4; }
    .detail-schedule-info { flex: 1; min-width: 0; }
    .detail-schedule-main { font-size: 12px; font-weight: 600; color: var(--gray-800); }
    .detail-schedule-sub { font-size: 11px; color: var(--gray-500); }
    .detail-schedule-del { width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 13px; color: var(--gray-400); background: transparent; border: none; border-radius: 4px; cursor: pointer; flex-shrink: 0; transition: all 0.2s; }
    .detail-schedule-del:hover { color: var(--red); background: var(--red-light); }
    .detail-schedule-empty { padding: 16px; text-align: center; font-size: 12px; color: var(--gray-400); }
    .detail-schedule-add { display: flex; align-items: center; justify-content: center; gap: 4px; width: 100%; padding: 8px; font-size: 12px; font-weight: 600; font-family: inherit; color: var(--gray-500); background: var(--cream); border: 1px dashed var(--gray-300); border-radius: var(--radius); cursor: pointer; transition: all 0.2s; }
    .detail-schedule-add:hover { background: var(--beige); color: var(--gray-700); }
    .bulk-step-bar { display: flex; align-items: center; padding: 14px 20px; border-bottom: 1px solid var(--gray-200); }
    .bulk-step { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--gray-400); }
    .bulk-step.active { color: var(--gray-800); font-weight: 600; }
    .bulk-step.done { color: var(--green-dark); }
    .bulk-step-num { width: 22px; height: 22px; border-radius: 50%; border: 1.5px solid currentColor; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }
    .bulk-step.active .bulk-step-num { background: var(--gray-800); color: var(--white); border-color: var(--gray-800); }
    .bulk-step.done .bulk-step-num { background: var(--green-dark); color: var(--white); border-color: var(--green-dark); }
    .bulk-step-line { flex: 1; height: 1px; background: var(--gray-200); margin: 0 8px; }
    .bulk-panel { display: none; }
    .bulk-panel.active { display: block; }
    .bulk-section-label { font-size: 12px; color: var(--gray-500); margin-bottom: 10px; }
    .bulk-qty-row { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .bulk-qty-btn { width: 36px; height: 36px; border: 1px solid var(--gray-300); border-radius: var(--radius); background: var(--cream); font-family: inherit; font-size: 18px; font-weight: 700; color: var(--gray-700); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
    .bulk-qty-btn:hover { background: var(--beige); border-color: var(--gray-400); }
    .bulk-qty-input { width: 80px; padding: 8px 10px; font-size: 20px; font-weight: 700; font-family: inherit; border: 1px solid var(--gray-300); border-radius: var(--radius); text-align: center; color: var(--gray-800); }
    .bulk-qty-presets { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
    .bulk-qty-preset { padding: 4px 12px; font-size: 12px; font-family: inherit; border: 1px solid var(--gray-300); border-radius: 20px; background: var(--white); color: var(--gray-600); cursor: pointer; transition: all 0.15s; }
    .bulk-qty-preset:hover, .bulk-qty-preset.active { background: var(--gray-800); color: var(--white); border-color: var(--gray-800); }
    .bulk-qty-note { font-size: 11px; color: var(--gray-400); }
    .bulk-opt-card { border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 14px 16px; margin-bottom: 10px; cursor: pointer; transition: all 0.15s; }
    .bulk-opt-card:hover { border-color: var(--gray-400); }
    .bulk-opt-card.selected { border: 2px solid var(--gray-800); background: var(--beige); }
    .bulk-opt-header { display: flex; align-items: center; gap: 10px; }
    .bulk-opt-check { width: 18px; height: 18px; border: 1.5px solid var(--gray-400); border-radius: 4px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: transparent; transition: all 0.15s; }
    .bulk-opt-card.selected .bulk-opt-check { background: var(--gray-800); border-color: var(--gray-800); color: var(--white); }
    .bulk-opt-name { font-size: 14px; font-weight: 600; color: var(--gray-800); flex: 1; }
    .bulk-opt-badge { font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 4px; background: var(--green-light); color: var(--green-dark); }
    .bulk-opt-price { font-size: 13px; font-weight: 600; color: var(--gray-600); }
    .bulk-opt-desc { font-size: 12px; color: var(--gray-500); margin-top: 6px; padding-left: 28px; line-height: 1.5; }
    .bulk-form-group { margin-bottom: 14px; }
    .bulk-form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--gray-700); margin-bottom: 4px; }
    .bulk-form-required { color: var(--red); margin-left: 2px; }
    .bulk-form-input { width: 100%; padding: 8px 10px; font-size: 13px; font-family: inherit; border: 1px solid var(--gray-300); border-radius: var(--radius); background: var(--white); color: var(--gray-800); box-sizing: border-box; }
    .bulk-form-input:focus { outline: none; border-color: var(--gray-500); box-shadow: 0 0 0 2px rgba(168,162,158,0.15); }
    .bulk-summary-card { background: var(--beige); border-radius: var(--radius); padding: 16px; }
    .bulk-summary-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 13px; border-bottom: 1px solid var(--gray-200); }
    .bulk-summary-row:last-of-type { border-bottom: none; }
    .bulk-summary-label { color: var(--gray-500); }
    .bulk-summary-value { font-weight: 600; color: var(--gray-800); }
    .bulk-summary-subtotal { display: flex; justify-content: space-between; align-items: center; padding: 8px 0 4px; border-top: 1px solid var(--gray-300); margin-top: 4px; font-size: 13px; color: var(--gray-600); }
    .bulk-summary-tax { display: flex; justify-content: space-between; align-items: center; padding: 4px 0 8px; font-size: 12px; color: var(--gray-500); border-bottom: 2px solid var(--gray-300); }
    .bulk-summary-total { display: flex; justify-content: space-between; align-items: baseline; padding-top: 12px; margin-top: 4px; }
    .bulk-summary-total-label { font-size: 13px; color: var(--gray-600); }
    .bulk-summary-total-value { font-size: 22px; font-weight: 700; color: var(--gray-800); }
    .bulk-summary-note { font-size: 11px; color: var(--gray-400); margin-top: 12px; line-height: 1.6; }
    .bulk-loading { display: none; text-align: center; padding: 20px 0 8px; font-size: 13px; color: var(--gray-500); }
    .bulk-loading.show { display: block; }
    @media (max-width: 768px) { .status-grid { grid-template-columns: repeat(3, 1fr); } .toolbar { flex-direction: column; align-items: stretch; } .search-box { width: 100%; } .contract-info { flex-direction: column; } }
    @media (max-width: 480px) { .status-grid { grid-template-columns: repeat(2, 1fr); } }
</style>
@endsection

@section('content')


    @if(($stats['alert'] ?? 0) > 0)
        <div class="alert-banner warning">
            <span>🔴 <strong>{{ $stats['alert'] }}件</strong>のデバイスで24時間以上検知がありません（要確認）</span>
            <button class="alert-banner-btn" onclick="filterByStatus('alert')">確認する</button>
        </div>
    @endif
    @if(($stats['offline'] ?? 0) > 0)
        <div class="alert-banner offline">
            <span>⚫ <strong>{{ $stats['offline'] }}件</strong>のデバイスが48時間以上通信していません（電波障害または電池切れの可能性）</span>
            <button class="alert-banner-btn" onclick="filterByStatus('offline')">確認する</button>
        </div>
    @endif

    <div class="status-grid">
        <div class="status-card {{ request('status') === 'normal' ? 'active' : '' }}" onclick="filterByStatus('normal')">
            <div class="status-value green">{{ $stats['normal'] ?? 0 }}</div>
            <div class="status-label"><span class="status-dot green"></span> 正常</div>
        </div>
        <div class="status-card {{ request('status') === 'warning' ? 'active' : '' }}" onclick="filterByStatus('warning')">
            <div class="status-value yellow">{{ $stats['warning'] ?? 0 }}</div>
            <div class="status-label"><span class="status-dot yellow"></span> 注意</div>
        </div>
        <div class="status-card {{ request('status') === 'alert' ? 'active' : '' }}" onclick="filterByStatus('alert')">
            <div class="status-value red">{{ $stats['alert'] ?? 0 }}</div>
            <div class="status-label"><span class="status-dot red"></span> 警告</div>
        </div>
        <div class="status-card {{ request('status') === 'offline' ? 'active' : '' }}" onclick="filterByStatus('offline')">
            <div class="status-value gray">{{ $stats['offline'] ?? 0 }}</div>
            <div class="status-label"><span class="status-dot gray"></span> 離線</div>
        </div>
        <div class="status-card {{ request('status') === 'vacant' ? 'active' : '' }}" onclick="filterByStatus('vacant')">
            <div class="status-value light">{{ $stats['vacant'] ?? 0 }}</div>
            <div class="status-label"><span class="status-dot light"></span> 空室</div>
        </div>
    </div>

    <div class="status-legend">
        <span>正常: 検知あり</span><span>注意: 電池低下/未検知気味</span><span>警告: 長時間未検知</span><span>離線: 通信途絶</span><span>空室: デバイス未割当</span>
    </div>

    <div class="toolbar">
        <div class="toolbar-left">
            <form method="GET" action="{{ route('partner.org.dashboard') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                <div class="search-box">
                    <span>🔍</span>
                    <input type="text" name="search" placeholder="部屋番号・名前で検索..." value="{{ request('search') }}">
                </div>
                <select name="status" class="filter-select">
                    <option value="">すべてのステータス</option>
                    <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>🟢 正常のみ</option>
                    <option value="warning" {{ request('status') === 'warning' ? 'selected' : '' }}>🟡 注意のみ</option>
                    <option value="alert" {{ request('status') === 'alert' ? 'selected' : '' }}>🔴 警告のみ</option>
                    <option value="offline" {{ request('status') === 'offline' ? 'selected' : '' }}>⚫ 離線のみ</option>
                    <option value="vacant" {{ request('status') === 'vacant' ? 'selected' : '' }}>⚪ 空室のみ</option>
                </select>
                <select name="watch" class="filter-select">
                    <option value="">すべての外出モード状態</option>
                    <option value="off" {{ request('watch') === 'off' ? 'selected' : '' }}>外出モードOFF（通常）</option>
                    <option value="on" {{ request('watch') === 'on' ? 'selected' : '' }}>外出モードON（外出中）</option>
                    <option value="timer" {{ request('watch') === 'timer' ? 'selected' : '' }}>🚶 外出スケジュール設定中</option>
                </select>
                <button type="submit" class="btn btn-sm btn-secondary">絞り込み</button>
            </form>
            <span class="toolbar-count">登録: <strong>{{ $devices->total() ?? 0 }}</strong> / {{ $organization->device_limit ?? 100 }}台</span>
        </div>
        <div class="toolbar-right">
            <button class="toolbar-btn" onclick="showNotificationModal()">🔔 通知設定</button>
            <button class="toolbar-btn" onclick="showTimerListModal()">🚶 外出スケジュール一覧</button>
            <button class="toolbar-btn" onclick="showAddDeviceModal()">デバイス新規お申込み</button>
            <a href="{{ route('partner.org.csv') }}" class="toolbar-btn">📥 CSV出力</a>
        </div>
    </div>

    <div class="table-card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>状態</th><th>部屋 / 名前</th><th>デバイスID</th><th>外出モード</th><th>最終検知</th><th>電池</th><th>電波</th><th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                        @php
                            $assignment = $device->orgAssignment ?? null;
                            $roomNumber = $assignment ? $assignment->room_number : null;
                            $tenantName = $assignment ? $assignment->tenant_name : null;
                            $isVacant = !$assignment || !$tenantName;
                            $displayStatus = $isVacant ? 'vacant' : $device->status;
                            $lastDetected = $device->last_human_detected_at;
                            $timeSince = $lastDetected ? $lastDetected->locale('ja')->diffForHumans() : null;
                            $rssi = $device->rssi;
                            $signalLabel = '-';
                            if ($rssi !== null) {
                                if ($rssi > -70) $signalLabel = '良好';
                                elseif ($rssi > -85) $signalLabel = '普通';
                                else $signalLabel = '弱い';
                            }
                        @endphp
                        <tr id="row-{{ $device->device_id }}">
                            <td>
                                @switch($displayStatus)
                                    @case('normal') <span class="device-status normal">正常</span> @break
                                    @case('warning') <span class="device-status warning">注意</span> @break
                                    @case('alert') <span class="device-status alert">警告</span><button class="clear-alert-btn" onclick="confirmClearAlert('{{ $device->device_id }}', '{{ $roomNumber }}', '{{ $tenantName }}')">✕ 解除</button> @break
                                    @case('offline') <span class="device-status offline">離線</span> @break
                                    @case('vacant') <span class="device-status vacant">空室</span> @break
                                    @default <span class="device-status offline">-</span>
                                @endswitch
                            </td>
                            <td>
                                @if($roomNumber)
                                    <strong>{{ $roomNumber }}</strong><br>
                                    <span style="font-size:12px;color:var(--gray-500);">{{ $tenantName ?: '-' }}</span>
                                @else
                                    <span style="color:var(--gray-400);">-</span>
                                @endif
                            </td>
                            <td class="mono">{{ $device->device_id }}</td>
                            <td>
                                @if($assignment)
                                    <label class="watch-toggle away">
                                        <input type="checkbox" {{ $device->away_mode ? 'checked' : '' }} onchange="toggleAwayMode('{{ $device->device_id }}', this.checked, this)">
                                        <span class="watch-slider"></span>
                                    </label>
                                    @if($device->away_until) <span class="watch-timer-icon">⏰</span> @endif
                                @endif
                            </td>
                            <td style="font-size:12px;">{{ $timeSince ?: '-' }}</td>
                            <td class="{{ $device->battery_pct && $device->battery_pct < 20 ? 'battery-low' : '' }}" style="font-size:12px;">{{ $device->battery_pct ? $device->battery_pct . '%' : '-' }}</td>
                            <td class="{{ $rssi !== null && $rssi < -85 ? 'signal-weak' : '' }}" style="font-size:12px;">{{ $signalLabel }}</td>
                            <td>
                                <button class="action-btn" onclick="showDeviceDetail('{{ $device->device_id }}')">詳細</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                        <td colspan="8" style="text-align:center;color:var(--gray-400);padding:40px 12px;">
                            デバイスがありません。デバイス追加を行ってください。<br>
                            <button class="btn btn-sm btn-primary" style="margin-top:10px;" onclick="showAddDeviceModal()">＋ デバイスを追加する</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($devices->hasPages())
            <div class="pagination-bar">
                <span class="pagination-info">{{ $devices->firstItem() }}〜{{ $devices->lastItem() }}件 / 全{{ $devices->total() }}件</span>
                <div class="pagination-buttons">
                    @if($devices->onFirstPage()) <span class="page-btn disabled">‹</span> @else <a href="{{ $devices->previousPageUrl() }}" class="page-btn">‹</a> @endif
                    @foreach($devices->getUrlRange(max(1, $devices->currentPage() - 2), min($devices->lastPage(), $devices->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page == $devices->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($devices->currentPage() + 2 < $devices->lastPage()) <span class="page-btn disabled">...</span><a href="{{ $devices->url($devices->lastPage()) }}" class="page-btn">{{ $devices->lastPage() }}</a> @endif
                    @if($devices->hasMorePages()) <a href="{{ $devices->nextPageUrl() }}" class="page-btn">›</a> @else <span class="page-btn disabled">›</span> @endif
                </div>
            </div>
        @endif
    </div>

    {{-- モーダル: デバイス新規お申込み（4ステップ） --}}
    <div id="addDeviceModal" class="modal-overlay" onclick="if(event.target===this)hideModal('addDeviceModal')">
        <div class="modal">
            <div class="modal-header">
                <h3>デバイス新規お申込み</h3>
                <button class="modal-close" onclick="hideModal('addDeviceModal')">×</button>
            </div>
            <div class="bulk-step-bar">
                <div class="bulk-step active" id="bulk-step-ind-1"><div class="bulk-step-num">1</div><span>台数</span></div>
                <div class="bulk-step-line"></div>
                <div class="bulk-step" id="bulk-step-ind-2"><div class="bulk-step-num">2</div><span>オプション</span></div>
                <div class="bulk-step-line"></div>
                <div class="bulk-step" id="bulk-step-ind-3"><div class="bulk-step-num">3</div><span>配送先</span></div>
                <div class="bulk-step-line"></div>
                <div class="bulk-step" id="bulk-step-ind-4"><div class="bulk-step-num">4</div><span>確認・決済</span></div>
            </div>
            <div id="bulk-panel-1" class="bulk-panel active modal-body">
                <p class="bulk-section-label">追加する台数を選択してください（1〜300台）</p>
                <div class="bulk-qty-row">
                    <button type="button" class="bulk-qty-btn" id="bulk-qty-minus">−</button>
                    <input type="number" class="bulk-qty-input" id="bulk-qty-input" value="10" min="1" max="300">
                    <button type="button" class="bulk-qty-btn" id="bulk-qty-plus">＋</button>
                </div>
                <div class="bulk-qty-presets">
                    <button type="button" class="bulk-qty-preset" data-val="10">10台</button>
                    <button type="button" class="bulk-qty-preset" data-val="20">20台</button>
                    <button type="button" class="bulk-qty-preset" data-val="50">50台</button>
                    <button type="button" class="bulk-qty-preset" data-val="100">100台</button>
                    <button type="button" class="bulk-qty-preset" data-val="200">200台</button>
                    <button type="button" class="bulk-qty-preset" data-val="300">300台</button>
                </div>
                <p class="bulk-qty-note">生成されたデバイスIDとPINは一覧CSVでダウンロードできます</p>
            </div>
            <div id="bulk-panel-2" class="bulk-panel modal-body">
                <p class="bulk-section-label">オプションを選択してください（複数可）</p>
                <div class="bulk-opt-card" id="bulk-opt-ai" onclick="bulkToggleOpt('ai')">
                    <div class="bulk-opt-header"><div class="bulk-opt-check" id="bulk-opt-ai-check">✓</div><span class="bulk-opt-name">AIコール</span><span class="bulk-opt-badge">Phase 3</span><span class="bulk-opt-price">+¥300 / 台 / 月</span></div>
                    <p class="bulk-opt-desc">異常検知時にAIが自動音声でご家族に電話通知します。</p>
                </div>
                <div class="bulk-opt-card" id="bulk-opt-sms" onclick="bulkToggleOpt('sms')">
                    <div class="bulk-opt-header"><div class="bulk-opt-check" id="bulk-opt-sms-check">✓</div><span class="bulk-opt-name">SMS通知</span><span class="bulk-opt-price">+¥100 / 台 / 月</span></div>
                    <p class="bulk-opt-desc">アラート時にSMSで緊急連絡先へ通知します。</p>
                </div>
            </div>
            <div id="bulk-panel-3" class="bulk-panel modal-body">
                <p class="bulk-section-label">デバイスの配送先をご入力ください</p>
                <div id="bulk-preset-area" style="margin-bottom:12px;"></div>
                <div class="bulk-form-group"><label>お名前<span class="bulk-form-required">*</span></label><input type="text" class="bulk-form-input" id="bulk-delivery-name" placeholder="山田 太郎"></div>
                <div class="bulk-form-group"><label>郵便番号<span class="bulk-form-required">*</span></label><input type="text" class="bulk-form-input" id="bulk-delivery-postal" placeholder="000-0000" maxlength="8"></div>
                <div class="bulk-form-group"><label>住所<span class="bulk-form-required">*</span></label><input type="text" class="bulk-form-input" id="bulk-delivery-address" placeholder="東京都千代田区〇〇 1-2-3"></div>
                <div class="bulk-form-group"><label>電話番号<span class="bulk-form-required">*</span></label><input type="tel" class="bulk-form-input" id="bulk-delivery-phone" placeholder="090-0000-0000"></div>
            </div>
            <div id="bulk-panel-4" class="bulk-panel modal-body">
                <div class="bulk-summary-card">
                    <div class="bulk-summary-row"><span class="bulk-summary-label">追加台数</span><span class="bulk-summary-value" id="bulk-sum-qty">10台</span></div>
                    <div class="bulk-summary-row"><span class="bulk-summary-label">基本料金</span><span class="bulk-summary-value">¥1,000 / 台 / 月</span></div>
                    <div class="bulk-summary-row" id="bulk-sum-ai-row" style="display:none;"><span class="bulk-summary-label">AIコール</span><span class="bulk-summary-value">+¥300 / 台 / 月</span></div>
                    <div class="bulk-summary-row" id="bulk-sum-sms-row" style="display:none;"><span class="bulk-summary-label">SMS通知</span><span class="bulk-summary-value">+¥100 / 台 / 月</span></div>
                    <div class="bulk-summary-subtotal"><span class="bulk-summary-label">小計（税抜）</span><span class="bulk-summary-value" id="bulk-sum-subtotal">¥7,000 / 月</span></div>
                    <div class="bulk-summary-tax"><span class="bulk-summary-label">消費税（10%）</span><span class="bulk-summary-value" id="bulk-sum-tax">¥700 / 月</span></div>
                    <div class="bulk-summary-total"><span class="bulk-summary-total-label">月額合計（税込）</span><span class="bulk-summary-total-value" id="bulk-sum-total">¥7,700 / 月</span></div>
                </div>
                <div id="bulk-card-info" style="margin-bottom:14px;padding:12px 14px;background:var(--white);border:1px solid var(--gray-200);border-radius:var(--radius);font-size:13px;display:flex;align-items:center;justify-content:space-between;"><span style="color:var(--gray-500);">💳 お支払いカード</span><span id="bulk-card-display" style="font-weight:600;">読み込み中...</span></div>
                <p class="bulk-summary-note">※ 24ヶ月最低契約。解約時は¥8,400の違約金が発生します。<br>※「決済へ進む」を押すとデバイスが生成され、IDとPINのCSVが自動でダウンロードされます。</p>
                <div class="bulk-loading" id="bulk-loading">デバイスを生成中です。しばらくお待ちください...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="bulk-btn-back" style="display:none;" onclick="bulkPrevStep()">戻る</button>
                <button type="button" class="btn btn-primary" id="bulk-btn-next" onclick="bulkNextStep()">次へ →</button>
            </div>
        </div>
    </div>

    {{-- モーダル: デバイス削除 --}}
    <div id="deleteModal" class="modal-overlay" onclick="if(event.target===this)hideModal('deleteModal')">
        <div class="modal"><div class="modal-header"><h3>⚠️ デバイス削除</h3><button class="modal-close" onclick="hideModal('deleteModal')">×</button></div>
            <form id="deleteForm" method="POST" action="">@csrf
                <div class="modal-body"><p>デバイス <strong id="deleteDeviceId" class="mono">-</strong> を組織から削除しますか？</p><p style="color:var(--gray-500);font-size:13px;margin-top:8px;">デバイスの登録データは残りますが、組織との紐付けが解除されます。</p></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="hideModal('deleteModal')">キャンセル</button><button type="submit" class="btn btn-danger">削除する</button></div>
            </form>
        </div>
    </div>

    {{-- モーダル: 警告解除 --}}
    <div id="clearAlertModal" class="modal-overlay" onclick="if(event.target===this)hideModal('clearAlertModal')">
        <div class="modal"><div class="modal-header"><h3>⚠️ 警告解除</h3><button class="modal-close" onclick="hideModal('clearAlertModal')">×</button></div>
            <div class="modal-body"><p id="clearAlertTarget" style="margin-bottom:8px;"></p><p>このデバイスの警告を解除しますか？</p><p style="color:var(--gray-500);font-size:13px;margin-top:8px;">ステータスが初期状態（-）に戻り、検知ログもクリアされます。<br>退去・長期不在等でデバイスを初期化する場合にご利用ください。</p></div>
            <div class="modal-footer"><button class="btn btn-secondary" onclick="hideModal('clearAlertModal')">キャンセル</button><button class="btn btn-danger" onclick="executeClearAlert()">警告を解除する</button></div>
        </div>
    </div>

    {{-- モーダル: デバイス詳細 --}}
    <div id="detailModal" class="modal-overlay" onclick="if(event.target===this)hideModal('detailModal')">
        <div class="modal" style="max-width:560px;"><div class="modal-header"><h3>📋 デバイス詳細</h3><button class="modal-close" onclick="hideModal('detailModal')">×</button></div>
            <div class="modal-body">
                <div class="detail-status-row"><div class="detail-status-badge normal" id="detailStatusBadge">-</div><button class="detail-clear-alert-btn" id="detailClearAlertBtn" style="display:none;" onclick="confirmClearAlertFromDetail()">✕ 警告解除</button></div>
                <div class="detail-section" style="margin-bottom:16px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <label class="watch-toggle"><input type="checkbox" id="detailNotifyEnabled" checked onchange="toggleNotifyService(this.checked)"><span class="watch-slider"></span></label>
                            <span style="font-size:13px;font-weight:600;color:var(--gray-700);">🔔 端末サブスクリプション</span>
                            <span id="detailNotifyLabel" style="font-size:12px;color:var(--gray-500);">有効</span>
                        </div>
                        <button class="btn btn-sm btn-secondary" onclick="showSubscriptionModal()">📋 契約プラン</button>
                    </div>
                    <p class="detail-notify-note">※ご契約後〇ヶ月は停止機能はご利用になれません。</p>
                </div>
                <div class="detail-section"><div class="detail-grid">
                    <div class="detail-item"><p class="detail-item-label">デバイスID</p><p class="detail-item-value mono" id="detailDeviceId">-</p></div>
                    <div class="detail-item"><p class="detail-item-label">最終検知</p><p class="detail-item-value" id="detailLastDetected">-</p></div>
                    <div class="detail-item"><p class="detail-item-label">部屋番号</p><input type="text" class="detail-form-input" id="detailRoomInput" placeholder="101"></div>
                    <div class="detail-item"><p class="detail-item-label">入居者名</p><input type="text" class="detail-form-input" id="detailTenantInput" placeholder="山田 太郎"></div>
                </div></div>
                <div class="detail-section"><div class="detail-section-title">📊 デバイス状態</div><div class="detail-grid">
                    <div class="detail-item"><p class="detail-item-label">電池残量</p><p class="detail-item-value" id="detailBattery">-</p></div>
                    <div class="detail-item"><p class="detail-item-label">電波強度</p><p class="detail-item-value" id="detailSignal">-</p></div>
                </div></div>
                <div class="detail-section"><div class="detail-section-title">⚙️ 外出モード設定</div><div class="detail-grid">
                    <div class="detail-item"><p class="detail-item-label">アラート時間</p>
                        <select class="detail-form-input" id="detailAlertHoursInput">
                            <option value="12">12時間</option><option value="24">24時間</option><option value="36">36時間</option><option value="48">48時間</option><option value="72">72時間</option>
                        </select>
                    </div>
                    <div class="detail-item"><p class="detail-item-label">設置高さ</p>
                        <div style="display:flex;align-items:center;gap:4px;"><input type="number" class="detail-form-input" id="detailHeightInput" min="100" max="300" style="width:70px;"><span style="font-size:12px;color:var(--gray-500);">cm</span></div>
                    </div>
                    <div class="detail-item"><p class="detail-item-label">ペット除外</p>
                        <select class="detail-form-input" id="detailPetExclusionInput"><option value="0">OFF</option><option value="1">ON</option></select>
                    </div>
                    <div class="detail-item"><p class="detail-item-label">外出モード</p>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <label class="watch-toggle away"><input type="checkbox" id="detailAwayModeToggle" onchange="toggleAwayModeFromDetail(this.checked, this)"><span class="watch-slider"></span></label>
                            <span id="detailAwayModeLabel" style="font-size:12px;color:var(--gray-600);">-</span>
                        </div>
                    </div>
                </div></div>
                <div class="detail-section"><div class="detail-section-title">📝 登録情報</div><div class="detail-grid">
                    <div class="detail-item"><p class="detail-item-label">登録日</p><p class="detail-item-value" id="detailRegistered">-</p></div>
                    <div class="detail-item"><p class="detail-item-label">メモ</p><input type="text" class="detail-form-input" id="detailMemoInput" placeholder="メモを入力..." maxlength="200"></div>
                </div></div>
                <div class="detail-section"><div class="detail-section-title">🚶 外出スケジュール</div><div id="detailScheduleList"></div><button class="detail-schedule-add" onclick="openScheduleAddFromDetail()">＋ 外出スケジュール追加</button></div>
            </div>
            <div class="modal-footer" style="justify-content:space-between;">
                <button type="button" class="cancel-link" onclick="showCancelFlow()">解約</button>
                <div style="display:flex;gap:8px;">
                    <button class="btn btn-secondary" onclick="hideModal('detailModal')">閉じる</button>
                    <button class="btn btn-primary" onclick="saveDetailChanges()">保存</button>
                </div>
            </div>
        </div>
    </div>

    {{-- モーダル: デバイス編集（後方互換） --}}
    <div id="editModal" class="modal-overlay" onclick="if(event.target===this)hideModal('editModal')">
        <div class="modal"><div class="modal-header"><h3>✏️ デバイス編集</h3><button class="modal-close" onclick="hideModal('editModal')">×</button></div>
            <form id="editForm" method="POST" action="">@csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group"><label class="form-label">デバイスID</label><input type="text" class="form-input" id="editDeviceId" disabled style="background:var(--gray-100);"></div>
                    <div class="form-group"><label class="form-label">部屋番号</label><input type="text" class="form-input" name="room_number" id="editRoomNumber" placeholder="101"></div>
                    <div class="form-group"><label class="form-label">入居者名</label><input type="text" class="form-input" name="tenant_name" id="editTenantName"></div>
                    <div class="form-group"><label class="form-label">メモ</label><input type="text" class="form-input" name="memo" id="editMemo"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="hideModal('editModal')">キャンセル</button><button type="submit" class="btn btn-primary">保存</button></div>
            </form>
        </div>
    </div>

    {{-- モーダル: 外出モードON確認 --}}
    <div id="watchOffModal" class="modal-overlay" onclick="if(event.target===this)hideModal('watchOffModal')">
        <div class="modal"><div class="modal-header"><h3>🚶 外出モードをONにしますか？</h3><button class="modal-close" onclick="hideModal('watchOffModal')">×</button></div>
            <div class="modal-body"><p><strong>⚠️ 注意:</strong> 外出モードをONにすると、このデバイスの未検知アラートが送信されなくなります。</p><p style="color:var(--gray-500);font-size:13px;margin-top:8px;">外出・旅行などで一時的に通知を止めたい場合にご利用ください。</p></div>
            <div class="modal-footer"><button class="btn btn-secondary" onclick="cancelAwayModeOn()">キャンセル</button><button class="btn btn-danger" onclick="executeAwayModeOn()">外出モードをONにする</button></div>
        </div>
    </div>

    {{-- モーダル: タイマー一覧 --}}
    <div id="timerListModal" class="modal-overlay" onclick="if(event.target===this)hideModal('timerListModal')">
        <div class="modal" style="max-width:620px;"><div class="modal-header"><h3>🚶 外出スケジュール一覧</h3><button class="modal-close" onclick="hideModal('timerListModal')">×</button></div>
            <div class="modal-body" id="timerListBody"><div class="timer-list-loading">読み込み中...</div></div>
            <div class="modal-footer"><button class="btn btn-secondary" onclick="hideModal('timerListModal')">閉じる</button></div>
        </div>
    </div>

    {{-- モーダル: スケジュール追加 --}}
    <div id="scheduleAddModal" class="modal-overlay" onclick="if(event.target===this)hideScheduleAddModal()">
        <div class="modal" style="max-width:480px;"><div class="modal-header"><h3>🚶 外出スケジュール追加</h3><button class="modal-close" onclick="hideScheduleAddModal()">×</button></div>
            <div class="modal-body">
                <div class="schedule-device-label" id="scheduleDeviceLabel">対象: <strong>-</strong></div>
                <div class="schedule-type-tabs">
                    <button class="schedule-type-tab active" id="tabOneshot" onclick="switchScheduleType('oneshot')">📅 単発</button>
                    <button class="schedule-type-tab" id="tabRecurring" onclick="switchScheduleType('recurring')">🔁 定期</button>
                </div>
                <div id="oneshotForm">
                    <div class="schedule-form-group"><label>開始日時</label><input type="datetime-local" id="schedStartAt"></div>
                    <div class="schedule-form-group"><label>終了日時（空欄＝手動復帰）</label><input type="datetime-local" id="schedEndAt"></div>
                </div>
                <div id="recurringForm" style="display:none;">
                    <div class="schedule-form-group"><label>曜日</label>
                        <div class="schedule-days" id="scheduleDays">
                            <button type="button" class="schedule-day-btn" data-day="0" onclick="toggleDay(this)">日</button>
                            <button type="button" class="schedule-day-btn" data-day="1" onclick="toggleDay(this)">月</button>
                            <button type="button" class="schedule-day-btn" data-day="2" onclick="toggleDay(this)">火</button>
                            <button type="button" class="schedule-day-btn" data-day="3" onclick="toggleDay(this)">水</button>
                            <button type="button" class="schedule-day-btn" data-day="4" onclick="toggleDay(this)">木</button>
                            <button type="button" class="schedule-day-btn" data-day="5" onclick="toggleDay(this)">金</button>
                            <button type="button" class="schedule-day-btn" data-day="6" onclick="toggleDay(this)">土</button>
                        </div>
                    </div>
                    <div class="schedule-form-group"><label>時間帯</label>
                        <div class="schedule-time-row"><input type="time" id="schedStartTime"><span>〜</span><input type="time" id="schedEndTime"></div>
                        <label class="schedule-nextday-check"><input type="checkbox" id="schedNextDay"> 翌日にまたがる</label>
                    </div>
                </div>
                <div class="schedule-form-group"><label>メモ（任意）</label><input type="text" id="schedMemo" placeholder="例: デイサービス" maxlength="200"></div>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary" onclick="hideScheduleAddModal()">キャンセル</button><button class="btn btn-primary" onclick="submitSchedule()">追加</button></div>
        </div>
    </div>

    {{-- モーダル: スケジュール削除 --}}
    <div id="scheduleDeleteModal" class="modal-overlay" onclick="if(event.target===this)hideModal('scheduleDeleteModal')">
        <div class="modal"><div class="modal-header"><h3>⚠️ 外出スケジュール削除</h3><button class="modal-close" onclick="hideModal('scheduleDeleteModal')">×</button></div>
            <div class="modal-body"><p>この外出スケジュールを削除しますか？</p><p id="scheduleDeleteDetail" style="color:var(--gray-500);font-size:13px;margin-top:8px;"></p></div>
            <div class="modal-footer"><button class="btn btn-secondary" onclick="hideModal('scheduleDeleteModal')">キャンセル</button><button class="btn btn-danger" onclick="executeDeleteSchedule()">削除する</button></div>
        </div>
    </div>

    {{-- モーダル: 契約プラン --}}
    <div id="subscriptionModal" class="modal-overlay" onclick="if(event.target===this)hideModal('subscriptionModal')">
        <div class="modal" style="max-width:500px;">
            <div class="modal-header"><h3>📋 契約プラン</h3><button class="modal-close" onclick="hideModal('subscriptionModal')">×</button></div>
            <div class="modal-body">
                <div style="font-size:12px;color:var(--gray-500);margin-bottom:16px;">対象デバイス：<span id="subModalDeviceId" class="mono" style="font-size:12px;"></span></div>

                {{-- SMS通知 --}}
                <div style="border:1px solid var(--gray-200);border-radius:var(--radius);padding:14px;margin-bottom:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                        <p style="font-size:13px;font-weight:600;color:var(--gray-700);">💬 SMS通知 <span style="font-size:11px;font-weight:400;color:var(--gray-500);">+¥100/台/月</span></p>
                        <div id="detailSmsActionArea"></div>
                    </div>
                    <div id="detailSmsInputs">
                        <input type="tel" class="detail-form-input" id="detailSmsPhone1" placeholder="09012345678" style="margin-bottom:6px;">
                        <input type="tel" class="detail-form-input" id="detailSmsPhone2" placeholder="09012345678（任意）">
                    </div>
                </div>

                {{-- AIコール --}}
                <div style="border:1px solid var(--gray-200);border-radius:var(--radius);padding:14px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                        <p style="font-size:13px;font-weight:600;color:var(--gray-700);">📞 電話通知（AIコール）<span style="font-size:11px;font-weight:400;color:var(--gray-500);">+¥300/台/月</span></p>
                        <div id="detailVoiceActionArea"></div>
                    </div>
                    <div id="detailVoiceInputs">
                        <input type="tel" class="detail-form-input" id="detailVoicePhone1" placeholder="09012345678" style="margin-bottom:6px;">
                        <input type="tel" class="detail-form-input" id="detailVoicePhone2" placeholder="09012345678（任意）">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hideModal('subscriptionModal'); showDeviceDetail(currentDetailDeviceId)">閉じる</button>
                <button class="btn btn-primary" onclick="saveDetailNotification(); hideModal('subscriptionModal'); showDeviceDetail(currentDetailDeviceId)">保存</button>
            </div>
        </div>
    </div>

    {{-- モーダル: オプション申込み確認 --}}
    <div id="optionConfirmModal" class="modal-overlay" onclick="if(event.target===this)hideModal('optionConfirmModal')">
        <div class="modal" style="max-width:440px;">
            <div class="modal-header"><h3 id="optionConfirmTitle">オプション申し込み確認</h3><button class="modal-close" onclick="hideModal('optionConfirmModal')">×</button></div>
            <div class="modal-body">
                <p id="optionConfirmBody" style="font-size:14px;color:var(--gray-700);margin-bottom:12px;"></p>
                <div style="background:var(--beige);border-radius:var(--radius);padding:12px 14px;font-size:13px;color:var(--gray-600);margin-bottom:10px;">
                    翌月1日より月額に加算されます。当月分は無料です。
                </div>
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--gray-700);">
                    <span>💳 お支払いカード：</span><span id="optionConfirmCard" style="font-weight:600;"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hideModal('optionConfirmModal')">キャンセル</button>
                <button class="btn btn-primary" id="optionConfirmBtn">申し込む</button>
            </div>
        </div>
    </div>

    {{-- モーダル: オプション解約確認 --}}
    <div id="optionCancelModal" class="modal-overlay" onclick="if(event.target===this)hideModal('optionCancelModal')">
        <div class="modal" style="max-width:440px;">
            <div class="modal-header"><h3 id="optionCancelTitle">解約確認</h3><button class="modal-close" onclick="hideModal('optionCancelModal')">×</button></div>
            <div class="modal-body">
                <p id="optionCancelBody" style="font-size:14px;color:var(--gray-700);margin-bottom:12px;"></p>
                <div style="background:var(--yellow-light);border-radius:var(--radius);padding:12px 14px;font-size:13px;color:#a16207;">
                    解約後は翌月1日より月額から除外されます。
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hideModal('optionCancelModal')">戻る</button>
                <button class="btn btn-danger" id="optionCancelBtn">解約する</button>
            </div>
        </div>
    </div>

    {{-- モーダル: 解約・端末返却案内 --}}
    <div id="cancelFlowModal" class="modal-overlay" onclick="if(event.target===this)hideModal('cancelFlowModal')">
        <div class="modal" style="max-width:520px;">
            <div class="modal-header"><h3>📦 解約・端末返却のご案内</h3><button class="modal-close" onclick="hideModal('cancelFlowModal')">×</button></div>
            <div class="modal-body">
                <div style="background:var(--yellow-light);border-left:3px solid var(--yellow);padding:12px 14px;border-radius:var(--radius);margin-bottom:16px;">
                    <p style="font-size:13px;font-weight:600;color:#a16207;margin-bottom:4px;">⚠️ 解約前にご確認ください</p>
                    <p style="font-size:12px;color:#a16207;line-height:1.6;">ご契約から<strong>〇ヶ月以内</strong>の解約は、1台あたり<strong>¥8,400の違約金</strong>が発生します。</p>
                </div>
                <div class="detail-section">
                    <div class="detail-section-title">📋 解約・返却の流れ</div>
                    <div style="font-size:13px;color:var(--gray-700);line-height:1.8;">
                        <p style="margin-bottom:6px;"><strong>① 解約申請</strong></p>
                        <p style="color:var(--gray-500);font-size:12px;margin-bottom:12px;">下記の「解約を申請する」ボタン、またはお問い合わせフォームより解約のご意思をお知らせください。</p>
                        <p style="margin-bottom:6px;"><strong>② 端末の返送</strong></p>
                        <p style="color:var(--gray-500);font-size:12px;margin-bottom:12px;">端末を<strong>レターパックライト等の郵送</strong>にてご返送ください。送料はお客様のご負担となります。</p>
                        <p style="margin-bottom:6px;"><strong>③ 返送確認・解約完了</strong></p>
                        <p style="color:var(--gray-500);font-size:12px;margin-bottom:4px;">弊社にて返送を確認後、解約処理を行います。<strong>返送確認ができない場合は端末代金を請求させていただく場合があります。</strong></p>
                    </div>
                </div>
                <div style="background:var(--beige);border-radius:var(--radius);padding:12px 14px;font-size:12px;color:var(--gray-600);line-height:1.8;">
                    <strong>返送先：</strong>担当者よりご案内いたします<br>
                    <strong>お問い合わせ：</strong>管理者までご連絡ください
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="hideModal('cancelFlowModal')">閉じる</button>
                <a href="mailto:support@example.com" class="btn btn-danger" style="text-decoration:none;">解約を申請する</a>
            </div>
        </div>
    </div>

    @include('partner.partials.notification-modal')
    <div id="toast" class="toast"></div>
@endsection

@section('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';
function showModal(id) { document.getElementById(id).classList.add('show'); }
function hideModal(id) { document.getElementById(id).classList.remove('show'); }
function showToast(msg, type) { const t = document.getElementById('toast'); t.textContent = msg; t.className = 'toast ' + type + ' show'; setTimeout(() => t.classList.remove('show'), 3000); }
function escapeHtml(s) { if (!s) return ''; const d = document.createElement('div'); d.appendChild(document.createTextNode(s)); return d.innerHTML; }
function filterByStatus(s) { const u = new URL(window.location); u.searchParams.get('status') === s ? u.searchParams.delete('status') : u.searchParams.set('status', s); window.location = u; }

// ===== デバイス新規お申込み（4ステップ） =====
var bulkStep = 1;
var bulkOpts = { ai: false, sms: false };

function showAddDeviceModal() {
    bulkStep = 1;
    bulkOpts = { ai: false, sms: false };
    document.getElementById('bulk-qty-input').value = 10;
    document.querySelectorAll('.bulk-qty-preset').forEach(function(b) { b.classList.remove('active'); });
    ['ai', 'sms'].forEach(function(k) { document.getElementById('bulk-opt-' + k).classList.remove('selected'); });
    ['bulk-delivery-name', 'bulk-delivery-postal', 'bulk-delivery-address', 'bulk-delivery-phone'].forEach(function(id) { document.getElementById(id).value = ''; });
    document.getElementById('bulk-loading').classList.remove('show');
    var pa = document.getElementById('bulk-preset-area');
    if (pa && deliveryPreset.name) { pa.innerHTML = '<button type="button" onclick="applyPreset()" style="width:100%;padding:10px 14px;font-size:13px;font-family:inherit;background:var(--beige);border:1px solid var(--gray-300);border-radius:var(--radius);cursor:pointer;text-align:left;color:var(--gray-700);">📦 前回の配送先を使用: ' + escapeHtml(deliveryPreset.name) + ' / ' + escapeHtml(deliveryPreset.postal) + '</button>'; } else if (pa) { pa.innerHTML = ''; }
    bulkUpdateStepUI();
    showModal('addDeviceModal');
}

function bulkUpdateStepUI() {
    [1, 2, 3, 4].forEach(function(i) {
        document.getElementById('bulk-panel-' + i).classList.toggle('active', i === bulkStep);
        var ind = document.getElementById('bulk-step-ind-' + i);
        ind.className = 'bulk-step' + (i < bulkStep ? ' done' : i === bulkStep ? ' active' : '');
    });
    document.getElementById('bulk-btn-back').style.display = bulkStep > 1 ? '' : 'none';
    var btn = document.getElementById('bulk-btn-next');
    if (bulkStep === 4) { btn.textContent = '決済へ進む'; }
    else if (bulkStep === 3) { btn.textContent = '確認へ →'; }
    else { btn.textContent = '次へ →'; }
    btn.disabled = false;
}

function bulkGetQty() { var v = parseInt(document.getElementById('bulk-qty-input').value) || 1; if (v < 1) v = 1; if (v > 300) v = 300; return v; }

async function bulkUpdateSummary() {
    var q = bulkGetQty();
    var add = (bulkOpts.ai ? 300 : 0) + (bulkOpts.sms ? 100 : 0);
    var subtotal = (1000 + add) * q;
    var tax = Math.floor(subtotal * 0.1);
    var total = subtotal + tax;
    document.getElementById('bulk-sum-qty').textContent = q + '台';
    document.getElementById('bulk-sum-ai-row').style.display = bulkOpts.ai ? '' : 'none';
    document.getElementById('bulk-sum-sms-row').style.display = bulkOpts.sms ? '' : 'none';
    document.getElementById('bulk-sum-subtotal').textContent = '¥' + subtotal.toLocaleString() + ' / 月';
    document.getElementById('bulk-sum-tax').textContent = '¥' + tax.toLocaleString() + ' / 月';
    document.getElementById('bulk-sum-total').textContent = '¥' + total.toLocaleString() + ' / 月';
    var cardDisplay = document.getElementById('bulk-card-display');
    var nextBtn = document.getElementById('bulk-btn-next');
    try {
        var res = await fetch('/partner/org/card-info', { headers: { 'Accept': 'application/json' } });
        var data = await res.json();
        if (data.found) { cardDisplay.textContent = data.brand + ' **** ' + data.last4; cardDisplay.style.color = 'var(--gray-800)'; nextBtn.disabled = false; }
        else { cardDisplay.innerHTML = '<span style="color:var(--red);">未登録 — <a href="/partner/billing" style="color:var(--red);text-decoration:underline;">カードを登録する</a></span>'; nextBtn.disabled = true; }
    } catch(e) { cardDisplay.textContent = '取得できませんでした'; }
}

async function bulkNextStep() {
    if (bulkStep === 3) {
        if (!document.getElementById('bulk-delivery-name').value.trim()) { showToast('お名前を入力してください', 'error'); return; }
        if (!document.getElementById('bulk-delivery-postal').value.trim()) { showToast('郵便番号を入力してください', 'error'); return; }
        if (!document.getElementById('bulk-delivery-address').value.trim()) { showToast('住所を入力してください', 'error'); return; }
        if (!document.getElementById('bulk-delivery-phone').value.trim()) { showToast('電話番号を入力してください', 'error'); return; }
    }
    if (bulkStep < 4) { bulkStep++; bulkUpdateStepUI(); if (bulkStep === 4) await bulkUpdateSummary(); }
    else { bulkExecute(); }
}

function bulkPrevStep() { if (bulkStep > 1) { bulkStep--; bulkUpdateStepUI(); } }
function bulkToggleOpt(key) { bulkOpts[key] = !bulkOpts[key]; document.getElementById('bulk-opt-' + key).classList.toggle('selected', bulkOpts[key]); }

async function bulkExecute() {
    var btn = document.getElementById('bulk-btn-next');
    btn.disabled = true; btn.textContent = '処理中...';
    document.getElementById('bulk-loading').classList.add('show');
    try {
        var res = await fetch('{{ route("partner.org.devices.bulk-checkout") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ count: bulkGetQty(), opt_ai: bulkOpts.ai, opt_sms: bulkOpts.sms, delivery_name: document.getElementById('bulk-delivery-name').value, delivery_postal: document.getElementById('bulk-delivery-postal').value, delivery_address: document.getElementById('bulk-delivery-address').value, delivery_phone: document.getElementById('bulk-delivery-phone').value })
        });
        var data = await res.json();
        if (res.ok && data.success) { hideModal('addDeviceModal'); showToast(data.count + '台のデバイスを追加しました', 'success'); setTimeout(function() { location.reload(); }, 1000); }
        else { showToast(data.message || '追加に失敗しました', 'error'); btn.disabled = false; btn.textContent = '決済へ進む'; document.getElementById('bulk-loading').classList.remove('show'); }
    } catch (e) { console.error(e); showToast('通信エラーが発生しました', 'error'); btn.disabled = false; btn.textContent = '決済へ進む'; document.getElementById('bulk-loading').classList.remove('show'); }
}

// ===== 配送先プリセット =====
var deliveryPreset = { name: '{{ $organization->delivery_name ?? "" }}', postal: '{{ $organization->delivery_postal ?? "" }}', address: '{{ $organization->delivery_address ?? "" }}', phone: '{{ $organization->delivery_phone ?? "" }}' };
function applyPreset() { document.getElementById('bulk-delivery-name').value = deliveryPreset.name; document.getElementById('bulk-delivery-postal').value = deliveryPreset.postal; document.getElementById('bulk-delivery-address').value = deliveryPreset.address; document.getElementById('bulk-delivery-phone').value = deliveryPreset.phone; }

function bulkSyncPresets() { var v = parseInt(document.getElementById('bulk-qty-input').value) || 0; document.querySelectorAll('.bulk-qty-preset').forEach(function(b) { b.classList.toggle('active', parseInt(b.dataset.val) === v); }); }
bulkSyncPresets();

document.getElementById('bulk-qty-minus').addEventListener('click', function() { var inp = document.getElementById('bulk-qty-input'); inp.value = Math.max(1, (parseInt(inp.value) || 1) - 1); bulkSyncPresets(); });
document.getElementById('bulk-qty-plus').addEventListener('click', function() { var inp = document.getElementById('bulk-qty-input'); inp.value = Math.min(300, (parseInt(inp.value) || 1) + 1); bulkSyncPresets(); });
document.getElementById('bulk-qty-input').addEventListener('input', bulkSyncPresets);
document.querySelectorAll('.bulk-qty-preset').forEach(function(btn) { btn.addEventListener('click', function() { document.getElementById('bulk-qty-input').value = this.dataset.val; bulkSyncPresets(); }); });

// ===== デバイス削除 =====
function confirmDelete(deviceId) { document.getElementById('deleteDeviceId').textContent = deviceId; document.getElementById('deleteForm').action = '/partner/org/devices/' + deviceId + '/remove'; showModal('deleteModal'); }

// ===== 警告解除 =====
let clearAlertDeviceId = null;
function confirmClearAlert(deviceId, roomNumber, tenantName) {
    clearAlertDeviceId = deviceId;
    var label = (roomNumber ? roomNumber + ' ' : '') + (tenantName ? tenantName + ' ' : '') + '（' + deviceId + '）';
    document.getElementById('clearAlertTarget').innerHTML = '対象: <strong class="mono">' + escapeHtml(label) + '</strong>';
    showModal('clearAlertModal');
}
function confirmClearAlertFromDetail() { if (!currentDetailDeviceId) return; hideModal('detailModal'); confirmClearAlert(currentDetailDeviceId, document.getElementById('detailRoomInput').value, document.getElementById('detailTenantInput').value); }
function executeClearAlert() {
    if (!clearAlertDeviceId) return;
    fetch('/partner/org/devices/' + clearAlertDeviceId + '/clear-alert', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
    .then(r => r.json()).then(d => { if (d.success) { showToast(d.message, 'success'); hideModal('clearAlertModal'); setTimeout(() => location.reload(), 500); } else showToast(d.message || 'エラー', 'error'); })
    .catch(() => showToast('通信エラー', 'error'));
}

// ===== 外出モード（一覧テーブル） =====
let pendingToggleDevice = null, pendingToggleCheckbox = null;
function toggleAwayMode(deviceId, checked, checkbox) {
    if (checked) { pendingToggleDevice = deviceId; pendingToggleCheckbox = checkbox; checkbox.checked = false; showModal('watchOffModal'); return; }
    sendToggleAwayMode(deviceId, false);
}
function cancelAwayModeOn() { hideModal('watchOffModal'); pendingToggleDevice = null; pendingToggleCheckbox = null; }
function executeAwayModeOn() { if (pendingToggleDevice) { sendToggleAwayMode(pendingToggleDevice, true); if (pendingToggleCheckbox) pendingToggleCheckbox.checked = true; } hideModal('watchOffModal'); }
function sendToggleAwayMode(deviceId, awayMode) {
    fetch('/partner/org/devices/' + deviceId + '/toggle-watch', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({ away_mode: awayMode }) })
    .then(r => r.json()).then(d => { if (d.success) showToast(d.message, 'success'); else showToast('エラー', 'error'); })
    .catch(() => showToast('通信エラー', 'error'));
}

// ===== 外出モード（詳細モーダル） =====
let pendingDetailToggle = false, pendingDetailCheckbox = null;
function toggleAwayModeFromDetail(checked, checkbox) {
    if (checked) {
        pendingDetailToggle = true;
        pendingDetailCheckbox = checkbox;
        checkbox.checked = false;
        showModal('watchOffModal');
        return;
    }
    sendToggleAwayMode(currentDetailDeviceId, false);
    document.getElementById('detailAwayModeLabel').textContent = 'OFF';
}
function cancelAwayModeOn() {
    hideModal('watchOffModal');
    pendingToggleDevice = null;
    pendingToggleCheckbox = null;
    pendingDetailToggle = false;
    pendingDetailCheckbox = null;
}
function executeAwayModeOn() {
    if (pendingDetailToggle && currentDetailDeviceId) {
        sendToggleAwayMode(currentDetailDeviceId, true);
        if (pendingDetailCheckbox) pendingDetailCheckbox.checked = true;
        document.getElementById('detailAwayModeLabel').textContent = 'ON（外出中）';
        pendingDetailToggle = false;
        pendingDetailCheckbox = null;
    } else if (pendingToggleDevice) {
        sendToggleAwayMode(pendingToggleDevice, true);
        if (pendingToggleCheckbox) pendingToggleCheckbox.checked = true;
    }
    hideModal('watchOffModal');
}

// ===== デバイス詳細 =====
let currentDetailDeviceId = null;
let currentDetailSmsEnabled = false, currentDetailVoiceEnabled = false;
let currentDetailSmsPhone1 = '', currentDetailSmsPhone2 = '';
let currentDetailVoicePhone1 = '', currentDetailVoicePhone2 = '';

function showSubscriptionModal() {
    if (!currentDetailDeviceId) return;
    hideModal('detailModal');
    document.getElementById('subModalDeviceId').textContent = currentDetailDeviceId;
    document.getElementById('detailSmsPhone1').value = currentDetailSmsPhone1;
    document.getElementById('detailSmsPhone2').value = currentDetailSmsPhone2;
    document.getElementById('detailVoicePhone1').value = currentDetailVoicePhone1;
    document.getElementById('detailVoicePhone2').value = currentDetailVoicePhone2;
    renderSmsAction(currentDetailSmsEnabled);
    renderVoiceAction(currentDetailVoiceEnabled);
    showModal('subscriptionModal');
}
function showDeviceDetail(deviceId) {
    currentDetailDeviceId = deviceId;
    fetch('/partner/org/devices/' + deviceId + '/detail', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json()).then(data => {
        const badge = document.getElementById('detailStatusBadge');
        const labels = { normal: '正常稼働中', warning: '注意', alert: '未検知警告', offline: '通信途絶' };
        badge.textContent = labels[data.status] || data.status;
        badge.className = 'detail-status-badge ' + (data.status || 'offline');
        document.getElementById('detailClearAlertBtn').style.display = data.status === 'alert' ? 'inline-flex' : 'none';
        document.getElementById('detailDeviceId').textContent = data.device_id;
        document.getElementById('detailLastDetected').textContent = data.last_human_detected || '-';
        var rssiLabel = '-';
        if (data.rssi !== null && data.rssi !== undefined) rssiLabel = data.rssi > -70 ? '良好 (' + data.rssi + 'dBm)' : data.rssi > -85 ? '普通 (' + data.rssi + 'dBm)' : '弱い (' + data.rssi + 'dBm)';
        document.getElementById('detailBattery').textContent = data.battery_pct !== null && data.battery_pct !== undefined ? data.battery_pct + '%' : '-';
        document.getElementById('detailSignal').textContent = rssiLabel;
        // 外出モードトグル
        var awayMode = data.away_mode || false;
        document.getElementById('detailAwayModeToggle').checked = awayMode;
        var awayLabel = awayMode ? 'ON（外出中）' : 'OFF';
        if (data.away_until) awayLabel += '（〜' + data.away_until + '）';
        document.getElementById('detailAwayModeLabel').textContent = awayLabel;
        document.getElementById('detailRegistered').textContent = data.registered_at || '-';
        document.getElementById('detailRoomInput').value = data.room_number || '';
        document.getElementById('detailTenantInput').value = data.tenant_name || '';
        document.getElementById('detailAlertHoursInput').value = data.alert_threshold_hours || 24;
        document.getElementById('detailHeightInput').value = data.install_height_cm || 200;
        document.getElementById('detailPetExclusionInput').value = data.pet_exclusion_enabled ? '1' : '0';
        document.getElementById('detailMemoInput').value = data.memo || '';
        var notifyEnabled = data.notification_service_enabled !== false;
        document.getElementById('detailNotifyEnabled').checked = notifyEnabled;
        document.getElementById('detailNotifyLabel').textContent = notifyEnabled ? '有効' : '停止中';

        // SMS・AIコール状態をキャッシュ（契約プランモーダルで使用）
        currentDetailSmsEnabled = data.sms_enabled || false;
        currentDetailVoiceEnabled = data.voice_enabled || false;
        currentDetailSmsPhone1 = data.sms_phone_1 || '';
        currentDetailSmsPhone2 = data.sms_phone_2 || '';
        currentDetailVoicePhone1 = data.voice_phone_1 || '';
        currentDetailVoicePhone2 = data.voice_phone_2 || '';

        renderDetailSchedules(data.schedules || [], data.device_id);
        showModal('detailModal');
    }).catch(() => showToast('詳細の取得に失敗しました', 'error'));
}

// ===== SMS個別申込/解約 =====
function renderSmsAction(enabled) {
    var area = document.getElementById('detailSmsActionArea');
    var inputs = document.getElementById('detailSmsInputs');
    if (!area) return;
    if (enabled) {
        area.innerHTML = '<span style="font-size:12px;font-weight:600;color:#2e7d32;background:#e8f5e9;padding:3px 10px;border-radius:10px;margin-right:8px;">契約中 ✓</span>'
            + '<button style="font-size:11px;color:var(--gray-400);background:none;border:none;text-decoration:underline;cursor:pointer;font-family:inherit;padding:0;" onclick="showOptionCancel(\'sms\')">解約する</button>';
        inputs.style.display = '';
    } else {
        area.innerHTML = '<button class="btn btn-sm btn-primary" style="font-size:12px;padding:5px 14px;" onclick="showOptionConfirm(\'sms\')">申し込む</button>';
        inputs.style.display = 'none';
    }
}

async function toggleSmsOption(enabled) {
    if (!currentDetailDeviceId) return;
    var payload = { sms_enabled: enabled ? 1 : 0 };
    if (!enabled) { payload.sms_phone_1 = null; payload.sms_phone_2 = null; }
    try {
        var res = await fetch('/partner/org/devices/' + currentDetailDeviceId + '/notification', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        var data = await res.json();
        if (data.success) {
            currentDetailSmsEnabled = enabled;
            renderSmsAction(enabled);
            showToast(enabled ? 'SMS通知を申し込みました' : 'SMS通知を解約しました', 'success');
        } else { showToast(data.message || 'エラーが発生しました', 'error'); }
    } catch(e) { showToast('通信エラー', 'error'); }
}

// ===== AIコール個別申込/解約 =====
function renderVoiceAction(enabled) {
    var area = document.getElementById('detailVoiceActionArea');
    var inputs = document.getElementById('detailVoiceInputs');
    if (!area) return;
    if (enabled) {
        area.innerHTML = '<span style="font-size:12px;font-weight:600;color:#2e7d32;background:#e8f5e9;padding:3px 10px;border-radius:10px;margin-right:8px;">契約中 ✓</span>'
            + '<button style="font-size:11px;color:var(--gray-400);background:none;border:none;text-decoration:underline;cursor:pointer;font-family:inherit;padding:0;" onclick="showOptionCancel(\'voice\')">解約する</button>';
        inputs.style.display = '';
    } else {
        area.innerHTML = '<button class="btn btn-sm btn-primary" style="font-size:12px;padding:5px 14px;" onclick="showOptionConfirm(\'voice\')">申し込む</button>';
        inputs.style.display = 'none';
    }
}

async function toggleVoiceOption(enabled) {
    if (!currentDetailDeviceId) return;
    var payload = { voice_enabled: enabled ? 1 : 0 };
    if (!enabled) { payload.voice_phone_1 = null; payload.voice_phone_2 = null; }
    try {
        var res = await fetch('/partner/org/devices/' + currentDetailDeviceId + '/notification', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        var data = await res.json();
        if (data.success) {
            currentDetailVoiceEnabled = enabled;
            renderVoiceAction(enabled);
            showToast(enabled ? 'AIコールを申し込みました' : 'AIコールを解約しました', 'success');
        } else { showToast(data.message || 'エラーが発生しました', 'error'); }
    } catch(e) { showToast('通信エラー', 'error'); }
}

// ===== オプション申込み確認モーダル（カードチェック付き） =====
async function showOptionConfirm(type) {
    var isVoice = type === 'voice';
    // カード登録確認
    var cardDisplay = '確認中...';
    try {
        var res = await fetch('/partner/org/card-info', { headers: { 'Accept': 'application/json' } });
        var data = await res.json();
        if (!data.found) {
            showToast('クレジットカードが登録されていません。管理者にお問い合わせください。', 'error');
            return;
        }
        cardDisplay = data.brand + ' **** ' + data.last4;
    } catch(e) {
        showToast('カード情報の取得に失敗しました', 'error');
        return;
    }
    document.getElementById('optionConfirmTitle').textContent = isVoice ? '📞 AIコール申し込み確認' : '💬 SMS通知申し込み確認';
    document.getElementById('optionConfirmBody').textContent = isVoice
        ? 'AIコール（電話通知）を申し込みます。月額 +¥300/台 が翌月より加算されます。'
        : 'SMS通知を申し込みます。月額 +¥100/台 が翌月より加算されます。';
    document.getElementById('optionConfirmCard').textContent = cardDisplay;
    var btn = document.getElementById('optionConfirmBtn');
    btn.onclick = function() {
        hideModal('optionConfirmModal');
        if (isVoice) toggleVoiceOption(true);
        else toggleSmsOption(true);
    };
    showModal('optionConfirmModal');
}

// ===== オプション解約確認モーダル =====
function showOptionCancel(type) {
    var isVoice = type === 'voice';
    document.getElementById('optionCancelTitle').textContent = isVoice ? '📞 AIコール解約確認' : '💬 SMS通知解約確認';
    document.getElementById('optionCancelBody').textContent = isVoice
        ? 'AIコール（電話通知）を解約します。翌月1日より月額から除外されます。'
        : 'SMS通知を解約します。翌月1日より月額から除外されます。';
    var btn = document.getElementById('optionCancelBtn');
    btn.onclick = function() {
        hideModal('optionCancelModal');
        if (isVoice) toggleVoiceOption(false);
        else toggleSmsOption(false);
    };
    showModal('optionCancelModal');
}

async function saveDetailChanges() {
    if (!currentDetailDeviceId) return;
    var payload = { room_number: document.getElementById('detailRoomInput').value || null, tenant_name: document.getElementById('detailTenantInput').value || null, memo: document.getElementById('detailMemoInput').value || null, alert_threshold_hours: parseInt(document.getElementById('detailAlertHoursInput').value) || 24, install_height_cm: parseInt(document.getElementById('detailHeightInput').value) || 200, pet_exclusion_enabled: document.getElementById('detailPetExclusionInput').value === '1' ? 1 : 0 };
    try {
        var res = await fetch('/partner/org/devices/' + currentDetailDeviceId + '/assignment', { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(payload) });
        var data = await res.json();
        if (res.ok && data.success) { showToast(data.message || '保存しました', 'success'); setTimeout(() => location.reload(), 800); }
        else showToast(data.message || '保存に失敗しました', 'error');
    } catch(e) { console.error(e); showToast('通信エラーが発生しました', 'error'); }
}

function toggleNotifyService(enabled) { document.getElementById('detailNotifyLabel').textContent = enabled ? '有効' : '停止中'; showToast(enabled ? '通知サービスを有効にしました' : '通知サービスを停止しました', 'success'); }

function saveDetailNotification() {
    if (!currentDetailDeviceId) return;
    var payload = {
        sms_phone_1: document.getElementById('detailSmsPhone1').value || null,
        sms_phone_2: document.getElementById('detailSmsPhone2').value || null,
        voice_phone_1: document.getElementById('detailVoicePhone1').value || null,
        voice_phone_2: document.getElementById('detailVoicePhone2').value || null,
    };
    fetch('/partner/org/devices/' + currentDetailDeviceId + '/notification', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(payload)
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast('通知設定を保存しました', 'success'); hideModal('subscriptionModal'); }
    })
    .catch(() => showToast('保存に失敗しました', 'error'));
}

function showCancelFlow() { showModal('cancelFlowModal'); }

function renderDetailSchedules(schedules, deviceId) {
    var c = document.getElementById('detailScheduleList');
    if (!schedules || !schedules.length) { c.innerHTML = '<div class="detail-schedule-empty">外出スケジュールなし</div>'; return; }
    var html = '<div class="detail-schedule-list">';
    schedules.forEach(s => {
        html += '<div class="detail-schedule-item">';
        if (s.type === 'oneshot') { html += '<div class="detail-schedule-icon oneshot">📅</div><div class="detail-schedule-info"><p class="detail-schedule-main">' + formatTimerDateTime(s.start_at) + ' 〜 ' + (s.end_at ? formatTimerDateTime(s.end_at) : '手動復帰') + '</p><p class="detail-schedule-sub">' + (s.memo ? escapeHtml(s.memo) : '単発') + '</p></div>'; }
        else { html += '<div class="detail-schedule-icon recurring">🔁</div><div class="detail-schedule-info"><p class="detail-schedule-main">毎週 ' + escapeHtml(s.days_label) + ' ' + s.start_time + '〜' + (s.next_day ? '翌' : '') + s.end_time + '</p><p class="detail-schedule-sub">' + (s.memo ? escapeHtml(s.memo) : '定期') + '</p></div>'; }
        html += '<button class="detail-schedule-del" onclick="confirmDeleteSchedule(\'' + escapeHtml(deviceId) + '\',' + s.id + ',\'' + escapeHtml(s.type === 'oneshot' ? formatTimerDateTime(s.start_at) : s.days_label) + '\')">×</button></div>';
    });
    c.innerHTML = html + '</div>';
}

let scheduleAddOrigin = null;
function openScheduleAddFromDetail() { scheduleAddOrigin = 'detail'; openScheduleAddModal(currentDetailDeviceId, document.getElementById('detailRoomInput').value, document.getElementById('detailTenantInput').value); }
function showTimerListModal() { showModal('timerListModal'); loadTimerList(); }

async function loadTimerList() {
    const body = document.getElementById('timerListBody');
    body.innerHTML = '<div class="timer-list-loading">読み込み中...</div>';
    try {
        const res = await fetch('{{ route("partner.org.timers") }}', { headers: { 'Accept': 'application/json' } });
        if (!res.ok) { body.innerHTML = '<div class="timer-list-empty">データの取得に失敗しました</div>'; return; }
        const data = await res.json();
        if (!data.length) { body.innerHTML = '<div class="timer-list-empty">外出スケジュールが設定されているデバイスはありません</div>'; return; }
        let awayCount = 0, oneshotCount = 0, recurringCount = 0;
        data.forEach(d => { if (d.away_mode) awayCount++; d.schedules.forEach(s => { if (s.type === 'oneshot') oneshotCount++; else recurringCount++; }); });
        let html = '<div class="timer-summary"><div class="timer-summary-item"><div class="timer-summary-value">' + data.length + '</div><div class="timer-summary-label">対象デバイス</div></div><div class="timer-summary-item"><div class="timer-summary-value">' + awayCount + '</div><div class="timer-summary-label">外出モード中</div></div><div class="timer-summary-item"><div class="timer-summary-value">' + oneshotCount + '</div><div class="timer-summary-label">単発予定</div></div><div class="timer-summary-item"><div class="timer-summary-value">' + recurringCount + '</div><div class="timer-summary-label">定期スケジュール</div></div></div>';
        data.forEach(d => {
            html += '<div class="timer-device-group"><div class="timer-device-header"><div class="timer-device-info">';
            if (d.room_number) html += '<span class="timer-device-room">' + escapeHtml(d.room_number) + '</span>';
            if (d.tenant_name) html += '<span class="timer-device-name">' + escapeHtml(d.tenant_name) + '</span>';
            html += '<span class="timer-device-id">' + escapeHtml(d.device_id) + '</span></div>';
            if (d.away_mode) { html += '<span class="timer-away-badge">⏸ 見守りOFF'; if (d.away_until) html += '（〜' + formatTimerDateTime(d.away_until) + '）'; html += '</span>'; }
            html += '</div>';
            if (d.schedules.length) {
                d.schedules.forEach(s => {
                    html += '<div class="timer-schedule-item">';
                    if (s.type === 'oneshot') { html += '<div class="timer-schedule-icon oneshot">📅</div><div class="timer-schedule-info"><p class="timer-schedule-main">' + formatTimerDateTime(s.start_at) + ' 〜 ' + (s.end_at ? formatTimerDateTime(s.end_at) : '手動復帰') + '</p><p class="timer-schedule-sub">' + (s.memo ? escapeHtml(s.memo) : '（メモなし）') + '</p></div><span class="timer-schedule-type oneshot">単発</span>'; }
                    else { html += '<div class="timer-schedule-icon recurring">🔁</div><div class="timer-schedule-info"><p class="timer-schedule-main">毎週 ' + escapeHtml(s.days_label) + ' ' + s.start_time + '〜' + (s.next_day ? '翌' : '') + s.end_time + '</p><p class="timer-schedule-sub">' + (s.memo ? escapeHtml(s.memo) : '（メモなし）') + '</p></div><span class="timer-schedule-type recurring">定期</span>'; }
                    html += '<button class="timer-delete-btn" onclick="confirmDeleteSchedule(\'' + escapeHtml(d.device_id) + '\',' + s.id + ',\'' + escapeHtml(s.type === 'oneshot' ? formatTimerDateTime(s.start_at) : s.days_label) + '\')">×</button></div>';
                });
            } else if (d.away_mode) { html += '<div class="timer-schedule-item"><div class="timer-schedule-icon oneshot">🚶</div><div class="timer-schedule-info"><p class="timer-schedule-main">手動で外出モード中</p><p class="timer-schedule-sub">外出スケジュール設定なし</p></div></div>'; }
            html += '<button class="timer-add-btn" onclick="scheduleAddOrigin=\'timerlist\';openScheduleAddModal(\'' + escapeHtml(d.device_id) + '\',\'' + escapeHtml(d.room_number || '') + '\',\'' + escapeHtml(d.tenant_name || '') + '\')">＋ 外出スケジュール追加</button></div>';
        });
        body.innerHTML = html;
    } catch (e) { console.error(e); body.innerHTML = '<div class="timer-list-empty">通信エラーが発生しました</div>'; }
}

function formatTimerDateTime(dtStr) {
    if (!dtStr) return '-';
    var p = dtStr.split(' ');
    if (p.length === 2) { var d = p[0].split('-'); if (d.length === 3) return parseInt(d[1]) + '/' + parseInt(d[2]) + ' ' + p[1]; }
    return dtStr;
}

let scheduleTargetDeviceId = null, scheduleType = 'oneshot';
function openScheduleAddModal(deviceId, roomNumber, tenantName) {
    scheduleTargetDeviceId = deviceId; scheduleType = 'oneshot';
    var label = (roomNumber ? roomNumber : '') + (tenantName ? (roomNumber ? ' ' : '') + tenantName : '');
    label = (label ? label + '（' : '') + deviceId + (label ? '）' : '');
    document.getElementById('scheduleDeviceLabel').innerHTML = '対象: <strong>' + escapeHtml(label) + '</strong>';
    ['schedStartAt','schedEndAt','schedStartTime','schedEndTime','schedMemo'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('schedNextDay').checked = false;
    document.querySelectorAll('.schedule-day-btn').forEach(b => b.classList.remove('active'));
    switchScheduleType('oneshot'); showModal('scheduleAddModal');
}
function hideScheduleAddModal() { hideModal('scheduleAddModal'); }
function switchScheduleType(type) {
    scheduleType = type;
    document.getElementById('tabOneshot').classList.toggle('active', type === 'oneshot');
    document.getElementById('tabRecurring').classList.toggle('active', type === 'recurring');
    document.getElementById('oneshotForm').style.display = type === 'oneshot' ? 'block' : 'none';
    document.getElementById('recurringForm').style.display = type === 'recurring' ? 'block' : 'none';
}
function toggleDay(btn) { btn.classList.toggle('active'); }

async function submitSchedule() {
    if (!scheduleTargetDeviceId) return;
    var payload = { type: scheduleType, memo: document.getElementById('schedMemo').value || null };
    if (scheduleType === 'oneshot') {
        var startAt = document.getElementById('schedStartAt').value;
        if (!startAt) { showToast('開始日時を入力してください', 'error'); return; }
        payload.start_at = startAt; var endAt = document.getElementById('schedEndAt').value; if (endAt) payload.end_at = endAt;
    } else {
        var days = []; document.querySelectorAll('.schedule-day-btn.active').forEach(b => days.push(parseInt(b.dataset.day)));
        if (!days.length) { showToast('曜日を1つ以上選択してください', 'error'); return; }
        var st = document.getElementById('schedStartTime').value, et = document.getElementById('schedEndTime').value;
        if (!st || !et) { showToast('開始時間と終了時間を入力してください', 'error'); return; }
        payload.days_of_week = days; payload.start_time = st; payload.end_time = et; payload.next_day = document.getElementById('schedNextDay').checked;
    }
    try {
        var res = await fetch('/partner/org/devices/' + scheduleTargetDeviceId + '/schedules', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(payload) });
        var data = await res.json();
        if (res.ok && data.success) { showToast('外出スケジュールを追加しました', 'success'); hideScheduleAddModal(); if (scheduleAddOrigin === 'detail' && currentDetailDeviceId) showDeviceDetail(currentDetailDeviceId); loadTimerList(); }
        else showToast(data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : '追加に失敗しました'), 'error');
    } catch (e) { console.error(e); showToast('通信エラーが発生しました', 'error'); }
}

let deleteScheduleDeviceId = null, deleteScheduleId = null;
function confirmDeleteSchedule(deviceId, scheduleId, detail) { deleteScheduleDeviceId = deviceId; deleteScheduleId = scheduleId; document.getElementById('scheduleDeleteDetail').textContent = deviceId + ' のスケジュール: ' + detail; showModal('scheduleDeleteModal'); }

async function executeDeleteSchedule() {
    if (!deleteScheduleDeviceId || !deleteScheduleId) return;
    try {
        var res = await fetch('/partner/org/devices/' + deleteScheduleDeviceId + '/schedules/' + deleteScheduleId, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
        var data = await res.json();
        if (res.ok && data.success) { showToast('外出スケジュールを削除しました', 'success'); hideModal('scheduleDeleteModal'); if (currentDetailDeviceId && document.getElementById('detailModal').classList.contains('show')) showDeviceDetail(currentDetailDeviceId); loadTimerList(); }
        else showToast(data.message || '削除に失敗しました', 'error');
    } catch (e) { console.error(e); showToast('通信エラーが発生しました', 'error'); }
}

// ===== 通知設定モーダル =====
function showNotificationModal() {
    fetch('{{ route("partner.org.notification") }}', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json()).then(d => {
        document.getElementById('orgNotifEmail1').value = d.notification_email_1 || '';
        document.getElementById('orgNotifEmail2').value = d.notification_email_2 || '';
        document.getElementById('orgNotifEmail3').value = d.notification_email_3 || '';
        document.getElementById('orgNotifEnabled').checked = d.notification_enabled;
        document.getElementById('orgNotifSms1').value = d.notification_sms_1 ? d.notification_sms_1.replace(/^\+81/, '0') : '';
        document.getElementById('orgNotifSms2').value = d.notification_sms_2 ? d.notification_sms_2.replace(/^\+81/, '0') : '';
        document.getElementById('orgNotifSmsEnabled').checked = d.notification_sms_enabled;
        showModal('notificationModal');
    })
    .catch(() => showModal('notificationModal'));
}

function saveOrgNotification() {
    var payload = {
        notification_email_1: document.getElementById('orgNotifEmail1').value || null,
        notification_email_2: document.getElementById('orgNotifEmail2').value || null,
        notification_email_3: document.getElementById('orgNotifEmail3').value || null,
        notification_enabled: document.getElementById('orgNotifEnabled').checked ? 1 : 0,
        notification_sms_1: document.getElementById('orgNotifSms1').value || null,
        notification_sms_2: document.getElementById('orgNotifSms2').value || null,
        notification_sms_enabled: document.getElementById('orgNotifSmsEnabled').checked ? 1 : 0,
    };
    fetch('{{ route("partner.org.notification.update") }}', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(payload)
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast(d.message, 'success'); hideModal('notificationModal'); }
        else showToast(d.message || '保存に失敗しました', 'error');
    }).catch(() => showToast('通信エラーが発生しました', 'error'));
}

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success')) showToast('{{ session("success") }}', 'success'); @endif
    @if(session('error')) showToast('{{ session("error") }}', 'error'); @endif
});
</script>
@endsection
