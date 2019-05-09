define(['jquery', 'block_grouplatency/d3-lazy', 'block_grouplatency/crossfilter-lazy', 'block_grouplatency/dc-lazy'], function ($, d3, crossfilter, dc) {

        var resultStart = 0;
        var resultEnd = 5;
        var dataTable;
        var facts;
        var postChart;

        buildChart = function (data, period) {
            var colorDomain = [];
            var colorRange = [];

            startDate = new Date(period[0] * 1000);
            startDate = startDate.setHours(0, 0, 0, 0);

            var lastPostDate = d3.max(data, function (d) {
                return d.date;
            });

            lastPostDate = new Date(lastPostDate * 1000);
            lastPostDate = lastPostDate.setHours(23, 59, 59, 999);

            endDate = new Date(period[1] * 1000);
            endDate = endDate.setHours(0, 0, 0, 0);

            data.forEach(function (d, i) {
                d.date = new Date(d.date * 1000);
                colorDomain.push(d.date);
                colorRange.push(d.color);
            });

            /******************************************************
             * Step1: Create the dc.js chart objects & ling to div *
             ******************************************************/

            postChart = dc.barChart("#dc-latency-chart");
            dataTable = dc.dataTable("#dc-latency-table");

            /****************************************
             *    Run the data through crossfilter    *
             ****************************************/

            facts = crossfilter(data);  // Gets our 'facts' into crossfilter

            /******************************************************
             * Create the Dimensions                               *
             * A dimension is something to group or filter by.     *
             * Crossfilter can filter by exact value, or by range. *
             ******************************************************/
            var date = facts.dimension(function (d) {
                return d.date;
            });

            var dates = date.group()
                .reduceCount(function (d) {
                    return d.date;
                });

            /***************************************
             *    Step4: Create the Visualisations   *
             ***************************************/
            postChart.width(232)
                .height(80)
                .mouseZoomable(false)
                .margins({top: 0, right: 15, bottom: 20, left: 15})
                .dimension(date)								// the values across the x axis
                .group(dates)							// the values on the y axis
                .transitionDuration(500)
                .centerBar(true)
                .x(d3.scaleTime().domain([startDate, endDate]).range([0, 232]))
                .xUnits(function () {
                    return 30;
                })
                .elasticY(true)
                .xAxis()
                .ticks(d3.timeDay.every(1))
                .tickFormat(function (d, i) {
                    if (i == 0 || i == 7 || i == 14) {
                        return d3.timeFormat("%d.%m.")(d);
                    } else {
                        return d3.timeFormat("");
                    }
                });

            postChart.yAxis().ticks(0);

            postChart.colors(d3.scaleOrdinal().domain(colorDomain)
                .range(colorRange))
                .colorAccessor(function (d) {
                    return d.key;
                });

            dataTable.width(232)
                .dimension(date)
                .group(function (d) {
                    return "";
                })
                .size(Infinity)
                .columns([
                    function (d) {
                        return d.date_formatted;
                    },
                    function (d) {
                        return '<a style="color:' + d.color + '" href="/mod/forum/discuss.php?d=' + d.postid + '"><strong>' + d.postmsg + '</strong></a>';
                    },
                    function (d) {
                        return d.since;
                    }
                ])
                .sortBy(function (d) {
                    return d.date;
                })
                .order(d3.descending);

            /****************************
             * Step6: Render the Charts  *
             ****************************/
            updateResult();
            dc.renderAll();

            postChart
                .filter(dc.filters.RangedFilter(startDate, lastPostDate));
        };

        updateResult = function () {
            dataTable.beginSlice(resultStart);
            dataTable.endSlice(resultStart + resultEnd);

            if (facts.size() > resultEnd) {
                if ($('.block_grouplatency #pagination').length == 0) {
                    $('.block_grouplatency .grouplatency-wrapper').append('<div id="pagination"></div>');
                }
                displayPagination();
            }
        };

        displayPagination = function () {
            var maxPages = Math.ceil(facts.size() / resultEnd);
            var currentPage = resultStart / resultEnd + 1;

            var buttons = '<button class="btn" id="prev"><i class="fa fa-chevron-left"></i></button>';
            buttons += '<button class="btn" disabled="disabled">' + currentPage + ' / ' + maxPages + '</button>';
            buttons += '<button class="btn" id="next"><i class="fa fa-chevron-right"></i></button>';

            $('.block_grouplatency #pagination').html(buttons);
            $('.block_grouplatency #pagination #prev').attr('disabled', resultStart - resultEnd < 0 ? 'true' : null);
            $('.block_grouplatency #pagination #next').attr('disabled', resultStart + resultEnd >= facts.size() ? 'true' : null);

            $('.block_grouplatency #pagination #prev').click(function () {
                resultStart -= resultEnd;
                updateResult();
                dataTable.redraw();
            });

            $('.block_grouplatency #pagination #next').click(function () {
                resultStart += resultEnd;
                updateResult();
                dataTable.redraw();
            });
        };

        return {
            init: function (data, period) {
                buildChart(data, period);
            }
        };
    }
);
