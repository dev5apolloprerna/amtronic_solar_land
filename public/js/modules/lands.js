/* ==========================================================================
   Land Master inline form (new parcel / edit land details)
   - choosing a park fills State / District / City and limits the SPV list
   - live land reference number preview (SPV-STATE-PARK|NOP-PARCEL-YEAR)
   - 7/12 acres from sq.m, increment hint
   ========================================================================== */
(function ($) {
  'use strict';
  const App = window.App, num = App.num;
  const $f = $('#landForm');
  if (!$f.length) return;
  const field = (n) => $f.find('[name="' + n + '"]');
  const inr = (n) => '₹' + Math.round(Number(n || 0)).toLocaleString('en-IN');

  // park → location + SPVs of that park
  field('park_id').on('change', function () {
    const park = $(this).val();
    $.getJSON($f.data('spv-url') + '?park_id=' + encodeURIComponent(park || '')).done(function (rows) {
      const $s = field('spv_id'), cur = $s.val();
      $s.html('<option value="">Select…</option>');
      rows.forEach((r) => $s.append($('<option>').val(r.id).text(r.label)));
      if (rows.some((r) => String(r.id) === String(cur))) $s.val(cur); else if (park && rows.length === 1) $s.val(rows[0].id);
      preview();
    });
    if (!park) return;
    $.getJSON($f.data('park-url') + '/' + encodeURIComponent(park)).done(function (p) {
      field('district_id').data('keep', p.district_id);
      field('city_id').data('keep', p.city_id);
      field('state_id').val(String(p.state_id)).trigger('change');
    });
  });

  // land reference preview
  let t = null;
  function preview() {
    clearTimeout(t);
    t = setTimeout(function () {
      if (!field('parcel_code').length) return; // edit mode: reference is fixed
      const q = { spv_id: field('spv_id').val(), state_id: field('state_id').val(), park_id: field('park_id').val(), parcel_code: field('parcel_code').val(), ref_year: field('ref_year').val() };
      $.getJSON($f.data('ref-url'), q).done(function (r) {
        $('#landRef').html(r.ref
          ? '<span class="ref">' + App.esc(r.ref) + '</span>' + (r.exists ? ' <span class="chip bad">already exists</span>' : '')
          : '<span class="hint">Fills once SPV, state, parcel and year are set</span>');
      });
    }, 200);
  }
  $f.on('change input', '[name="spv_id"],[name="state_id"],[name="parcel_code"],[name="ref_year"]', preview);
  field('parcel_code').on('input', function () { const p = this.selectionStart; this.value = this.value.toUpperCase(); this.setSelectionRange(p, p); });

  // acres and increment hint
  function calc() {
    const sqm = field('area_sqm_712').val();
    $('#f_area_acres_712').text(sqm === '' || sqm === undefined ? '—' : (Math.round(num(sqm) / 4046.8564 * 100) / 100).toLocaleString('en-IN', { maximumFractionDigits: 2 }));
    const rate = num(field('rate_per_acre').val()), yrs = num(field('increment_after_yrs').val()), pct = num(field('increment_pct').val());
    const $h = field('increment_pct').closest('.fld').find('small.hint');
    const txt = rate && yrs ? 'From year ' + (yrs + 1) + ': ' + inr(rate * (1 + pct / 100)) + ' per acre' : '';
    if ($h.length) $h.text(txt); else if (txt) field('increment_pct').after('<small class="hint">' + txt + '</small>');
  }
  $f.on('input', '[name="area_sqm_712"],[name="rate_per_acre"],[name="increment_after_yrs"],[name="increment_pct"]', calc);
  preview();
})(jQuery);
