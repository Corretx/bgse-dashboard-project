<title>Comparative Effectiveness Research</title>
<link rel="stylesheet" type="text/css" href="skeleton.css" />
<script src="files/display.js" type="text/javascript"></script>

<div id="header"><h1>Comparative Effectiveness Research</h1></div>

<div id="menu">
    <a id="home_link" href="index.php" class="active" onclick="show_content('home'); return false;">Home</a> &middot;
    <a id="data_link" href="data.php" onclick="show_content('data'); return false;">Data</a> &middot;
    <a id="analysis_link" href="analysis.php" onclick="show_content('analysis'); return false;">Analysis</a>
</div>

<div id="main">
<h3>Comparative Effectiveness Research:</h3>
<p>Designed to inform health-care decisions by providing evidence on the effectiveness, benefits, and harms of different treatment options. For our analysis we compare two different treatment options for diabetes patients and
study their outcomes.</p>
<ol>We follow the following steps perform this analysis.
  <li>Identify our population of interest.</li>
  <li>Separate into two cohorts based on the drugs of interest.</li>
  <li>Develop a logistic regression model to calculate propensity scores for treatment.</li>
  <li>Perform balancing of cohorts using propensity score matching.</li>
  <li>Rank variables in terms of their importance to the outcome using a lasso model.</li>
  <li>Create an association matrix to identify the clusters that show the highest statistically
significant differences in the outcome.</li>
</ol>

<h4>Data Tab: Population Overview</h4>
<p>The Population Overview page provides an overview of the population of interest, displaying the age/gender 
distribution of the cohort and allows you to filter based on the selected population.</p>
<ol>The Population Overview tab is divided into four sections:
    <li>Age and gender distribution by drug.</li>
    <li>Drug of interest tree map - Indicates the frequencies of patients for each drug in the
selected patient population.</li>
    <li>The Comorbidities tree map - Indicates the frequencies of patients for coexisting medical
conditions in the selected patient population.</li>
    <li>The Concurrent Prescriptions tree map - Indicates the frequencies of patients prescribed
drugs from the particular prescription category in the selected patient population.</li>
</ol>

<h4>Analysis Tab: Suggested Associations</h4>
<p>The Suggested Association tab allows you to identify variables that may have a significant impact on the 
outcome selected under the conditions specified by confidence interval and test measure for the filtered population set.</p>
<p>The Association matrix is created using the top 10 variables having the highest impact
on the outcome using a lasso model and using them as clustering variables.
Now you can identify the clusters that show the highest statistically significant differences
in the outcome using an appropriate test measure.</p>
</div>
