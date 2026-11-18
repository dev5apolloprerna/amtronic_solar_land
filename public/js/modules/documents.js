/* Upload drawer: choosing a survey number reloads the form with its linkable records. */
(function ($) {
  'use strict';
  $(document).on('drawer:loaded', function (e, module, $body) {
    if (module !== 'document') return;
    const $f = $body.find('form[data-doc-form]');
    $f.on('change', 'select[name=plot_id]', function () {
      if ($(this).val()) App.openDrawer($f.data('reload-url') + '?plot_id=' + encodeURIComponent($(this).val()));
    });
    const records = $('#docRecords').data('records') || {};
    $f.on('change', '[name=linked]', function () {
      const $r = $f.find('[name=record]').empty();
      Object.entries(records[$(this).val()] || {}).forEach(([id, label]) => $r.append($('<option>').val(id).text(label)));
    });
  });
})(jQuery);
