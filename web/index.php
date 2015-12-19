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

</div>
