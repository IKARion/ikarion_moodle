define(['jquery', 'block_grouplatency/d3'], function ($, d3) {

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
                var bfyesterday = new Date();
                bfyesterday.setDate(bfyesterday.getDate() - 2);

                return bfyesterday;
            });

            maxX = d3.max(data, function (d) {
                /*
                var tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate());

                return tomorrow;
                */

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

            height = 20;

            chart = function (symbol) {
                var svg;
                svg = d3.select(this);

                var g = svg.selectAll('rect').data(symbol.values).enter().append('g').attr('class', 'rect-row');
                var timelineWidth = 0;
                var timelineStart = 0;

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
                        return d.color || '#7c7d7e';
                    }).attr('data-id', function (d) {
                    return d.id;
                });

                // post back
                /*
                g.append('rect').attr('x', function (d) {
                    return timelineStart + timelineWidth;
                }).attr('y', 0)
                    .attr('width', function (d) {
                        return '30';
                    }).attr('height', height)
                    .attr('fill', function (d) {
                        return '#fff';
                    });
                    */

                // text inside timeline
                g.append('text').text(function (d) {
                    var date1 = d.date;
                    var date2 = new Date();
                    var timeDiff = Math.abs(date2.getTime() - date1.getTime());
                    var diffHours = Math.floor(timeDiff / (1000 * 3600));

                    return '+ ' + diffHours + ' Std.';
                })
                    .attr("x", function (d) {
                        var barchart_pos = $('.block_grouplatency .bar-graph').offset().left;
                        var rect_pos = $(this).siblings('rect').offset().left;
                        var rect_width = $(this).siblings('rect').width();
                        var rect_end = (rect_pos + rect_width) - barchart_pos - 40;

                        return rect_end;
                    })
                    .attr("y", '15')
                    .attr("fill", "#FFF")
                    .attr("text-anchor", "middle");
                ;
            };

            allCharts = d3.select(wrapper).selectAll('svg').data(symbols).enter().append('svg').attr('height', height + 5).attr('class', 'border').each(chart);
            xAxis = d3.axisBottom(x).ticks(width / 90).tickFormat(d3.timeFormat("%d.%m."));
            globalX = d3.select(wrapper).append('svg').attr('class', 'axis').call(xAxis);

            var chartPos = $(wrapper).position().top;
            var chartHeight = $(wrapper).outerHeight();

            catchAll = d3.select(wrapper).append('svg')
                .attr('class', 'zoom')
                .style('height', chartHeight + 'px')
                .style('top', chartPos + 'px')
                .append('rect')
                .attr('fill', 'none')
                .attr('width', '100%')
                .attr('height', '100%');

            catchAll.call(d3.zoom().scaleExtent([0.1, 10]).on('zoom', function () {
                var transform;

                transform = d3.event.transform;
                globalX.call(xAxis.scale(transform.rescaleX(x)));
                var chart_return = allCharts.selectAll('rect').attr('x', function (d) {
                    return transform.applyX(spanX(d));
                }).attr('width', function (d) {
                    return transform.k * spanW(d);
                });

                var rects = $('.block_grouplatency .bar-graph .rect-row rect');
                $(rects).each(function () {
                    var barchart_pos = $('.block_grouplatency .bar-graph').offset().left;
                    var barchart_width = $('.block_grouplatency .bar-graph').width();
                    var barchart_end = (barchart_pos + barchart_width);
                    var rect_pos = $(this).offset().left;
                    var rect_width = $(this).width();
                    var rect_end = (rect_pos + rect_width);
                    var id = $(this).data('id');

                    if (rect_end < barchart_pos) {
                        $('.block_grouplatency #dc-table-graph tr[data-id=' + id + ']').fadeOut();
                    } else if (rect_end > barchart_pos && rect_end < barchart_end) {
                        $('.block_grouplatency #dc-table-graph tr[data-id=' + id + ']').fadeIn();
                    } else if (rect_pos > barchart_end) {
                        $('.block_grouplatency #dc-table-graph tr[data-id=' + id + ']').fadeOut();
                    } else if (rect_pos < barchart_end) {
                        $('.block_grouplatency #dc-table-graph tr[data-id=' + id + ']').fadeIn();
                    }
                });

                // apply text to rect on zoom
                var texts = $('.block_grouplatency .bar-graph .rect-row text');
                $(texts).each(function () {
                    var barchart_pos = $('.block_grouplatency .bar-graph').offset().left;
                    var rect_pos = $(this).siblings('rect').offset().left;
                    var rect_width = $(this).siblings('rect').width();
                    var rect_end = (rect_pos + rect_width) - barchart_pos - 40;

                    $(this).attr('x', rect_end);
                });

                return chart_return;
            }));
        }

        return {
            init: function (data) {
                buildChart(data);
            }
        };
    }
);
