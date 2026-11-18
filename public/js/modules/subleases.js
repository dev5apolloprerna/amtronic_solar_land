/* Sub-lease drawer: live MW capacity bar for the chosen lease and period (mirrors CapacityService). */
(function ($) {
  'use strict';
  $(document).on('drawer:loaded', function (e, module, $body) {
    if (module !== 'sublease') return;
    const $f = $body.find('form'), $meta = $body.find('[data-sublease-form]');
    let timer = null;

    function render(c) {
      const mw = $f.find('[name=status]').val() === 'Active' || $f.find('[name=status]').length === 0 ? App.num($f.find('[name=capacity_mw]').val()) : 0;
      const total = c.lease_mw, used = c.peak, free = Math.max(0, c.free), over = mw - free;
      const pct = (x) => (total ? Math.max(0, Math.min(100, (x / total) * 100)) : 0);
      $('#capPanel').html(
        '<div class="cap"><div class="cap-head"><b>MW available on ' + App.esc(c.lease_no) + '</b><span class="mono">' + App.fmt(total) + ' MW leased · ' + App.esc(c.from) + ' to ' + App.esc(c.to) + '</span></div>' +
        '<div class="bar" role="img" aria-label="' + App.fmt(used) + ' of ' + App.fmt(total) + ' MW already sub-leased at peak">' +
        '<span class="seg used" style="width:' + pct(used) + '%"></span><span class="seg cur ' + (over > 0.0005 ? 'over' : '') + '" style="width:' + pct(Math.min(mw, free)) + '%"></span></div>' +
        '<div class="cap-leg"><span><i style="background:var(--muted)"></i>Already sub-leased (peak) ' + App.fmt(used) + ' MW</span>' +
        '<span><i style="background:' + (over > 0.0005 ? 'var(--bad)' : 'var(--accent)') + '"></i>This sub-lease ' + App.fmt(mw) + ' MW</span>' +
        '<span><i style="background:var(--sunk);border:1px solid var(--line)"></i>Free after save ' + App.fmt(Math.max(0, free - mw)) + ' MW</span></div>' +
        (over > 0.0005 ? '<p class="cap-err">' + (free <= 0.0005 ? 'No MW is free in this period. Change the dates.' : 'That is ' + App.fmt(over) + ' MW more than is free in this period. Enter ' + App.fmt(free) + ' MW or less.') + '</p>' : '') +
        '<span class="hint">Lease runs ' + App.esc(c.lease_start) + ' to ' + App.esc(c.lease_end) + '. Today ' + App.fmt(c.sub_today) + ' MW is sub-leased.</span></div>');
    }

    function refresh() {
      clearTimeout(timer);
      timer = setTimeout(function () {
        const leaseId = $f.find('[name=lease_id]').val() || $meta.data('lease');
        if (!leaseId) return;
        $.getJSON(String($meta.data('cap-url')).replace('__ID__', leaseId), {
          start: $f.find('[name=start_date]').val(), end: $f.find('[name=end_date]').val(), exclude: $meta.data('exclude') || '',
        }).done(render);
      }, 200);
    }

    $f.on('change', '[name=lease_id]', function () { $f.find('[name=start_date],[name=end_date]').val(''); refresh(); });
    $f.on('input change', '[name=capacity_mw],[name=start_date],[name=end_date],[name=status]', refresh);
    refresh();
  });
})(jQuery);
