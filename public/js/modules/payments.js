/* Payment drawer: pick instalment → landowner, show balance, suggest TDS, compute net. */
(function ($) {
  'use strict';
  $(document).on('drawer:loaded', function (e, module, $body) {
    if (module !== 'payment') return;
    const $f = $body.find('form'), $meta = $body.find('[data-payment-form]');
    let allocs = {}, tdsTouched = !!$f.find('[name=tds_deducted]').val() && $f.find('[name=tds_deducted]').val() !== '0';
    let ratio = Number($('#allocInfo [data-tds-ratio]').data('tds-ratio') || 0);

    function info(a) {
      if (!a) { $('#allocInfo').html('<div class="note-b">Choose a landowner to see their share, paid and remaining balance.</div>'); return; }
      ratio = a.tds_ratio;
      $('#allocInfo').html('<div class="note-b"><b>' + App.esc(a.party) + '</b> · ' + App.fmt(a.share, 4) + '% · Share ' + App.inr(a.gross) + ' (TDS ' + App.inr(a.tds) + ')' +
        ' · Already paid ' + App.inr(a.paid) + ' · <b>Remaining ' + App.inr(a.remaining) + '</b>' + (a.bank ? '<br>Bank ' + App.esc(a.bank) : '') + '</div>');
      if (!$f.find('[name=gross_amount]').val()) { $f.find('[name=gross_amount]').val(a.remaining.toFixed(2)); suggestTds(); }
    }

    function load(scheduleId, keep) {
      $.getJSON(String($meta.data('alloc-url')).replace('__ID__', scheduleId)).done(function (rows) {
        allocs = {};
        const $sel = $f.find('[name=rent_allocation_id]');
        $sel.html('<option value="">Select…</option>');
        rows.forEach((a) => { allocs[a.id] = a; $sel.append($('<option>').val(a.id).text(a.label).prop('disabled', a.remaining <= 0.009)); });
        if (keep) $sel.val(String(keep));
        info(allocs[$sel.val()]);
      });
    }

    function suggestTds() {
      if (tdsTouched) return net();
      $f.find('[name=tds_deducted]').val((App.num($f.find('[name=gross_amount]').val()) * ratio).toFixed(2));
      net();
    }
    function net() { $('#netPaid').text(App.inr(App.num($f.find('[name=gross_amount]').val()) - App.num($f.find('[name=tds_deducted]').val()))); }

    $f.on('change', '[name=schedule_pick]', function () { if ($(this).val()) load($(this).val()); });
    $f.on('change', '[name=rent_allocation_id]', function () { $f.find('[name=gross_amount]').val(''); info(allocs[$(this).val()]); });
    $f.on('input', '[name=gross_amount]', suggestTds);
    $f.on('input', '[name=tds_deducted]', function () { tdsTouched = true; net(); });

    // Instalment preset (from schedule list): load its owners for live balance info.
    const $alloc = $f.find('select[name=rent_allocation_id]');
    const sid = $meta.data('schedule-id');
    if ($alloc.length && sid) load(sid, $alloc.val());
    net();
  });
})(jQuery);
