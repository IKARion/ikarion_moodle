define(['jquery'], function ($) {
        return {
            init: function () {
                $('#forum_chkbox').change(function () {
                    if ($(this).prop('checked') && $('#wiki_chkbox').prop('checked')) {
                        $('#forum-chart').hide();
                        $('#forum-wiki-chart').show();
                        $('#wiki-chart').hide();
                    } else {
                        $('#forum-chart').hide();
                        $('#forum-wiki-chart').hide();
                        $('#wiki-chart').show();
                    }
                });

                $('#wiki_chkbox').change(function () {
                    if ($(this).prop('checked')) {
                        $('#forum-chart').hide();
                        $('#forum-wiki-chart').hide();
                        $('#wiki-chart').show();
                    } else {
                        $('#forum-chart').show();
                        $('#forum-wiki-chart').hide();
                        $('#wiki-chart').hide();
                    }
                });

                $('.user-symbols span').each(function () {
                    var id = $(this).data('symbol-id');
                    var rect = $('#forum-wiki-chart rect[data-symbol-id="' + id + '"]');
                    var rect_start = $(rect).offset().left;
                    var top = $(this).offset().top;

                    $(this).offset({top: top, left: rect_start + 7});

                    console.log(rect_start);
                    console.log($(this).offset().left);
                });
            }
        };
    }
);
