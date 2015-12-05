<?php
	include 'functions.php';
	$GLOBALS['graphid'] = 0;
	// Load libraries
	document_header();
	// Create connection
	$link = connect_to_db();
?>
	<div id="data" style="display: none">
	
	<h2>Data</h2>
	
	<p>In this section we carry out an initial analysis of past transactions, with the objective of gathering information about the categories, products and customers that tend to generate the highest revenues. The results shown in this page can provide insights to inform the activities of the sales team. This information, together with the recommendation system and customer analysis which we have implemented in the next page, can support the activities of the company's marketing team.</p>
	
	<p> The chart below shows the best selling products ranked according to the revenues they generate. Only the top 10 best selling products are shown.</p>

<?php
    // Patients for novolog
    
    $query1 = "Select age,gender from Group9db.Novolog_female";
    $query2 = "Select age,gender from Group9db.Novolog_male";
    $title = "Novolog patients";
    query_and_print_graph_multibar($query1,$query2,$title,"Patients");
?>

<?php
    // Patients for Humalog
    
    $query = "Select age,gender from Group9db.Humalog_female";
    $query2 = "Select age,gender from Group9db.Humalog_male";
    $title = "Humalog patients";
    query_and_print_graph_multibar($query,$query2,$title,"Patients");
?>


	</div>
	
	<div id="analysis" style="display: none">
	<h2>Analysis</h2>
	
	<p>Below we show the top 20 product recommendation rules identified by the <b>Apriori algorithm</b>. The table can be read as follows: for each rule, the left-hand side shows a potential basket that the customer has put together, while the right-hand side shows the additional product that could be purchased to "complete that basket".</p>

	<p>For example, the first rule indicates that a customer that has already added dried applies and sild (herring) to her basket, would be recommended gorgonzola cheese <em>(note: it sounds disgusting but the customer is always right!)</em> The recommendations are based on the analysis of historical transaction already stored in the database.</p>
	</div>		
<?php
	// Close connection
	mysql_close($link);
?>
