/* Landowner share drawer: live total after save (must not exceed 100%). */
(function ($) {
  'use strict';
  $(document).on('drawer:loaded', function (e, module, $body) {
    if (module !== 'ownership') return;
    const $pct = $body.find('[name=share_percent]');
    if (!$pct.length) return;
    const others = Number($pct.data('others') || 0);
    const show = () => {
      const t = others + App.num($pct.val());
      $('#shareTotal').html('<b style="color:' + (t > 100.0001 ? 'var(--bad)' : Math.abs(t - 100) < 0.0001 ? 'var(--ok)' : 'inherit') + '">' + App.fmt(t, 4) + '%</b>' +
        (t > 100.0001 ? ' · exceeds 100%' : Math.abs(t - 100) < 0.0001 ? ' · ready to finalise' : ' · ' + App.fmt(100 - t, 4) + '% left'));
    };
    $pct.on('input', show);
    show();
  });
})(jQuery);
