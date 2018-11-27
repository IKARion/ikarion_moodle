define(['jquery', 'block_grouplatency/d3', 'core/config'], function ($, d3, cfg) {

        buildChart = function (data) {
            var allCharts, catchAll, chart, globalX, height, maxX, minX, spanW, spanX, symbols, width,
                wrapper, x, xAxis;

            wrapper = document.querySelector('.block_grouplatency #dc-post-chart');

            data.forEach(function (d) {
                d.date = new Date(d.date * 1000);
            });

            var dateFormat = d3.timeFormat("%d.%m");
            width = wrapper.clientWidth;

            minX = d3.min(data, function (d) {
                return d.date;
            });

            maxX = d3.max(data, function (d) {
                return new Date();
            });

            x = d3.scaleTime().domain([minX, maxX]).rangeRound([0, width]);

            symbols = d3.nest().key(function (d) {
                return d.date;
            }).entries(data);

            spanX = function (d) {
                return x(d.date);
            };

            spanW = function (d) {
                return x(new Date()) - x(d.date);
            };

            height = 25;

            chart = function (symbol) {
                var svg;
                svg = d3.select(this);

                var g = svg.selectAll('rect').data(symbol.values).enter().append('g').attr('class', 'rect-row');
                var timelineWidth = 0;
                var timelineStart = 0;
                var barchart_pos = $('.block_grouplatency .bar-graph').offset().left;
                var barchart_width = $('.block_grouplatency .bar-graph').width();

                // timeline
                g.append('rect').attr('x', function (d) {
                    timelineStart = spanX(d);
                    return timelineStart;
                }).attr('y', 0)
                    .attr('width', function (d) {
                        timelineWidth = spanW(d);
                        return timelineWidth;
                    }).attr('height', height)
                    .attr('fill', function (d) {
                        return d.color || '#AEAEAE';
                    }).attr('data-id', function (d) {
                    return d.id;
                }).attr('class', 'overdue');

                // post back
                g.append('rect').attr('x', function (d) {
                    return (timelineStart + timelineWidth) - height;
                }).attr('y', 0).attr('width', function (d) {
                    return height;
                }).attr('height', height)
                    .attr('fill', function (d) {
                        return '#003560';
                    }).attr('class', 'answer');

                // text inside timeline
                g.append('text').text(function (d) {
                    return d.since;
                }).attr("x", function (d) {
                    var rect_end = (barchart_pos + barchart_width) - barchart_pos - 60;

                    return rect_end;
                }).attr("y", '18').attr("fill", "#003560").attr("text-anchor", "middle").attr('class', 'overdue-text');

                // text inside answer button
                g.append('text').attr('font-family', 'FontAwesome').attr('fontz-size', '14px').text(function (d) {
                    return '\uf112';
                }).attr("x", function (d) {
                    var rect_end = (barchart_pos + barchart_width) - barchart_pos - 13;
                    return rect_end;
                }).attr("y", '19').attr("fill", "#FFF").attr("text-anchor", "middle").attr('class', 'answer-icon')
                    .attr('data-postid', function (d) {
                        return d.postid;
                    }).attr('data-discussid', function (d) {
                    return d.discussionid;
                });
            };

            allCharts = d3.select(wrapper).selectAll('svg').data(symbols).enter().append('svg').attr('height', height + 5).attr('class', 'border').each(chart);
            xAxis = d3.axisBottom(x).ticks(2).tickFormat(d3.timeFormat("%d.%m."));
            globalX = d3.select(wrapper).append('svg').attr('class', 'axis').call(xAxis);

            // Define the div for the tooltip
            var div = d3.select("body").append("div")
                .attr("class", "ttip")
                .style("display", 'none');

            // Rect toolip
            allCharts.selectAll('.rect-row')
                .on("mouseover", function (d) {
                    div.transition()
                        .duration(200)
                        .style("display", "block");
                    div.html(
                        '<span style="color:#AEAEAE;">' + d.since + '</span><br>'
                        + '<a class="user-profile" href="' + cfg.wwwroot + '/user/view.php?id=' + d.userid + '&course=' + d.courseid + '"><strong>' + d.user + '</strong></a><br>'
                        + '<a class="post-message" href="' + cfg.wwwroot + '/mod/forum/post.php?reply=' + d.postid + '"><span' +
                        ' style="text-decoration:underline;">' + d.postmsg + '</span></a>'
                    ).style("left", function (d) {
                        var rect = d3.event.srcElement;
                        var text = $(rect).siblings('text.overdue-text').offset().left;
                        var tooltip = $('.ttip').outerWidth();

                        return (text - tooltip - 15) + "px";
                        //return (d3.event.pageX) + "px";
                    }).style("top", (d3.event.pageY - 28) + "px");
                });

            $('.ttip').on("mouseleave", function (d) {
                $(this).hide(500);
            });
        }

        return {
            init: function (data) {
                buildChart(data);
            }
        };
    }
);
