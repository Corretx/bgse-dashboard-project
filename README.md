# Comparative Effectiveness Research

### Overview

The aim of the project is to inform health-care decisions by providing evidence on the effectiveness, benefits, and
harms of different treatment options.

In particular, the target users of the dashboard are life-science researchers who want to see the impact of different
drugs to treat patients belonging to a specific therapeutic area. 

The overarching goal is to provide better evidence to inform decisions made by health-care providers

### Structure

The core of the analysis is contained in these files:

- `AttributeImportance.R`
- `Cohorts.sql`
- `Cohort_Balancing.R`
- `data.php`
- `analysis.php`


### Implementation

To develop the product recommendation system we have used the Apriori algorithm. We provide a link to the relevant Wikipedia article on the introductory page of the web application.

To develop the LASSO regression, we first have identified the top 20 customers in terms of total revenues generated. Then we have carried out a LASSO regression using the `lars` package with the objective to narrow down the number of customers with a significant marginal contribution to revenues. 

The 'Data' tab includes a network graph of the links between product categories. Note that the graph is generated dynamically each time the `./setup.sh run` command is given. The script saves a `.png` file in the `/web` sub-directory, which is then retrieved via `html` link.

### Required packages

The `R` analysis relies on the following packages. 

- `glmnet`
- `reshape2`
- `plyr`
- `RMySQL`

## Acknowledgments

This project is based on code by: Guglielmo Bartolozzi, Christian Brownlees, Anna Corretger, Santhosh Narayanan, Guglielmo Pelino
