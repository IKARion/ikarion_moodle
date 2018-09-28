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
                    var chart_start = $('#dc-activity-chart').offset().left;
                    var rect = $('#forum-wiki-chart rect[data-symbol-id="' + id + '"]');
                    var rect_x = parseInt($(rect).attr('x'));
                    var top = $(this).offset().top;

                    $(this).offset({top: top, left: (chart_start + rect_x + 7)});
                });
            }
        };
    }
);
