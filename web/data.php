<title>Comparative Effectiveness Research</title>
<link rel="stylesheet" type="text/css" href="skeleton.css" />
<script src="files/display.js" type="text/javascript"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>

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
    
    $query1 = "Select Age, count(*) as Gender From Group9db.Cohort_Patient Where Drug_Name = 'Novolog' and Gender = 'female'";    
    $query2 = "Select Age, count(*) as Gender From Group9db.Cohort_Patient Where Drug_Name = 'Novolog' and Gender = 'male'";
    if (isset($_POST['submit'])) {
        $query1 .= " and ".$where;
        $query2 .= " and ".$where;
    }
    $query1 .= " Group by Age";
    $query2 .= " Group by Age";
    // query_and_print_graph_multibar($query1,$query2,$title,"Patients");
?>

<?php
    // Patients for Humalog
    
    $query3 = "Select Age, count(*) as Gender From Group9db.Cohort_Patient Where Drug_Name = 'Humalog' and Gender = 'female'";    
    $query4 = "Select Age, count(*) as Gender From Group9db.Cohort_Patient Where Drug_Name = 'Humalog' and Gender = 'male'";
    if (isset($_POST['submit'])) {
        $query3 .= " and ".$where;
        $query4 .= " and ".$where;
    } 
    $query3 .= " Group by Age";
    $query4 .= " Group by Age";
    // query_and_print_graph_multibar($query1,$query2,$title,"Patients");
?>
<h3>Age & Gender distribution</h3>
<table align='center'>
    <th align='center' border-bottom = '0'> Humalog</th>
    <th align='center' border-bottom = '0'> Novolog</th>
<tr>
    <td align='center' border-bottom = '0'> <?php query_and_print_graph_multibar($query1,$query2,"Patients"); ?></td>
    <td align='center' border-bottom = '0'> <?php query_and_print_graph_multibar($query3,$query4,"Patients"); ?></td>
</tr>
</table>

<h3>Treemaps</h3>
<p><em><b>Size of node:</b> Proportional to the no. of patients</em></p>
<p><em><b>Colour of node:</b> Average length of stay</em></p>

<?php
    $query1 = "Select Drug_Name, Count(*), round(avg(Outcome_Value),4) from Group9db.Population_in_Selection A, Group9db.Cohort_Outcome B 
    where A.Cohort_Pt_Key = B.Cohort_Pt_Key and B.Outcome_Key = 101
    group by Drug_Name";
    $title1 = "Drug";
    $query2 = "Select Race, Count(*), round(avg(Outcome_Value),4) from Group9db.Population_in_Selection A, Group9db.Cohort_Outcome B
    where A.Cohort_Pt_Key = B.Cohort_Pt_Key and B.Outcome_Key = 101
    group by Race";
    $title2 = "Race";
    $query3 = "Select Variable_Name, Count(*), round(avg(Outcome_Value),4) from Group9db.Population_in_Selection A, Group9db.Cohort_Outcome B,
    Group9db.Cohort_Variable C, Group9db.Variable_Meta D
    where A.Cohort_Pt_Key = B.Cohort_Pt_Key 
    and C.Cohort_Pt_Key = A.Cohort_Pt_Key
    and B.Outcome_Key = 101
    and D.Variable_Type = 'Comorbidity'
    and C.Variable_Key = D.Variable_Key
    group by Variable_Name";
    $title3 = "Comorbities";
    $query4 = "Select Variable_Name, Count(*), round(avg(Outcome_Value),4) from Group9db.Population_in_Selection A, Group9db.Cohort_Outcome B,
    Group9db.Cohort_Variable C, Group9db.Variable_Meta D
    where A.Cohort_Pt_Key = B.Cohort_Pt_Key 
    and C.Cohort_Pt_Key = A.Cohort_Pt_Key
    and B.Outcome_Key = 101
    and D.Variable_Type = 'Prescription'
    and C.Variable_Key = D.Variable_Key
    group by Variable_Name";
    $title4 = "Prescriptions";
 ?>

<table align='center' border = '0'>
<tr>
    <td align='center' border-bottom = '0'> <?php tree($query1,$title1); ?></td>
    <td align='center' border-bottom = '0'> <?php tree($query2,$title2); ?></td>
</tr>
<tr>
    <td align='center' border-bottom = '0'> <?php tree($query3,$title3); ?></td>
    <td align='center' border-bottom = '0'> <?php tree($query4,$title4); ?></td>
</tr>
</table>

<?php
   $rEngine = "/usr/local/bin/Rscript --vanilla ";
   $rScript = "~/Documents/MyApp/analysis/Clustering.R";

   $cmd = sprintf("%s %s", $rEngine, $rScript); #  >&1 2>&1
   $result = system($cmd);
?>

<?php
	// Close connection
	mysql_close($link);
?>
