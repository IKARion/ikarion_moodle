define(['jquery'], function ($) {
        return {
            init: function () {
                $('#forum_chkbox, #wiki_chkbox').change(function () {
                    var forum_checked = $('#forum_chkbox').prop('checked');
                    var wiki_checked = $('#wiki_chkbox').prop('checked');

                    if (!forum_checked && !wiki_checked) {
                        $('#forum-chart').hide();
                        $('#forum-wiki-chart').hide();
                        $('#wiki-chart').hide();
                    }

                    if (forum_checked && wiki_checked) {
                        $('#forum-chart').hide();
                        $('#wiki-chart').hide();
                        $('#forum-wiki-chart').show();
                    }

                    if (!forum_checked && wiki_checked) {
                        $('#forum-chart').hide();
                        $('#forum-wiki-chart').hide();
                        $('#wiki-chart').show();
                    }

                    if (forum_checked && !wiki_checked) {
                        $('#forum-wiki-chart').hide();
                        $('#wiki-chart').hide();
                        $('#forum-chart').show();
                    }
                });

                $('.user-symbols span').each(function () {
                    var id = $(this).data('symbol-id');
                    //var chart_start = $('#dc-activity-chart').offset().left;
                    var chart_start = $('#forum-wiki-chart').offset().left;
                    var rect = $('#forum-wiki-chart rect[data-symbol-id="' + id + '"]');
                    var rect_x = parseInt($(rect).attr('x'));
                    var top = $(this).offset().top;

                    $(this).offset({top: top, left: (chart_start + rect_x + 7)});
                });
            }
        };
    }
);
