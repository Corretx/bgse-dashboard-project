library(glmnet)
library(reshape2)
library(plyr)
library(RMySQL)

#Connect to the Database
mydb = dbConnect(MySQL(), user='root', password='root', dbname='Group9db')

#Fetch Patient data from the Cohort_Patient table
rs = dbSendQuery(mydb, "select * from Cohort_Patient")
cohort_pt = fetch(rs, n=-1)
cohort_pt = cohort_pt[,c("Cohort_Pt_Key","Drug_Name")]

#Fetch Variable data from the Cohort_Variable table
rs = dbSendQuery(mydb, "select * from Cohort_Variable")
cohort_var = fetch(rs, n=-1)
cohort_var$Variable_Key <- paste("VRBL_",cohort_var$Variable_Key,sep="")
cohort_var$Value <- 1
var_cast = dcast(cohort_var[-1],Cohort_Pt_Key ~ Variable_Key, fill = 0 ,value.var = "Value")

#Fetch Outcome data from the Cohort_Outcome table
rs = dbSendQuery(mydb, "select * from Cohort_Outcome")
cohort_out = fetch(rs, n=-1)
cohort_out$Outcome_Key <- paste("OTCME_",cohort_out$Outcome_Key,sep="")
out_cast = dcast(cohort_out[-1],Cohort_Pt_Key ~ Outcome_Key, fill = 0 ,value.var = "Outcome_Value")

model.data = merge(var_cast,out_cast)
model.data = model.data[,-1]

var_list = colnames(var_cast[-1])
out_list = colnames(out_cast[-1])
nvar = ncol(var_cast) - 1
nout = ncol(out_cast) - 1

Assoc_table = data.frame()

family = c(rep("poisson",6),rep("binomial",3))

model.data$OTCME_108[model.data$OTCME_108 == 0] <- NA
model.data$OTCME_108[model.data$OTCME_108 == '>200'] <- 'High'
model.data$OTCME_108[model.data$OTCME_108 == '>300'] <- 'High'

model.data$OTCME_109[model.data$OTCME_109 == 0] <- NA
model.data$OTCME_109[model.data$OTCME_109 == '>7'] <- 'High'
model.data$OTCME_109[model.data$OTCME_109 == '>8'] <- 'High'

#Iterate over each outcome to calculate the top 10 significant variables associated with them
for(i in 1:nout){
  outcome_key = out_list[i]
  
  x <- as.matrix(model.data[,c(1:nvar)])
  ifelse(i < 7,y <- as.numeric(model.data[,nvar + i]), y <- as.factor(model.data[,nvar + i])) 
  
  x <- x[is.finite(y),]
  y <- y[is.finite(y)]

  #Fit the Lasso Regression model
  lasso.model <- glmnet(x,y,family=family[i],alpha=1,dfmax=10)
  s = min(lasso.model$lambda)
  #Extract the coefficients
  coefficients = data.frame(as.matrix(coef(lasso.model,s)))
  coefficients = data.frame(Variable = row.names(coefficients), Coef = coefficients$X1)
  #Remove the intecept
  coefficients = coefficients[-1,]
  #Extract the Non-zero-coefficients
  coefficients = coefficients[which(coefficients$Coef != 0),]
  #Calculate the ranks
  coefficients$Rank = nrow(coefficients) + 1 - rank(abs(coefficients$Coef),ties.method = "random")
  coefficients = coefficients[which(coefficients$Rank < 11),]
  coefficients$Coef <- NULL
  coefficients$Outcome_Key <- substr(outcome_key,7,100)
  coefficients$Variable_Key <- substr(coefficients$Variable, 6, 100)
  coefficients$Variable <- NULL
  coefficients$Var_Out_Assoc_Key <- (10*(i-1)+1):(10*i)
  coefficients = coefficients[c(4,3,2,1)]
  
  Assoc_table = rbind(Assoc_table,coefficients)
}
#Write the Variable Outcome Association data into the database
dbWriteTable(mydb, "Var_Out_Assoc", Assoc_table,overwrite=TRUE)
