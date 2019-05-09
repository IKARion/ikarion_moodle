define(['jquery', 'core/ajax', 'core/templates'], function ($, ajax, Templates) {

        getPluginData = function (data) {
            var promises = ajax.call([
                {methodname: 'block_groupactivity_get_data', args: data}
            ]);

            promises[0].done(function (response) {
                var responsedata = JSON.parse(response.data);
                
                if (responsedata.showblock || responsedata.canmanage) {
                    if (responsedata.participation && !responsedata.maxvalue) {
                        $('.block_groupactivity').hide();
                    } else {
                        Templates.render('block_groupactivity/main', responsedata).then(function (html, js) {
                            Templates.replaceNodeContents('.groupactivity-wrapper', html, js);

                            if (responsedata.selfassessbutton > 0) {
                                responsedata.selfassessbutton == 1 ? showModal = 1 : showModal = 0;

                                require(['block_groupactivity/modal'], function (modal) {
                                    modal.init(data.instanceid, showModal);
                                });
                            }

                            if (responsedata.useritems.length && responsedata.maxvalue) {
                                require(['block_groupactivity/graph'], function (graph) {
                                    graph.init(responsedata.items, responsedata.maxvalue, responsedata.useritems);
                                });
                            }
                        }).fail(function (ex) {
                            Templates.replaceNodeContents('.groupactivity-wrapper', 'rendering failed');
                        });
                    }
                } else {
                    $('.block_groupactivity').hide();
                }
            }).fail(function (ex) {
                Templates.replaceNodeContents('.groupactivity-wrapper', 'request failed');
            });
        };

        return {
            init: function (data) {
                getPluginData(data);
            }
        };
    }
);
