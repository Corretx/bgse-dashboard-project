library(optmatch)
library(MatchIt)
library(RMySQL)
library(cowplot)
library(plyr)

#Connection to SQL
mydb = dbConnect(MySQL(), user='root', password='root' , dbname='Group9db')

#Read the data from the data base (Patient Master)
rs = dbSendQuery(mydb, "select * from Patient_Master")
patient_data = fetch(rs, n=-1)

#Re-code the Drug Variable
attach(patient_data)
patient_data$drug[Drug_Name == 'Humalog'] <- 0
patient_data$drug[Drug_Name == 'Novolog'] <- 1
patient_data$Drug_Name <- NULL

#Convert the categorical covariates to factors for the regression
patient_data$Age <- as.factor(as.character(patient_data$Age))
patient_data$Gender <- as.factor(as.character(patient_data$Gender))
patient_data$Race <- as.factor(as.character(patient_data$Race))

#Fit a logistic regression to calculate propesity score and perform the matching
m.out <- matchit(drug ~ Age + Gender + Race, data = patient_data, method = "nearest", distance = "logit")

#Extract the matched pairs of records from the previous output
m.data <- match.data(m.out,distance ="pscore")

#Re-code the Drug Variable and create the data-frame for table insert
matched.data <- m.data[,1:5]
matched.data$Drug_Name[matched.data$drug == 0] <- 'Humalog'
matched.data$Drug_Name[matched.data$drug == 1] <- 'Novolog'
matched.data$drug <- NULL
matched.data$Cohort_Pt_Key <- 1:nrow(matched.data)
matched.data <- matched.data[c(6,1,5,2,3,4)]

rs = dbSendQuery(mydb, "select * from Patient_Master")
before = fetch(rs, n=-1)
after <- read.csv("Cohort_Patient.csv")[,-1]

#Populate the Cohort_Patient table with the matched patients forming the cohort
dbWriteTable(mydb, "Cohort_Patient", matched.data,overwrite=TRUE)