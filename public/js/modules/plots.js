/* Plot / survey number form: park pre-fills location and SPVs, live Land Reference Number preview, acres hint. */
(function ($) {
  'use strict';
  const $form = $('#plotForm');
  if (!$form.length) return;

  const fill = ($sel, rows, val) => {
    $sel.html('<option value="">Select…</option>');
    rows.forEach((r) => $sel.append($('<option>').val(r.id).text(r.label)));
    if (val) $sel.val(String(val));
  };

  function preview() {
    const url = $form.data('ref-url');
    const $box = $('#landRefPreview');
    if (!url || !$form.find('[name=park_id]').length) return;
    const q = {
      park_id: $form.find('[name=park_id]').val(), state_id: $form.find('[name=state_id]').val(),
      city_id: $form.find('[name=city_id]').val(), taluka_id: $form.find('[name=taluka_id]').val(),
    };
    if (!q.park_id || !q.state_id || !q.city_id || !q.taluka_id) {
      $box.html('<span class="hint">Fills once park, state, city and taluka are set</span>');
      return;
    }
    $.getJSON(url, q).done((r) => $box.html(r.ref ? '<span class="ref">' + App.esc(r.ref) + '</span> <span class="hint">next available · confirmed on save</span>' : '—'));
  }

  // Choosing a park fills its SPVs and its state / city / taluka (still editable).
  $form.on('change', '[data-plot-park]', function () {
    const parkId = $(this).val();
    const $spv = $form.find('[name=spv_id]');
    if (!parkId) { fill($spv, []); preview(); return; }
    $.getJSON($form.data('spv-url'), { park_id: parkId }).done(function (r) {
      fill($spv, r.spvs, r.spvs.length === 1 ? r.spvs[0].id : null);
      if (r.park) {
        $form.find('[name=state_id]').val(String(r.park.state_id));
        fill($form.find('[name=city_id]'), r.cities, r.park.city_id);
        fill($form.find('[name=taluka_id]'), r.talukas, r.park.taluka_id);
      }
      preview();
    });
  });

  $form.on('change', '[name=state_id],[name=city_id],[name=taluka_id]', function () { setTimeout(preview, 250); });

  const ACRES = { Acre: 1, Hectare: 2.4710538, 'Sq.m': 1 / 4046.8564 };
  function acres() {
    const a = App.num($form.find('[name=total_area]').val()), u = $form.find('[name=land_unit]').val();
    $('#acresHint').text(a ? '= ' + App.fmt(a * (ACRES[u] || 1), 4) + ' acres. Area under lease cannot exceed this.' : '');
  }
  $form.on('input change', '[name=total_area],[name=land_unit]', acres);
  acres();
  preview();
})(jQuery);
