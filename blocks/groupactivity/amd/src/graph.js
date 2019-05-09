define(['jquery', 'core/ajax'], function ($, ajax) {

        var svg;

        buildGraph = function (items, maxvalue, data) {
            var margin = {top: 0, right: 0, bottom: 0, left: 0};

            var width = 230,
                height = 150;

            svg = d3.select("#dc-activity-chart .bar-graph")
                .append("svg")
                .attr("width", width + margin.left + margin.right)
                .attr("height", height + margin.top + margin.bottom)
                .append("g")
                .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            // Transpose the data into layers
            var dataset = d3.layout.stack()(["item1", "item2", "item3"].map(function (item) {
                return data.map(function (d) {
                    return {x: d.id, y: +d[item], color: d.color, id: d.id};
                });
            }));

            // Set x, y and colors
            var x = d3.scale.ordinal()
                .domain(dataset[0].map(function (d) {
                    return d.x;
                }))
                .rangeRoundBands([10, width], 0.5);

            var y = d3.scale.linear()
                .domain([0, maxvalue])
                .range([height, 0]);

            // Create groups for each series, rects for each segment
            var groups = svg.selectAll("g.cost")
                .data(dataset)
                .enter().append("g")
                .attr("class", "cost")
                .attr("data-id", function (d, i) {
                    return i;
                });

            var rect = groups.selectAll("rect")
                .data(function (d) {
                    return d;
                })
                .enter()
                .append("rect")
                .attr("x", function (d) {
                    return x(d.x);
                })
                .attr("y", function (d) {
                    return y(d.y0 + d.y);
                })
                .attr("height", function (d) {
                    return y(d.y0) - y(d.y0 + d.y);
                })
                .attr("width", "25px")
                .style("stroke", "#7E7E7F")
                .attr('data-symbol-id', function (d) {
                    return d.id;
                }).style("fill", function (d, i) {
                    var groupid = $(this).parent().data('id');
                    
                    switch (groupid) {
                        case 0:
                            var fillPattern = svg.append("pattern")
                                .attr("id", "rectpattern" + groupid + d.id)
                                .attr("patternUnits", "userSpaceOnUse")
                                .attr("width", 5)
                                .attr("height", 5)
                                .attr("patternTransform", "rotate(90)");
                            var fillPatternRectangle = fillPattern.append("rect")
                                .attr("height", 5)
                                .attr("width", 5)
                                .attr("fill", hexToRGB(d.color, 0.7));
                            fillPatternRectangle = fillPattern.append("rect")
                                .attr("height", 5)
                                .attr("width", 2)
                                .attr("fill", d.color);

                            return "url(#rectpattern" + groupid + d.id + ")";
                        case 1:
                            var fillPattern = svg.append("pattern")
                                .attr("id", "rectpattern" + groupid + d.id)
                                .attr("patternUnits", "userSpaceOnUse")
                                .attr("width", 5)
                                .attr("height", 5);
                            var fillPatternRectangle = fillPattern.append("rect")
                                .attr("height", 5)
                                .attr("width", 5)
                                .attr("fill", hexToRGB(d.color, 0.5));
                            fillPatternRectangle = fillPattern.append("rect")
                                .attr("height", 5)
                                .attr("width", 2)
                                .attr("fill", d.color);

                            return "url(#rectpattern" + groupid + d.id + ")";
                        case 2:
                            return hexToRGB(d.color, 0.3);
                    }
                });

            // Draw legend
            var legendsvg = d3.select("#dc-activity-chart .legend-graph")
                .append("svg")
                .attr("width", width + margin.left + margin.right)
                .attr("height", 70)
                .append("g")
                .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            var legend = legendsvg.selectAll(".legend")
                .data(items)
                .enter().append("g")
                .attr("class", "legend")
                .attr("transform", function (d, i) {
                    var pos = (items.length - 1) - i;

                    return "translate(0," + pos * 25 + ")";
                });

            legend.append("rect")
                .attr("x", 0)
                .attr("width", 18)
                .attr("height", 18)
                .style("stroke", "#7E7E7E")
                .style("fill", function (d, i) {
                    switch (i) {
                        case 2:
                            return "none";
                            break;
                        case 1:
                            return "url(#vertLines)";
                            break;
                        case 0:
                            return "url(#horzLines)";
                            break;
                        default:
                            return "none";
                    }
                });

            legend.append("text")
                .attr("x", 25)
                .attr("y", 9)
                .attr("dy", ".35em")
                .style("text-anchor", "start")
                .text(function (d, i) {
                    return items[i].shortname;
                });
        };

        hexToRGB = function hexToRGB(hex, alpha) {
            var r = parseInt(hex.slice(1, 3), 16),
                g = parseInt(hex.slice(3, 5), 16),
                b = parseInt(hex.slice(5, 7), 16);

            if (alpha) {
                return "rgba(" + r + ", " + g + ", " + b + ", " + alpha + ")";
            } else {
                return "rgb(" + r + ", " + g + ", " + b + ")";
            }
        };

        return {
            init: function (items, maxvalue, data) {
                buildGraph(items, maxvalue, data);

                $('.user-symbols span').each(function () {
                    var id = $(this).data('symbol-id');
                    var chart_start = $('#dc-activity-chart').offset().left;
                    var rect = $('#dc-activity-chart rect[data-symbol-id="' + id + '"]');
                    var rect_x = parseInt($(rect).attr('x'));
                    var top = $(this).offset().top;

                    $(this).offset({top: top, left: (chart_start + rect_x + 7)});
                });

                $('svg rect').click(function (d) {
                    var fillPattern = svg.append("pattern")
                        .attr("id", "rectpattern" + d.id)
                        .attr("patternUnits", "userSpaceOnUse")
                        .attr("width", 5)
                        .attr("height", 5)
                        .attr("patternTransform", "rotate(45)");
                    var fillPatternRectangle = fillPattern.append("rect")
                        .attr("height", 20)
                        .attr("width", 3)
                        .attr("fill", 'blue');

                    $(this).attr("fill", "url(#rectpattern" + d.id + ")");
                });
            }
        };
    }
);
