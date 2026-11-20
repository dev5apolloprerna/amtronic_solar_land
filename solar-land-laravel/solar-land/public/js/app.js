/* ==========================================================================
   Solar Land Lease – core UI behaviour (jQuery)
   Drawer forms, delete modal, toasts, cascading dropdowns, mobile nav.
   Module-specific code lives in public/js/modules/<module>.js and hooks into
   the 'drawer:loaded' event fired after a drawer form is rendered.
   ========================================================================== */
(function ($) {
  'use strict';

  const App = (window.App = window.App || {});

  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
  });

  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  App.esc = esc;

  /* ---------- toasts ---------- */
  App.toast = function (text, sub) {
    const $t = $('<div class="toast" role="status"></div>').html(esc(text) + (sub ? '<small>' + esc(sub) + '</small>' : ''));
    $('#toasts').append($t);
    setTimeout(() => $t.remove(), 4200);
  };
  // toast queued before a page reload
  try {
    const q = sessionStorage.getItem('toast');
    if (q) { const t = JSON.parse(q); App.toast(t.text, t.sub); sessionStorage.removeItem('toast'); }
  } catch (_) { /* storage unavailable */ }
  // toast from server flash
  $('#flash [data-toast]').each(function () { App.toast($(this).data('toast'), $(this).data('sub')); });

  App.reloadWithToast = function (text, sub, url) {
    try { sessionStorage.setItem('toast', JSON.stringify({ text, sub })); } catch (_) { /* ignore */ }
    if (url) window.location.href = url; else window.location.reload();
  };

  /* ---------- drawer ---------- */
  const $drawer = $('#drawer'), $scrim = $('#scrim');

  App.openDrawer = function (url) {
    $('#drBody').html('<p class="hint">Loading…</p>');
    $('#drHead').html('');
    $drawer.prop('hidden', false); $scrim.prop('hidden', false);
    $.get(url).done(function (res) {
      $('#drHead').html('<div><small>' + esc(res.sub || '') + '</small><h2>' + esc(res.title || '') + '</h2></div><button class="x" data-act="close-drawer" aria-label="Close">×</button>');
      $('#drBody').html(res.html);
      $('#saveBtn').prop('hidden', !!res.readonly).text(res.saveLabel || 'Save');
      $(document).trigger('drawer:loaded', [res.module || '', $('#drBody')]);
      setTimeout(() => $('#drBody').find('input:not([type=hidden]):not([type=checkbox]):not([type=file]),select,textarea').first().trigger('focus'), 30);
    }).fail(function (xhr) {
      $('#drBody').html('<div class="err">' + esc(errorText(xhr)) + '</div>');
      $('#saveBtn').prop('hidden', true);
    });
  };

  App.closeDrawer = function () { $drawer.prop('hidden', true); $scrim.prop('hidden', true); $('#drBody').empty(); };

  function errorText(xhr) {
    if (xhr.responseJSON && xhr.responseJSON.message) return xhr.responseJSON.message;
    if (xhr.status === 403) return 'You do not have permission for this action.';
    if (xhr.status === 419) return 'Your session expired. Reload the page and sign in again.';
    return 'Something went wrong (' + xhr.status + '). Try again.';
  }

  function showErrors($form, xhr) {
    $form.find('.ferr').remove(); $form.find('.has-err').removeClass('has-err');
    const errs = (xhr.responseJSON && xhr.responseJSON.errors) || {};
    const first = Object.values(errs)[0];
    $form.find('.err.top').remove();
    $form.prepend('<div class="err top" role="alert">' + esc(first ? first[0] : errorText(xhr)) + '</div>');
    Object.keys(errs).forEach(function (k) {
      const name = k.replace(/\.(\w+)/g, '[$1]');
      const $f = $form.find('[name="' + name + '"],[name="' + name + '[]"]').first().closest('.fld');
      $f.addClass('has-err').append('<small class="ferr">' + esc(errs[k][0]) + '</small>');
    });
    $form.closest('.dr-body,#inlineBody').scrollTop(0);
  }
  App.showErrors = showErrors;

  /** Submit any form with AJAX (drawer or inline). */
  App.submitForm = function ($form, $btn) {
    const fd = new FormData($form[0]);
    $btn && $btn.addClass('loading');
    return $.ajax({ url: $form.attr('action'), method: 'POST', data: fd, processData: false, contentType: false })
      .done(function (res) { App.reloadWithToast(res.message || 'Saved', res.sub, res.redirect); })
      .fail(function (xhr) { showErrors($form, xhr); })
      .always(function () { $btn && $btn.removeClass('loading'); });
  };

  $(document).on('click', '#saveBtn', function () {
    const $f = $('#drBody form').first();
    if ($f.length) App.submitForm($f, $(this));
  });
  $(document).on('submit', 'form[data-ajax]', function (e) { e.preventDefault(); App.submitForm($(this), $(this).find('[type=submit]')); });
  $(document).on('submit', '#drBody form', function (e) { e.preventDefault(); App.submitForm($(this), $('#saveBtn')); });

  /* ---------- modal ---------- */
  App.modal = function (title, body, foot, wide) {
    $('#modal').html('<div class="modal' + (wide ? ' wide' : '') + '" role="dialog" aria-modal="true" aria-label="' + esc(title) + '"><h3>' + esc(title) + '</h3><div class="mb">' + body + '</div><div class="mf">' + foot + '</div></div>').prop('hidden', false);
    setTimeout(() => $('#modal .mf .btn').last().trigger('focus'), 20);
  };
  App.closeModal = function () { $('#modal').prop('hidden', true).empty(); };

  let pending = null;
  $(document).on('click', '[data-delete-url]', function () {
    const $b = $(this);
    pending = { url: $b.data('delete-url'), method: 'DELETE' };
    App.modal('Delete ' + $b.data('label') + '?',
      '<p style="margin:0 0 8px">' + esc($b.data('message') || 'This removes the record. The deletion, who did it and when are kept in the audit trail.') + '</p>',
      '<button class="btn" data-act="modal-close">Cancel</button><button class="btn danger" data-act="confirm">Delete</button>');
  });
  $(document).on('click', '[data-confirm-url]', function () {
    const $b = $(this);
    pending = { url: $b.data('confirm-url'), method: $b.data('method') || 'POST' };
    App.modal($b.data('title') || 'Are you sure?', '<p style="margin:0">' + esc($b.data('message') || '') + '</p>',
      '<button class="btn" data-act="modal-close">Cancel</button><button class="btn pri" data-act="confirm">' + esc($b.data('ok') || 'Confirm') + '</button>');
  });
  $(document).on('click', '[data-act="confirm"]', function () {
    if (!pending) return;
    $.ajax({ url: pending.url, method: 'POST', data: { _method: pending.method } })
      .done((res) => App.reloadWithToast(res.message || 'Done', res.sub, res.redirect))
      .fail((xhr) => App.modal("Can't do this", '<p style="margin:0">' + esc(errorText(xhr)) + '</p>', '<button class="btn pri" data-act="modal-close">OK</button>'));
    pending = null;
  });

  /* ---------- generic click actions ---------- */
  $(document).on('click', '[data-drawer]', function (e) { e.preventDefault(); App.openDrawer($(this).data('drawer')); });
  $(document).on('click', '[data-act="close-drawer"]', App.closeDrawer);
  $(document).on('click', '[data-act="modal-close"]', App.closeModal);
  $(document).on('click', '[data-act="menu"]', () => $('#nav').toggleClass('open'));
  $scrim.on('click', function () { App.closeDrawer(); $('#nav').removeClass('open'); });
  $(document).on('keydown', function (e) {
    if (e.key !== 'Escape') return;
    if (!$('#modal').prop('hidden')) App.closeModal(); else if (!$drawer.prop('hidden')) App.closeDrawer();
  });
  $(document).on('click', '#modal', function (e) { if (e.target.id === 'modal') App.closeModal(); });

  /* History modal: loads audit rows for one record */
  $(document).on('click', '[data-history]', function () {
    $.get($(this).data('history')).done((res) => App.modal(res.title, res.html, '<button class="btn pri" data-act="modal-close">Close</button>', true));
  });

  /* ---------- filter bars ---------- */
  $(document).on('change', 'form.bar-f select, form.bar-f input[type=date]', function () { $(this).closest('form').trigger('submit'); });
  let searchTimer = null;
  $(document).on('input', 'form.bar-f input[type=search]', function () {
    const $f = $(this).closest('form');
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => $f.trigger('submit'), 450);
  });
  // keep the caret in the search box after reload
  const $s = $('form.bar-f input[type=search]').first();
  if ($s.length && $s.val()) { const v = $s.val(); $s.trigger('focus')[0].setSelectionRange(v.length, v.length); }

  /* ---------- cascading dropdowns ----------
     <select data-cascade="#city_id" data-cascade-url="/lookup/cities?state_id="> fills the target with JSON [{id,label}] */
  $(document).on('change', 'select[data-cascade]', function () {
    const $src = $(this), $scope = $src.closest('form').length ? $src.closest('form') : $(document);
    const $target = $scope.find($src.data('cascade'));
    if (!$target.length) return;
    const keep = $target.data('keep');
    $target.html('<option value="">Select…</option>');
    if (!$src.val()) { $target.trigger('change'); return; }
    $.getJSON($src.data('cascade-url') + encodeURIComponent($src.val())).done(function (rows) {
      rows.forEach((r) => $target.append($('<option>').val(r.id).text(r.label)));
      if (keep) $target.val(String(keep));
      $target.data('keep', null).trigger('change');
    });
  });

  /* ---------- file drop zone ---------- */
  $(document).on('dragover', '.drop', function (e) { e.preventDefault(); $(this).addClass('hot'); });
  $(document).on('dragleave drop', '.drop', function () { $(this).removeClass('hot'); });
  $(document).on('drop', '.drop', function (e) {
    e.preventDefault();
    const files = e.originalEvent.dataTransfer.files, input = $(this).find('input[type=file]')[0];
    if (input && files.length) { input.files = files; $(input).trigger('change'); }
  });
  $(document).on('change', '.drop input[type=file]', function () {
    const f = this.files[0], $z = $(this).closest('.drop');
    $z.find('.file-pill').remove();
    if (!f) return;
    const max = Number($z.data('max-mb') || 100) * 1048576;
    if (f.size > max) { App.toast(f.name + ' is larger than ' + $z.data('max-mb') + ' MB'); this.value = ''; return; }
    $z.append('<div class="file-pill"><span class="mono">' + esc(f.name) + '</span><span class="sub">' + (f.size / 1048576).toFixed(2) + ' MB</span></div>');
    const $title = $(this).closest('form').find('[name="doc_title"]');
    if ($title.length && !$title.val()) $title.val(f.name.replace(/\.[^.]+$/, '').replace(/[_-]+/g, ' '));
  });

  /* ---------- numbers ---------- */
  App.num = (v) => (v === '' || v == null || isNaN(v) ? 0 : Number(v));
  App.inr = (n) => '₹' + Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 });
  App.fmt = (n, d) => Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: d ?? 3 });
})(jQuery);
