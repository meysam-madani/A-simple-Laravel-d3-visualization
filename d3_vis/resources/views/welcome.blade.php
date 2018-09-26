<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://d3js.org/d3.v4.min.js"></script>
  <title>A-simple-Laravel-d3-visualization</title>
  <style>
  h2{
    color:#944  ;
  }
  .cde{
    background-color: #ffe;
    border:1px solid #444;
    padding:5px;
    color:#226;
  }
  .in-link{
    color:#118;
    background-color: #eef;

  }
</style>
</head>
<body>
  <div id="description">
    <h2>How to do that?</h2>
    <ul>
      <li> Create a laravel project (or use yours)
        <pre class="cde">composer create-project --prefer-dist laravel/laravel d3_vis</pre>
      </li>
      <li>Take a look at d3 calendar <a class="in-link" href="https://bl.ocks.org/mbostock/4063318">https://bl.ocks.org/mbostock/4063318</a></li>
      <li>Take a look at its data <a class="in-link" href="https://bl.ocks.org/mbostock/raw/4063318/dji.csv">https://bl.ocks.org/mbostock/raw/4063318/dji.csv</a>. Import it as a table in a mysql database "test" table "calendar"</li>
      <li>make a controlle for returning data by:
        <pre class="cde">php artisan make:controller CalendarData</pre>
      </li>
      <li>Take a look at <span class="in-link">CalendarData</span> controller in <span class="in-link">App/Http/Controllers</span>. We get data from table "calendar", you can customize that fetch.</li>
      <li>In <span class="in-link">routes/web.php</span> add the route <span class="in-link">"/calander_data.json"</span> there. Check the route <span class="in-link">http://localhost:8000/calander_data.json</span> </li>
      <li>Now we need some minor changes to d3 calendar referenced above, we do that in the following script</li>
      <pre class="cde"></pre>
    </ul>
  </div>
  <h2>Calendar view</h2>
  <div id="container"></div>
</body>

<script>
var width = 960,
height = 136,
cellSize = 17;

var formatPercent = d3.format(".1%");

var color = d3.scaleQuantize()
.domain([-0.05, 0.05])
.range(["#a50026", "#d73027", "#f46d43", "#fdae61", "#fee08b", "#ffffbf", "#d9ef8b", "#a6d96a", "#66bd63", "#1a9850", "#006837"]);

var svg = d3.select("#container")
.selectAll("svg")
.data(d3.range(1990, 2011))
.enter().append("svg")
.attr("width", width)
.attr("height", height)
.append("g")
.attr("transform", "translate(" + ((width - cellSize * 53) / 2) + "," + (height - cellSize * 7 - 1) + ")");

svg.append("text")
.attr("transform", "translate(-6," + cellSize * 3.5 + ")rotate(-90)")
.attr("font-family", "sans-serif")
.attr("font-size", 10)
.attr("text-anchor", "middle")
.text(function(d) { return d; });

var rect = svg.append("g")
.attr("fill", "none")
.attr("stroke", "#ccc")
.selectAll("rect")
.data(function(d) { return d3.timeDays(new Date(d, 0, 1), new Date(d + 1, 0, 1)); })
.enter().append("rect")
.attr("width", cellSize)
.attr("height", cellSize)
.attr("x", function(d) { return d3.timeWeek.count(d3.timeYear(d), d) * cellSize; })
.attr("y", function(d) { return d.getDay() * cellSize; })
.datum(d3.timeFormat("%Y-%m-%d"));

svg.append("g")
.attr("fill", "none")
.attr("stroke", "#000")
.selectAll("path")
.data(function(d) { return d3.timeMonths(new Date(d, 0, 1), new Date(d + 1, 0, 1)); })
.enter().append("path")
.attr("d", pathMonth);

d3.json("/calander_data.json", function(error, json) {
  if (error) throw error;

  var data = d3.nest()
  .key(function(d) { return d.Date; })
  .rollup(function(d) { return (d[0].Close - d[0].Open) / d[0].Open; })
  .object(json);

  rect.filter(function(d) { return d in data; })
  .attr("fill", function(d) { return color(data[d]); })
  .append("title")
  .text(function(d) { return d + ": " + formatPercent(data[d]); });
});

function pathMonth(t0) {
  var t1 = new Date(t0.getFullYear(), t0.getMonth() + 1, 0),
  d0 = t0.getDay(), w0 = d3.timeWeek.count(d3.timeYear(t0), t0),
  d1 = t1.getDay(), w1 = d3.timeWeek.count(d3.timeYear(t1), t1);
  return "M" + (w0 + 1) * cellSize + "," + d0 * cellSize
  + "H" + w0 * cellSize + "V" + 7 * cellSize
  + "H" + w1 * cellSize + "V" + (d1 + 1) * cellSize
  + "H" + (w1 + 1) * cellSize + "V" + 0
  + "H" + (w0 + 1) * cellSize + "Z";
}
</script>
</html>
