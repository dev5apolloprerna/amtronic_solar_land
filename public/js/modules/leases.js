/* Lease drawer: defaults from the survey number, end date, annual rent and first instalment preview, park capacity. */
(function ($) {
  'use strict';
  const FREQ = { Annual: 12, 'Half-yearly': 6, Quarterly: 3, Monthly: 1 };
  const ACRES = { Acre: 1, Hectare: 2.4710538, 'Sq.m': 1 / 4046.8564 };
  const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

  function endDate(start, months) {
    const [y, m, d] = start.split('-').map(Number);
    const last = new Date(y, m - 1 + months + 1, 0).getDate(); // no overflow, like Carbon addMonthsNoOverflow
    const dt = new Date(y, m - 1 + months, Math.min(d, last));
    dt.setDate(dt.getDate() - 1);
    return dt;
  }

  $(document).on('drawer:loaded', function (e, module, $body) {
    if (module !== 'lease') return;
    const $f = $body.find('form'), $meta = $body.find('[data-lease-form]');
    const v = (n) => $f.find('[name=' + n + ']').val();

    function recalc() {
      const start = v('lease_start_date'), months = App.num(v('lease_duration_months'));
      if (start && months) {
        const d = endDate(start, months);
        $('#leaseEnd').text(String(d.getDate()).padStart(2, '0') + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear());
        $('#leaseYears').text(App.fmt(months / 12, 2) + ' years');
      }
      if (v('rent_type') === undefined) return;
      const ac = App.num(v('total_lease_area')) * (ACRES[v('land_unit')] || 1);
      const annual = v('rent_type') === 'Fixed annual' ? App.num(v('rent_rate')) : App.num(v('rent_rate')) * ac;
      const inst = annual * (FREQ[v('payment_frequency')] || 12) / 12;
      const tds = v('tds_applicable') === 'Yes' ? inst * App.num(v('tds_rate')) / 100 : 0;
      $('#rentPreview').text(App.inr(annual) + ' / year · first instalment ' + App.inr(inst) + (tds ? ' (TDS ' + App.inr(tds) + ')' : ''));
    }

    $f.on('change', '[name=plot_id]', function () {
      const id = $(this).val();
      if (!id) return;
      $.getJSON(String($meta.data('plot-url')).replace('__ID__', id)).done(function (p) {
        $f.find('[name=total_lease_area]').val(p.area_under_lease_acres);
        $f.find('[name=land_unit]').val('Acre');
        $f.find('[name=rent_rate]').val(p.rate_per_acre);
        $f.find('[name=escalation_pct]').val(p.increment_pct);
        $f.find('[name=escalation_every_yrs]').val(p.increment_after_yrs);
        if (p.connectivity_mw && !v('leased_capacity_mw')) $f.find('[name=leased_capacity_mw]').val(p.connectivity_mw);
        $('#leasePlotInfo').html('<div class="note-b"><b>' + App.esc(p.owners || 'Ownership not finalised yet') + '</b> · Park ' + App.esc(p.park_code) + ' · SPV ' + App.esc(p.spv_code) +
          ' · ' + App.fmt(p.area_under_lease_acres, 4) + ' ac under lease · ' + App.inr(p.rate_per_acre) + ' per acre</div>');
        $('#parkCapInfo').text('Park ' + p.park_code + ': ' + App.fmt(p.park_installed) + ' MW installed · ' + App.fmt(p.park_leased_today) + ' MW leased today · ' +
          App.fmt(p.park_installed - p.park_leased_today) + ' MW free today');
        recalc();
      });
    });
    $f.on('input change', 'input,select', recalc);
    recalc();
  });
})(jQuery);
