/* Permission matrix: unticking View clears the row; ticking any action ticks View. */
(function ($) {
  'use strict';
  $(document).on('change', '#permForm input[type=checkbox]', function () {
    const $row = $(this).closest('tr'), act = $(this).data('act');
    if (act === 'view' && !this.checked) $row.find('input[type=checkbox]').prop('checked', false);
    if (act !== 'view' && this.checked) $row.find('input[data-act=view]').prop('checked', true);
    $('#permHint').text('Unsaved changes. Each change is logged to the audit trail when you save.');
  });
})(jQuery);
