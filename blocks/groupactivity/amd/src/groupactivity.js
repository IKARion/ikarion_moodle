// https://bl.ocks.org/bricedev/0d95074b6d83a77dc3ad

define(['jquery', 'block_groupactivity/d3'], function ($, d3) {

        buildChart = function (data) {
            var svg = d3.select("#dc-activity-chart svg"),
                margin = {top: 20, right: 20, bottom: 30, left: 40},
                width = +svg.attr("width") - margin.left - margin.right,
                height = +svg.attr("height") - margin.top - margin.bottom,
                g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            var x0 = d3.scaleBand()
                .rangeRound([0, width], .1);

            var x1 = d3.scaleBand();

            var y = d3.scaleLinear()
                .range([height, 0]);

            /*
            var xAxis = d3.svg.axis()
                .scale(x0)
                .tickSize(0)
                .orient("bottom");

            var yAxis = d3.svg.axis()
                .scale(y)
                .orient("left");
                */

            var color = d3.scaleBand()
                .range(["#ca0020","#f4a582","#d5d5d5","#92c5de","#0571b0"]);

            var categoriesNames = data.map(function(d) { return d.categorie; });
            var rateNames = data[0].values.map(function(d) { return d.rate; });

            x0.domain(categoriesNames);
            x1.domain(rateNames).rangeRound([0, x0.rangeRound()]);
            y.domain([0, d3.max(data, function(categorie) { return d3.max(categorie.values, function(d) { return d.value; }); })]);

            svg.append("g")
                .attr("class", "x axis")
                .attr("transform", "translate(0," + height + ")")
                //.call(xAxis);

            svg.append("g")
                .attr("class", "y axis")
                .style('opacity','0')
                //.call(yAxis)
                .append("text")
                .attr("transform", "rotate(-90)")
                .attr("y", 6)
                .attr("dy", ".71em")
                .style("text-anchor", "end")
                .style('font-weight','bold')
                .text("Value");

            svg.select('.y').transition().duration(500).delay(1300).style('opacity','1');

            var slice = svg.selectAll(".slice")
                .data(data)
                .enter().append("g")
                .attr("class", "g")
                .attr("transform",function(d) { return "translate(" + x0(d.categorie) + ",0)"; });

            slice.selectAll("rect")
                .data(function(d) { return d.values; })
                .enter().append("rect")
                .attr("width", x1.rangeBand())
                .attr("x", function(d) { return x1(d.rate); })
                .style("fill", function(d) { return color(d.rate) })
                .attr("y", function(d) { return y(0); })
                .attr("height", function(d) { return height - y(0); })
                .on("mouseover", function(d) {
                    d3.select(this).style("fill", d3.rgb(color(d.rate)).darker(2));
                })
                .on("mouseout", function(d) {
                    d3.select(this).style("fill", color(d.rate));
                });

            slice.selectAll("rect")
                .transition()
                .delay(function (d) {return Math.random()*1000;})
                .duration(1000)
                .attr("y", function(d) { return y(d.value); })
                .attr("height", function(d) { return height - y(d.value); });
        }

        return {
            init: function (data) {
                buildChart(data);
            }
        };
    }
);
