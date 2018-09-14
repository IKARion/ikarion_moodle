define(['block_grouplatency/d3', 'block_grouplatency/crossfilter', 'block_grouplatency/dc'], function (d3, crossfilter, dc) {

        buildChart = function (data) {
            var allCharts, catchAll, chart, globalX, height, maxX, minX, parser, spanW, spanX, symbols, width,
                wrapper, x, xAxis, dataTable;

            dataTable = dc.dataTable("#dc-table-graph");
            wrapper = document.querySelector('#dc-post-chart');

            var facts = crossfilter(data);
            var date = facts.dimension(function (d) {
                return d.date;
            });

            var dateFormat = d3.timeFormat("%d.%m");

            data.forEach(function (d) {
                d.date = parseDate(d.date);
            });

            // Like d3.timeFormat, but faster.
            function parseDate(d) {
                return new Date(2018,
                    d.substring(0, 2) - 1,
                    d.substring(2, 4),
                    d.substring(4, 6),
                    d.substring(6, 8));
            }

            width = wrapper.clientWidth;
            parser = d3.isoParse;

            minX = d3.min(data, function (d) {
                return parser(d.date);
            });

            maxX = d3.max(data, function (d) {
                return new Date();
            });

            x = d3.scaleTime().domain([minX, maxX]).rangeRound([0, width]);

            symbols = d3.nest().key(function (d) {
                return d.date;
            }).entries(data);

            //height = wrapper.getBoundingClientRect().bottom;
            height = 15;

            spanX = function (d) {
                return x(parser(d.date));
            };

            spanW = function (d) {
                return x(parser(new Date())) - x(parser(d.date));
            };

            chart = function (symbol) {
                var svg;
                svg = d3.select(this);
                return svg.selectAll('rect').data(symbol.values).enter().append('rect').attr('x', function (d) {
                    return spanX(d);
                }).attr('y', 0).attr('width', function (d) {
                    return spanW(d);
                }).attr('height', height).attr('fill', function (d) {
                    return d.color || '#7f7f7f';
                }).attr('stroke-width', 1).attr('stroke', '#000')
                    .append("text")
                    .attr("x", function (d) {
                        return 20;
                    })
                    .attr("y", height / 2)
                    .attr("dy", ".35em")
                    .text(function (d) {
                        return 'tea';
                    });
            };

            allCharts = d3.select(wrapper).selectAll('svg').data(symbols).enter().append('svg').attr('height', height).each(chart);
            xAxis = d3.axisBottom(x).ticks(width / 100);
            globalX = d3.select(wrapper).append('svg').attr('class', 'axis').call(xAxis);
            catchAll = d3.select(wrapper).append('svg').attr('class', 'zoom').append('rect').attr('fill', 'none').attr('width', width).attr('height', wrapper.getBoundingClientRect().bottom);

            catchAll.call(d3.zoom().scaleExtent([0.1, 10]).on('zoom', function () {
                var transform;
                transform = d3.event.transform;
                globalX.call(xAxis.scale(transform.rescaleX(x)));
                return allCharts.selectAll('rect').attr('x', function (d) {
                    return transform.applyX(spanX(d));
                }).attr('width', function (d) {
                    return transform.k * spanW(d);
                });
            }));

            dataTable.width(232)
                .dimension(date)
                .group(function (d) {
                    return "";
                })
                .size(10)
                .columns([
                    function (d) {
                        return dateFormat(d.date);
                    },
                    function (d) {
                        return '<a href="' + d.postid + '">' + d.posttitle + '</a>';
                    },
                    function (d) {
                        var date1 = new Date(d.date);
                        var date2 = new Date();
                        var timeDiff = Math.abs(date2.getTime() - date1.getTime());
                        var diffHours = Math.ceil(timeDiff / (1000 * 3600));

                        return '+ ' + diffHours + ' Std.';
                    }
                ])
                .sortBy(function (d) {
                    return d.date;
                })
                .order(d3.ascending)
                .render();
        }

        return {
            init: function (data) {
                buildChart(data);
            }
        };
    }
);
