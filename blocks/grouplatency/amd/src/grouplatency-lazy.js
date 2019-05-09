define(['jquery', 'core/ajax', 'core/templates'], function ($, ajax, Templates) {

        getPluginData = function (data) {
            var promises = ajax.call([
                {methodname: 'block_grouplatency_get_data', args: data}
            ]);

            promises[0].done(function (response) {
                var responsedata = JSON.parse(response.data);

                if (responsedata.hasdata != false || responsedata.preparation != 0) {
                    Templates.render('block_grouplatency/main', responsedata).then(function (html, js) {
                        Templates.replaceNodeContents('.grouplatency-wrapper', html, js);

                        if (responsedata.hasdata != false) {
                            require(['block_grouplatency/graph-lazy'], function (graph) {
                                graph.init(responsedata.posts, responsedata.task_period);
                            });
                        }

                        handlePrompt();
                    }).fail(function (ex) {
                        Templates.replaceNodeContents('.grouplatency-wrapper', 'rendering failed');
                    });
                } else {
                    $('.block_grouplatency').hide();
                }
            }).fail(function (ex) {
                Templates.replaceNodeContents('.grouplatency-wrapper', 'request failed');
            });
        };

        handlePrompt = function () {
            if (sessionStorage.getItem('prompt') != null) {
                var prompt_status = sessionStorage.getItem('prompt');

                if (prompt_status == 'expanded') {
                    $('.prompt-content').show();
                } else {
                    $('.prompt-content').hide();
                    $('.prompt .arrow').toggleClass('fa-caret-down fa-caret-right');
                }
            }

            $('.prompt-header').click(function () {
                $('.prompt .arrow').toggleClass('fa-caret-down fa-caret-right');
                $('.prompt-content').toggle();

                if ($('.prompt-content').is(":visible")) {
                    sessionStorage.setItem('prompt', 'expanded');
                } else {
                    sessionStorage.setItem('prompt', 'collapsed');
                }
            });
        };

        return {
            init: function (data) {
                getPluginData(data);
            }
        };
    }
);
