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
    // Total Revenue by product
    
    $query = "SELECT ProductName, Revenue FROM ecommerce.ProductsVsCustomers_Pivot ORDER BY Revenue DESC limit 10";
    $title = "Products by revenues";
    query_and_print_graph($query,$title,"Euros");
?>


<?php
    // Total Revenue by product
    
    $query = "Select age,gender from Group9db.Age_Gender";
    $title = "Distribution by age";
    query_and_print_graph($query,$title,"People");
?>


<?php
	// Close connection
	mysql_close($link);
?>
