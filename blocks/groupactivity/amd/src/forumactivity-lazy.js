define(['jquery', 'block_groupactivity/d3'], function ($, d3) {

        buildChart = function (data) {
            var svg = d3.select("#dc-activity-chart svg#forum-chart"),
                margin = {top: 0, right: 0, bottom: 0, left: 0},
                width = +svg.attr("width") - margin.left - margin.right,
                height = +svg.attr("height") - margin.top - margin.bottom,
                center = 0,
                g = svg.append("g").attr("transform", "translate(" + center + "," + margin.top + ")");

            var x = d3.scaleBand().rangeRound([0, 120], .1);
            var y = d3.scaleLinear().rangeRound([height, 0]);

            x.domain(data.map(function (d) {
                return d.name;
            }));
            y.domain([0, d3.max(data, function (d) {
                return d.words_total;
            })]);
            
            g.selectAll(".bar")
                .data(data)
                .enter().append("rect")
                .attr("class", "bar")
                .attr("x", function (d) {
                    return x(d.name);
                })
                .attr("y", function (d) {
                    return y(d.words_forum);
                })
                .attr("width", 25)
                .attr("height", function (d) {
                    return height - y(d.words_forum);
                })
                .attr('fill', '#003560');
        }

        return {
            init: function (data) {
                buildChart(data);
            }
        };
    }
);
