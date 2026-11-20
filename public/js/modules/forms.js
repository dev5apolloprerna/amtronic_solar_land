/* ==========================================================================
   Drawer forms inside a land parcel: live calculations shown before saving
   (the server repeats every rule on save).
   survey: sq.m → acres · farmer: Aadhaar masking · lease: end date, year-1 rent,
   TDS field · schedule: TDS / net · payment: farmer account, net paid ·
   sublease: area + MW capacity panel · document: linked-record list ·
   generic data-show-if toggles.
   ========================================================================== */
(function ($) {
  'use strict';
  const App = window.App, num = App.num, esc = App.esc;
  const SQM = 4046.8564;
  const FREQ = { Annual: 12, 'Half-yearly': 6, Quarterly: 3, Monthly: 1 };
  const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const pad = (n) => String(n).padStart(2, '0');
  const fmtD = (s) => { if (!s) return '—'; const [y, m, d] = s.split('-'); return d + ' ' + MONTHS[m - 1] + ' ' + y; };
  const inr = (n) => '₹' + Math.round(Number(n || 0)).toLocaleString('en-IN');
  const fmtA = (n) => Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 });
  const fmtM = (n) => Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 3 });
  /** same as Carbon addMonthsNoOverflow(m)->subDay() */
  function endDate(start, months) {
    const [y, mo, d] = start.split('-').map(Number);
    const tY = y + Math.floor((mo - 1 + months) / 12), tM = (mo - 1 + months) % 12;
    const last = new Date(tY, tM + 1, 0).getDate();
    const dt = new Date(tY, tM, Math.min(d, last));
    dt.setDate(dt.getDate() - 1);
    return dt.getFullYear() + '-' + pad(dt.getMonth() + 1) + '-' + pad(dt.getDate());
  }
  const val = ($s, n) => $s.find('[name="' + n + '"]').val();
  /** replace the text of a read-only field, keeping its LOCKED badge */
  const setRo = ($s, id, html) => { const $el = $s.find('#' + id), $lock = $el.find('.lock').detach(); $el.html(html).append($lock); };

  /* ---- data-show-if="field=value" ---- */
  function toggles($scope) {
    $scope.find('[data-show-if]').each(function () {
      const [k, v] = $(this).data('show-if').split('=');
      $(this).prop('hidden', val($scope, k) !== v);
    });
  }
  $(document).on('change', '#drBody select, #drBody input', function () { toggles($('#drBody')); });

  /* ---- survey: acres from sq.m ---- */
  $(document).on('input', '#drBody [data-sqm-to]', function () {
    const v = $(this).val();
    setRo($('#drBody'), $(this).data('sqm-to').slice(1), v === '' ? '—' : fmtA(Math.round(num(v) / SQM * 100) / 100));
  });

  /* ---- farmer: mask a 12-digit Aadhaar as soon as it is typed ---- */
  $(document).on('blur', '#drBody [name="aadhaar"]', function () {
    const d = String($(this).val()).replace(/\D/g, '');
    if (d.length === 12) $(this).val('XXXX-XXXX-' + d.slice(-4));
  });

  /* ---- lease ---- */
  function lease($f) {
    const start = val($f, 'lease_start_date'), months = num(val($f, 'lease_duration_mths'));
    setRo($f, 'f_lease_end_date', start && months ? fmtD(endDate(start, months)) : '—');
    $f.find('[name="lease_duration_mths"]').closest('.fld').find('small.hint').text(months ? fmtA(months / 12) + ' years' : '');
    const fm = FREQ[val($f, 'rent_frequency')] || 12;
    setRo($f, 'f_rent_amount', inr(num(val($f, 'leased_area_acres')) * num(val($f, 'rent_rate_per_acre')) * fm / 12));
    const $tds = $f.find('[name="tds_rate"]');
    if (val($f, 'tds_applicable') === 'Yes' && !num($tds.val())) $tds.val(10);
  }
  $(document).on('input change', '#drBody [data-lease-form] input, #drBody [data-lease-form] select', function () { lease($(this).closest('[data-lease-form]')); });

  /* ---- schedule: TDS & net from rent ---- */
  $(document).on('input', '#drBody [data-schedule-form] [name="rent_amount"]', function () {
    const $f = $(this).closest('[data-schedule-form]'), rent = num($(this).val()), tds = Math.round(rent * num($f.data('tds')) / 100);
    setRo($f, 'f_tds_amount', inr(tds)); setRo($f, 'f_net_payable', inr(rent - tds));
  });

  /* ---- payment ---- */
  function payment($f, prefill) {
    const acc = $f.data('accounts') || {}, fid = val($f, 'farmer_id'), a = acc[fid];
    let total = 0; Object.values(acc).forEach((x) => { total += num(x.pct); });
    const rem = a ? Math.max(0, a.share - a.paid) : 0;
    $f.find('[data-account]').html('<b>Farmer account:</b> ' + (a
      ? esc(a.name) + ' · ' + fmtA(a.pct) + '% ownership · Share ' + inr(a.share) + ' · Already paid ' + inr(a.paid) + ' · <b>Remaining ' + inr(rem) + '</b>'
      : 'Choose an owner to see their share, paid and remaining balance.') + '<br>Total ownership: ' + fmtA(total) + '%.');
    if (prefill && a) { $f.find('[name="gross_amount"]').val(rem || ''); $f.find('[name="tds_deducted"]').val(Math.min(a.tds, rem) || 0); }
    setRo($f, 'f_net_paid', inr(num(val($f, 'gross_amount')) - num(val($f, 'tds_deducted'))));
  }
  $(document).on('change', '#drBody [data-payment-form] [name="schedule_id"]', function () {
    const url = $(this).closest('[data-payment-form]').data('reload');
    if (url) App.openDrawer(url + '?schedule_id=' + encodeURIComponent($(this).val()));
  });
  $(document).on('change', '#drBody [data-payment-form] [name="farmer_id"]', function () { payment($(this).closest('[data-payment-form]'), true); });
  $(document).on('input', '#drBody [data-payment-form] [name="gross_amount"], #drBody [data-payment-form] [name="tds_deducted"]', function () { payment($(this).closest('[data-payment-form]'), false); });

  /* ---- sublease: area + MW panel ---- */
  function bar(used, cur, total, over) {
    const pct = (x) => total ? Math.max(0, Math.min(100, x / total * 100)) : 0;
    return '<div class="bar"><span class="seg used" style="width:' + pct(used) + '%"></span><span class="seg cur ' + (over ? 'over' : '') + '" style="width:' + pct(Math.min(cur, Math.max(0, total - used))) + '%"></span></div>';
  }
  function sublease($f) {
    const info = ($f.data('leases') || {})[$f.find('[data-lease-id]').val()], $p = $f.find('[data-cap-panel]');
    if (!info) { $p.html('<div class="note-b">Pick a lease. This panel shows how much of its area and capacity is still free to sublease.</div>'); return; }
    const st = val($f, 'status');
    const area = info.area, usedA = area - info.areaFree, curA = st === 'Terminated' ? 0 : num(val($f, 'sublease_area_acres'));
    const freeA = Math.max(0, info.areaFree), overA = curA - freeA;
    const mw = info.mw, usedM = Math.max(0, mw - info.mwFree), curM = st === 'Active' ? num(val($f, 'capacity_mw')) : 0;
    const freeM = Math.max(0, info.mwFree), overM = curM - freeM;
    let h = '<div class="cap"><div class="cap-head"><b>Area available on ' + esc(info.code) + '</b><span class="mono">' + fmtA(area) + ' ac leased</span></div>' + bar(usedA, curA, area, overA > 1e-9) +
      '<div class="cap-leg"><span><i style="background:var(--muted)"></i>Already subleased ' + fmtA(usedA) + ' ac</span><span><i style="background:' + (overA > 1e-9 ? 'var(--bad)' : 'var(--accent)') + '"></i>This sublease ' + fmtA(curA) + ' ac</span><span><i style="background:var(--sunk);border:1px solid var(--line)"></i>Free after save ' + fmtA(Math.max(0, freeA - curA)) + ' ac</span></div>' +
      (overA > 1e-9 ? '<p class="cap-err">That is ' + fmtA(overA) + ' acres more than is free. Enter ' + fmtA(freeA) + ' acres or less.</p>' : '') +
      '<div class="cap-head" style="margin-top:6px"><b>Capacity available on ' + esc(info.code) + '</b><span class="mono">' + fmtM(mw) + ' MW leased</span></div>' + bar(usedM, curM, mw, overM > 5e-4) +
      '<div class="cap-leg"><span><i style="background:var(--muted)"></i>Already subleased (peak) ' + fmtM(usedM) + ' MW</span><span><i style="background:' + (overM > 5e-4 ? 'var(--bad)' : 'var(--accent)') + '"></i>This sublease ' + fmtM(curM) + ' MW</span><span><i style="background:var(--sunk);border:1px solid var(--line)"></i>Remaining after save ' + fmtM(Math.max(0, freeM - curM)) + ' MW</span></div>' +
      (overM > 5e-4 ? '<p class="cap-err">That is ' + fmtM(overM) + ' MW more than is free over the lease term. Enter ' + fmtM(freeM) + ' MW or less (the server checks the exact dates on save).</p>' : '') +
      '<span class="hint">Lease term ' + fmtD(info.start) + ' to ' + fmtD(info.end) + '. Only Active subleases use MW; Terminated ones free their acres.</span></div>';
    $p.html(h);
  }
  $(document).on('input change', '#drBody [data-sublease-form] input, #drBody [data-sublease-form] select', function () {
    const $f = $(this).closest('[data-sublease-form]');
    if ($(this).is('[data-lease-id]')) {
      const info = ($f.data('leases') || {})[$(this).val()];
      if (info) { $f.find('[name="sublease_start"]').val(info.start); $f.find('[name="sublease_end"]').val(info.end); }
    }
    sublease($f);
  });

  /* ---- document: linked record list follows "Linked to" ---- */
  $(document).on('change', '#drBody [data-doc-form] [name="linked_entity_type"]', function () {
    const $f = $(this).closest('[data-doc-form]'), $t = $f.find('[name="linked_entity_id"]');
    $.getJSON($f.data('entities-url') + encodeURIComponent($(this).val())).done(function (rows) {
      $t.html('<option value="">Select…</option>');
      rows.forEach((r) => $t.append($('<option>').val(r.id).text(r.label)));
      if (rows.length === 1) $t.val(rows[0].id);
    });
  });

  /* ---- run once when a drawer opens ---- */
  $(document).on('drawer:loaded', function (e, module, $body) {
    toggles($body);
    if (module === 'lease') lease($body.find('[data-lease-form]'));
    if (module === 'payment') payment($body.find('[data-payment-form]'), false);
    if (module === 'sublease') sublease($body.find('[data-sublease-form]'));
  });
})(jQuery);
