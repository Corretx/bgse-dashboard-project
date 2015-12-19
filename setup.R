# Install R packages required

if("RMySQL" %in% rownames(installed.packages()) == FALSE) {install.packages("RMySQL", repos="http://cran.r-project.org", lib="/home/ubuntu/projects/Rlibs/")}
if("reshape2" %in% rownames(installed.packages()) == FALSE) {install.packages("reshape2", repos="http://cran.r-project.org", lib="/home/ubuntu/projects/Rlibs/")}
if("plyr" %in% rownames(installed.packages()) == FALSE) {install.packages("plyr", repos="http://cran.r-project.org", lib="/home/ubuntu/projects/Rlibs/")}
if("glmnet" %in% rownames(installed.packages()) == FALSE) {install.packages("glmnet", repos="http://cran.r-project.org", lib="/home/ubuntu/projects/Rlibs/")}

