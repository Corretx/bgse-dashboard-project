<?php

function connect_to_db() {    
    $host = "127.0.0.1";
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

function query_and_print_graph_multibar($query,$query2,$ylabel) {
    $id = "graph" . $GLOBALS['graphid'];
    $GLOBALS['graphid'] = $GLOBALS['graphid'] + 1;
 
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
        .margin({right: 60, left: 60})
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
    $count = mysql_num_rows($result);
    echo $count; 
    if($count == 0){
      echo "<h2>Insufficient data in selection to generate association matrix for this outcome</h2>";
    }else{
    echo "<h3>" . $title . "</h3>";
    echo "<table align='center'>";
    echo "<tr>";
    for($i = 0; $i < mysql_num_fields($result); $i++) {
    echo "<th class = 'rotate' ><div><span>" . mysql_field_name($result, $i). "</span></div></th>";
    }
    echo "</tr>";
    // Write rows
    mysql_data_seek($result, 0) ;
    // $compta = 0;
    while ($row = mysql_fetch_assoc($result)) {
        echo "<tr>";
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
    }
    
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

function tree($query,$global) {    
    $id = "treemap" . $GLOBALS['graphid'];
    $GLOBALS['graphid'] = $GLOBALS['graphid'] + 1;
    // Perform Query
    $result = mysql_query($query);

    // Check result
    // This shows the actual query sent to MySQL, and the error. Useful for debugging.
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }

    //Load the data
    $str = $str . "[['Race', 'Global', 'Value (size)', 'Growth (color)'],". PHP_EOL;
    $str = $str . " ['".$global."', null, 0, 0],". PHP_EOL;
    while ($row = mysql_fetch_array($result)) {
        $str = $str . " ['" . $row[0] . "', '".$global."', " . $row[1] . ", " . $row[2] ."], " . PHP_EOL;
    }
    rtrim($str, ", ");
    $str = $str . "]";


    $str2 = " <script type='text/javascript'>
    google.load('visualization', '1', {packages:['treemap']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

        var data = google.visualization.arrayToDataTable(".$str.");

        tree = new google.visualization.TreeMap(document.getElementById('".$id."'));

        tree.draw(data, {
          minColor: '#f00',
          midColor: '#ddd',
          maxColor: '#0d0',
          headerHeight: 25,
          fontColor: 'black',
          showScale: true,
          generateTooltip: showFullTooltip
        });

    function showFullTooltip(row, size, value) {
        return '<div style=\"background:#fd9; padding:10px; border-style:solid\">' +
           '<span style=\"font-family:Courier\"><b>' + data.getValue(row, 0) +
           '</b>, ' + data.getValue(row, 1) + '</span><br>' + '<br>' +
           'Number of Patients: ' + size + '<br>' +
            'Average length of stay (colour): ' + ': ' + data.getValue(row, 3) + ' </div>';
    }

      }
       </script>";
    echo $str2;
    echo PHP_EOL,"<div id= '".$id."' style='width: 500px; height: 300px;'></div>",PHP_EOL;
    $str = $str . PHP_EOL . "mycharts.push(". $id . "tree.draw)" . PHP_EOL;
}


?>
