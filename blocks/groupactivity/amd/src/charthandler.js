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
            }
        };
    }
);
