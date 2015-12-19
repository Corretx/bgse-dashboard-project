<title>Comparative Effectiveness Research</title>
<link rel="stylesheet" type="text/css" href="skeleton.css" />
<script src="files/display.js" type="text/javascript"></script>

<div id="header"><h1>Comparative Effectiveness Research</h1></div>

<div id="menu">
    <a id="home_link" href="index.php"  onclick="show_content('home'); return false;">Home</a> &middot;
    <a id="data_link" href="data.php" class="active" onclick="show_content('data'); update_data_charts(); return false;">Data</a> &middot;
    <a id="analysis_link" href="analysis.php" onclick="show_content('analysis'); return false;">Analysis</a>
</div>

<?php
	include 'functions.php';
	$GLOBALS['graphid'] = 0;

    // Load libraries
    document_header();

	// Create connection
	$link = connect_to_db();
?>

	<!-- Create the filters -->
	<form name="search" method="POST" action = "data.php" align = "center">
    
        <select name="gender">
            <option value="">Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
        </select>
        
        <select name= "race" >
            <option value="">Race</option>
            	<option value="Caucasian">Caucasian</option>            
                <option value="AfricanAmerican">AfricanAmerican</option>
                 <option value="Asian">Asian</option>
                <option value="Hispanic">Hispanic</option>
                <option value="Other">Other</option>
        </select>  

        <select name="min_age">
            <option value="">Min. age</option>
                <option value="[0-10)">[0-10)</option>
                <option value="[10-20)">[10-20)</option>
                <option value="[20-30)">[20-30)</option>
                <option value="[30-40)">[30-40)</option>
                <option value="[40-50)">[40-50)</option>
                <option value="[50-60)">[50-60)</option>
                <option value="[60-70)">[60-70)</option>
                <option value="[70-80)">[70-80)</option>
                <option value="[80-90)">[80-90)</option>
                <option value="[90-100)">[90-100)</option>
        </select> 

        <select name="max_age">
            <option value="">Max. age</option>
                <option value="[0-10)">[0-10)</option>
                <option value="[10-20)">[10-20)</option>
                <option value="[20-30)">[20-30)</option>
                <option value="[30-40)">[30-40)</option>
                <option value="[40-50)">[40-50)</option>
                <option value="[50-60)">[50-60)</option>
                <option value="[60-70)">[60-70)</option>
                <option value="[70-80)">[70-80)</option>
                <option value="[80-90)">[80-90)</option>
                <option value="[90-100)">[90-100)</option>
        </select> 
        
        <input type="submit" name="submit" value="Apply">
    </form>


<?php
    //Apply filters
    if (isset($_POST['submit'])) {
            $count = 0;
            $where = "";
            if (!empty($_POST['gender'])) {
                $search_term = mysql_real_escape_string($_POST['gender']);
                $where .= " Gender = '{$search_term}'";
                $count++;
            }
            if (!empty($_POST['race'])) {
                if ($count > 0) $where .= " and "; 
                $search_term = mysql_real_escape_string($_POST['race']);
                $where .= " Race = '{$search_term}'";
                $count++;
            }
            if (!empty($_POST['min_age'])) {
                if ($count > 0) $where .= " and "; 
                $search_term = mysql_real_escape_string($_POST['min_age']);
                $where .= " Age >= '{$search_term}'";
                $count++;
            }
            if (!empty($_POST['max_age'])) {
                if ($count > 0) $where .= " and "; 
                $search_term = mysql_real_escape_string($_POST['max_age']);
                $where .= " Age <= '{$search_term}'";
                $count++;
            }
            if ($count == 0){
                $where .= " 1 = 1";
            }
    }
?>

<?php
    $query1 = "DROP TABLE IF EXISTS Group9db.Population_in_Selection";
    if (isset($_POST['submit'])) {
        $query = "Create table Group9db.Population_in_Selection as Select * from Group9db.Cohort_Patient Where ";
        $query .= $where;
    }
    else $query = "Create table Group9db.Population_in_Selection as Select * from Group9db.Cohort_Patient";
    create_table($query1,$query);
?>

<?php
    $query1 = "select count(*) from Group9db.Cohort_Patient";
    $query2 = "Select count(*) from Group9db.Population_in_Selection";
    table_data($query1, $query2);
?>

<?php
    // Patients for Novolog
    
    $query1 = "Select Age, count(Gender) as Gender From Group9db.Cohort_Patient Where Drug_Name = 'Novolog' and Gender = 'female'";    
    $query2 = "Select Age, count(Gender) as Gender From Group9db.Cohort_Patient Where Drug_Name = 'Novolog' and Gender = 'male'";
    if (isset($_POST['submit'])) {
        $query1 .= " and ".$where;
        $query2 .= " and ".$where;
    }
    $query1 .= " Group by Age";
    $query2 .= " Group by Age";
    $title = "Age & Gender distribution for Novolog";
    query_and_print_graph_multibar($query1,$query2,$title,"Patients");
?>

<?php
    // Patients for Humalog
    
    $query1 = "Select Age, count(Gender) as Gender From Group9db.Cohort_Patient Where Drug_Name = 'Humalog' and Gender = 'female'";    
    $query2 = "Select Age, count(Gender) as Gender From Group9db.Cohort_Patient Where Drug_Name = 'Humalog' and Gender = 'male'";
    if (isset($_POST['submit'])) {
        $query1 .= " and ".$where;
        $query2 .= " and ".$where;
    } 
    $query1 .= " Group by Age";
    $query2 .= " Group by Age";
    $title = "Age & Gender distribution for Humalog";
    query_and_print_graph_multibar($query1,$query2,$title,"Patients");
?>

<?php

    $query = "Select Count(Age), Race, Count(Age) from Group9db.Population_in_Selection";
    $title = "Tree map";
   // treemap($query,$title,"hola");
?>

<?php
    $query = "Select Race, Count(*) from Group9db.Population_in_Selection group by Race";
    $title = "Tree map";
    $result = treemapdata($query,$title,"hola");
    // treemap($query,$title,"hola");
?>

<script>

var sample_data = <?php echo $result ; ?>

var chart = d3plus.viz()
        .container("#viz")
        .data(sample_data)
        .type("tree_map")
        .id("name")
        .size("value")
        .color("growth")
        .draw()

</script>


<?php
	// Close connection
	mysql_close($link);
?>
