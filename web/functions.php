<?php

function connect_to_db() {    
    $host = "localhost";
    $dbuser = "root";
    $dbpass = "root";
    $dbname = "Group9db";

    $link = mysql_connect($host,$dbuser,$dbpass);

    if (!$link) {
        die('Could not connect: ' . mysql_error());
    }   
    return $link;  
}

function document_header() {
    $str = <<<MY_MARKER
<link rel='stylesheet' href='files/nv.d3.css' type='text/css'>
<script src='files/d3.v2.js' type='text/javascript' ></script>
<script src='files/nv.d3.js' type='text/javascript' ></script>
<script src="files/d3plus.js" type='text/javascript' ></script>
<script>
    var mycharts = [];
    function update_data_charts() {
        for (i = 0; i < mycharts.length; i++) {
            mycharts[i]();
        }
    }
</script>
MY_MARKER;
    echo $str;
}

function query_and_print_table($query,$title) {
    // Perform Query
    $result = mysql_query($query);
    // Check result
    // This shows the actual query sent to MySQL, and the error. Useful for debugging.
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }
    // Use result
    // Attempting to print $result won't allow access to information in the resource
    // One of the mysql result functions must be used
    // See also mysql_result(), mysql_fetch_array(), mysql_fetch_row(), etc.
    echo "<h2>" . $title . "</h2>";
    echo "<table align='center'>";
    echo "<thead><tr></tr>";
    $row = mysql_fetch_assoc($result);
    foreach ($row as $col => $value) {                
        echo "<th>" . $col . "</th>";
    }
    echo "</tr></thead>";
    // Write rows
    mysql_data_seek($result, 0);
    while ($row = mysql_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $e) {                
            echo "<td>" . $e . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    // Free the resources associated with the result set
    // This is done automatically at the end of the script
    mysql_free_result($result);
}

function query_and_print_graph_multibar($query,$query2,$title,$ylabel) {
    $id = "graph" . $GLOBALS['graphid'];
    $GLOBALS['graphid'] = $GLOBALS['graphid'] + 1;
    
    echo "<h3>" . $title . "</h3>";
    echo PHP_EOL,'<div id="'. $id . '"><svg style="height:300px"></svg></div>',PHP_EOL;
    // Perform Query
    $result = mysql_query($query);
    $result2 = mysql_query($query2);
    // Check result
    // This shows the actual query sent to MySQL, and the error. Useful for debugging.
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }
    $str = "<script type='text/javascript'>
        function " . $id . "Chart() {";
    $str = $str . <<<MY_MARKER
   nv.addGraph(function() {
    var chart = nv.models.multiBarChart()
        .x(function(d) { return d.label })
        .y(function(d) { return d.value })
        .margin({right: 150, left: 150})
        .showControls(false)
    chart.yAxis     //Chart y-axis settings
      .axisLabel('Patients')
      .tickFormat(d3.format('.0f'))
    chart.xAxis     //Chart x-axis settings
      .axisLabel('Age');
    
MY_MARKER;
    $str = $str . PHP_EOL . 'chart.yAxis.axisLabel("' . $ylabel . '").axisLabelDistance(30)';
    $str = $str . PHP_EOL . "d3.select('#" . $id . " svg')
          .datum(" . $id . "Data())
          .call(chart);";
    $str = $str . <<<MY_MARKER
      nv.utils.windowResize(chart.update);
      return chart;
    });
}    
MY_MARKER;
    $str = $str . PHP_EOL . $id . "Chart();" . PHP_EOL;
    $str = $str . PHP_EOL . "mycharts.push(". $id . "Chart)" . PHP_EOL;
    $str = $str . PHP_EOL . "function " . $id . 'Data() {
 return  [ 
    {
      "key": "Female", color: "#e1aebb"'; 
    $str = $str . ', values: [';
    while ($row = mysql_fetch_array($result)) {
        $str = $str . '{ "label":"' . $row[0] . '","value":' . $row[1] . '},' . PHP_EOL;
    }
    $str = $str . '] }, {
    "key": "Male", color: "#aecbe1" ';
    $str = $str . ', values: [';
    while ($row = mysql_fetch_array($result2)) {
        $str = $str . '{ "label":"' . $row[0] . '","value":' . $row[1] . '},' . PHP_EOL;
    }
    $str = $str . '] } ] }</script>';
    echo $str;
}

function query_and_print_table2($query,$title) {
    // Perform Query
    $result = mysql_query($query);
    // Check result
    // This shows the actual query sent to MySQL, and the error. Useful for debugging.
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }
    // Use result
    // Attempting to print $result won't allow access to information in the resource
    // One of the mysql result functions must be used
    // See also mysql_result(), mysql_fetch_array(), mysql_fetch_row(), etc.
    echo "<h2>" . $title . "</h2>";
    echo "<table align='center'>";
    echo "<thead><tr></tr>";
    $row = mysql_fetch_assoc($result);
    foreach ($row as $col => $value) {                
        echo "<th bgcolor='#B2C3CE'>" . $col . "</th>";
    }
    echo "</tr></thead>";
    // Write rows
    mysql_data_seek($result, 0);
    while ($row = mysql_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $e) {                
            echo "<td>" . $e . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    // Free the resources associated with the result set
    // This is done automatically at the end of the script
    mysql_free_result($result);
}

function create_table($query,$query2) {
    // Perform Query
    $result = mysql_query($query);
    $result2 = mysql_query($query2);

    // Check result
    // This shows the actual query sent to MySQL, and the error. Useful for debugging.
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }

    if (!$result2) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query2;
        die($message);
    }
    // mysql_free_result($result);
}


function treemap($query,$title,$label) {
    $id = "graph" . $GLOBALS['graphid'];
    $GLOBALS['graphid'] = $GLOBALS['graphid'] + 1;
    
    echo "<h2>" . $title . "</h2>";
    echo PHP_EOL,'<div align="center" id="'. $id . '"><svg style="height:500px; width:800px"></svg></div>',PHP_EOL;
    // Perform Query
    $result = mysql_query($query);
    // Check result
    // This shows the actual query sent to MySQL, and the error. Useful for debugging.
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }
    $str = "<script type='text/javascript'>
        function " . $id . "Chart() {";
    $str = $str . <<<MY_MARKER
       make_vis(function() {
        var chart = d3plus.viz()
        .container("#viz")
        .data(Data())
        .type("tree_map")
        .id("name")
        .size("value")
        .color("growth")
        .draw()

        return chart;
    });

}    
MY_MARKER;
    $str = $str . PHP_EOL . $id . "Chart();" . PHP_EOL;
    $str = $str . PHP_EOL . "mycharts.push(". $id . "Chart)" . PHP_EOL;
    $str = $str . PHP_EOL . "function " . 'Data() {
        return  ['; 
    while ($row = mysql_fetch_array($result)) {
        $str = $str . '{ "value":' . $row[1] . ',"name":"' . $row[0] . '","growth":' . $row[1] .'},' . PHP_EOL;
    }
    $str = $str . '] }</script>';
    echo $str;
}

function treemapdata($query,$title,$label) {
    $id = "graph" . $GLOBALS['graphid'];
    $GLOBALS['graphid'] = $GLOBALS['graphid'] + 1;
    
    echo "<h2>" . $title . "</h2>";
    echo PHP_EOL,'<div align="center" id="'. $id . '"><svg style="height:500px; width:800px"></svg></div>',PHP_EOL;
    // Perform Query
    $result = mysql_query($query);
    // Check result
    // This shows the actual query sent to MySQL, and the error. Useful for debugging.
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }
    $str = $str . '[';
    while ($row = mysql_fetch_array($result)) {
        $str = $str . ' { "value":' . $row[1] . ',"name":"' . $row[0] . '","growth":' . $row[1] .'},' . PHP_EOL;
    }
    rtrim($str, ", ");
    $str = $str . ']';
    
    return $str;
}


function cluster_table($query,$title) {
    // Perform Query
    $result = mysql_query($query);
    // Check result
    // This shows the actual query sent to MySQL, and the error. Useful for debugging.
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }
    // Use result
    // Attempting to print $result won't allow access to information in the resource
    // One of the mysql result functions must be used
    // See also mysql_result(), mysql_fetch_array(), mysql_fetch_row(), etc.
    echo "<h3>" . $title . "</h2>";
    echo "<table align='center'>";
    echo "<thead><tr></tr>";
    $row = mysql_fetch_assoc($result);
    foreach ($row as $col => $value) {                
        echo "<th class = 'rotate' ><div><span>" . $col . "</span></div></th>";
    }
    echo "</tr></thead>";
    // Write rows
    mysql_data_seek($result, 0);
    $compta = 0;
    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $e) {
            if($e == '0') echo "<td bgcolor='white'></td>";   
            else if($e == '1') echo "<td bgcolor='#FFEB9C'></td>";              
            else if($e == 'Significant') echo "<td bgcolor='#BBEDC3'><FONT COLOR='#006100'>" . $e . "</FONT></td>";
            else if($e == 'Non-Significant') echo "<td bgcolor='#FFC7CE'><FONT COLOR='#9C0006'>" . $e . "</FONT></td>";
            else echo "<td>" . $e . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    // Free the resources associated with the result set
    // This is done automatically at the end of the script
    mysql_free_result($result);
}

function table_data($query1, $query2) {
    // Perform Query
    $result = mysql_query($query1);
    $result2 = mysql_query($query2);

    // Check result
    // This shows the actual query sent to MySQL, and the error. Useful for debugging.
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query1;
        die($message);
    }

    if (!$result2) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query2;
        die($message);
    }
    // Use result
    // Attempting to print $result won't allow access to information in the resource
    // One of the mysql result functions must be used
    // See also mysql_result(), mysql_fetch_array(), mysql_fetch_row(), etc.
    echo "<table align='center'>";
    echo "<thead>";
    $row1 = mysql_fetch_assoc($result);
    $row2 = mysql_fetch_assoc($result2);
    echo "<td>Total number of patients</td> <td>Patients in selection</td>";
    echo "</thead>";
    // Write rows
    mysql_data_seek($result, 0);
    mysql_data_seek($result2, 0);
    while ($row = mysql_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $e) {                
            echo "<td align='center'>" . $e . "</td>";
        }
    }
    while ($row = mysql_fetch_assoc($result2)) {
        foreach ($row as $e) {                
            echo "<td align='center'>" . $e . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    // Free the resources associated with the result set
    // This is done automatically at the end of the script
    mysql_free_result($result);
}


?>
