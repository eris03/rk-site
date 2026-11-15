/* RK Developers — Project edit screen: gallery and image pickers (WordPress Media Library). */
(function ($) {
  'use strict';

  function syncGallery($wrap) {
    var ids = $wrap.find('.rk-gallery-list li').map(function () { return $(this).data('id'); }).get();
    $wrap.find('input[type=hidden]').val(ids.join(','));
  }

  $('.rk-gallery-field').each(function () {
    var $wrap = $(this);
    var $list = $wrap.find('.rk-gallery-list');
    $list.sortable({ update: function () { syncGallery($wrap); } });

    $wrap.on('click', '.rk-remove', function () {
      $(this).closest('li').remove();
      syncGallery($wrap);
    });

    var frame;
    $wrap.on('click', '.rk-add-gallery', function (e) {
      e.preventDefault();
      if (!frame) {
        frame = wp.media({ title: 'Project gallery', button: { text: 'Add to gallery' }, library: { type: 'image' }, multiple: 'add' });
        frame.on('select', function () {
          frame.state().get('selection').each(function (att) {
            var a = att.toJSON();
            if ($list.find('li[data-id="' + a.id + '"]').length) return;
            var thumb = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
            $list.append('<li data-id="' + a.id + '"><img src="' + thumb + '" /><button type="button" class="rk-remove" aria-label="Remove image">&times;</button></li>');
          });
          syncGallery($wrap);
        });
      }
      frame.open();
    });
  });

  $('.rk-image-field').each(function () {
    var $wrap = $(this), frame;
    $wrap.on('click', '.rk-pick-image', function (e) {
      e.preventDefault();
      if (!frame) {
        frame = wp.media({ title: 'Choose image', button: { text: 'Use this image' }, library: { type: 'image' }, multiple: false });
        frame.on('select', function () {
          var a = frame.state().get('selection').first().toJSON();
          var thumb = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
          $wrap.find('input').val(a.id);
          $wrap.find('img').attr('src', thumb).show();
        });
      }
      frame.open();
    });
    $wrap.on('click', '.rk-clear-image', function () {
      $wrap.find('input').val('');
      $wrap.find('img').attr('src', '').hide();
    });
  });
})(jQuery);
