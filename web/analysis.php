<title>Comparative Effectiveness Research</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<script src="files/display.js" type="text/javascript"></script>
<<<<<<< HEAD
=======
<script src='files/jquery-1.11.3.js' type='text/javascript' ></script>
>>>>>>> 1fb9a529f5362152c4697bc36225d771a5ecfbc1

<div id="header"><h1>Comparative Effectiveness Research</h1></div>


<div id="menu">
    <a id="home_link" href="index.php"  onclick="show_content('home'); return false;">Home</a> &middot;
    <a id="data_link" href="data.php" onclick="show_content('data'); return false;">Data</a> &middot;
    <a id="analysis_link" href="analysis.php" class="active"  onclick="show_content('analysis'); update_data_charts(); return false;">Analysis</a> &middot;
</div>

<?php
	include 'functions.php';
	$GLOBALS['graphid'] = 0;

    // Load libraries
    document_header();

	// Create connection
	$link = connect_to_db();
?>


    <form name="search" method="POST" action = "analysis.php">

        <select name="outcome">
            <option value="">Outcome</option>
                <option value="101">Length.Of.Stay</option>
                <option value="102">No.of.Outpatient.Visits</option>
                <option value="103">No.of.Inpatient.Visits</option>
                <option value="104">No.of.Emergency.Visits</option>
                <option value="105">No.Of.Readmissions</option>
                <option value="106">No.Of.Readmissions.within.30.days</option>
                <option value="107">Mortality.Rate</option>
                <option value="108">Max.Serum.Glucose</option>
                <option value="109">HbA1c</option>
        </select> 
        
        <input type="submit" name="submit" value="Apply">
    </form>

 <?php
   $rEngine = "/usr/local/bin/Rscript --vanilla ";
   $rScript = "~/Documents/MyApp/analysis/Clustering.R";

   $cmd = sprintf("%s %s", $rEngine, $rScript); #  >&1 2>&1
   $result = system($cmd);
?>

<<<<<<< HEAD

=======
>>>>>>> 1fb9a529f5362152c4697bc36225d771a5ecfbc1
<?php
    // Patients for Humalog
    
    $query = "Select * From Group9db.Matrix_OTCME_";
    if (isset($_POST['submit']) && !empty($_POST['outcome'])) {
            $search_term = mysql_real_escape_string($_POST['outcome']);
            $query .= "{$search_term}";
    }
    else $query .= "101";
    $title = "Cluster";
<<<<<<< HEAD
    query_and_print_table2($query,$title);
=======
    cluster_table($query,$title);
>>>>>>> 1fb9a529f5362152c4697bc36225d771a5ecfbc1
?>

<?php
	// Close connection
	mysql_close($link);
?>
