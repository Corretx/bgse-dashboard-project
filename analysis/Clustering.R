lib.loc <- '/home/ubuntu/projects/Rlibs'
require(reshape2,lib.loc)
require(plyr,lib.loc)
require(RMySQL,lib.loc)

#Connect to the Database
mydb = dbConnect(MySQL(), user='root', password='root' , dbname='Group9db')

#Fetch Patient data from the Cohort_Patient table
rs = dbSendQuery(mydb, "select * from Population_in_Selection")
cohort_pt = fetch(rs, n=-1)
cohort_pt = cohort_pt[,c("Cohort_Pt_Key","Drug_Name")]

#Fetch Variable data from the Cohort_Variable table
rs = dbSendQuery(mydb, "select * from Cohort_Variable")
cohort_var = fetch(rs, n=-1)
cohort_var$Variable_Key <- paste("VRBL_",cohort_var$Variable_Key,sep="")
cohort_var$Value <- 1
var_cast = dcast(cohort_var[-1],Cohort_Pt_Key ~ Variable_Key, fill = 0 ,value.var = "Value")
var_cast = merge(cohort_pt,var_cast)

#Fetch Outcome data from the Cohort_Outcome table
rs = dbSendQuery(mydb, "select * from Cohort_Outcome")
cohort_out = fetch(rs, n=-1)
cohort_out$Outcome_Key <- paste("OTCME_",cohort_out$Outcome_Key,sep="")
out_cast = dcast(cohort_out[-1],Cohort_Pt_Key ~ Outcome_Key, fill = 0 ,value.var = "Outcome_Value")
out_cast = merge(cohort_pt,out_cast)

model.data = merge(var_cast,out_cast)
model.data = model.data[,-1]

rs = dbSendQuery(mydb, "select Variable_Key,Outcome_Key from Var_Out_Assoc order by Outcome_Key,Rank")
Assoc_table = fetch(rs, n=-1)

Assoc_table$Variable_Key <- paste("VRBL_",Assoc_table$Variable_Key,sep="")
Assoc_table$Outcome_Key <- paste("OTCME_",Assoc_table$Outcome_Key,sep="")

rs = dbSendQuery(mydb, "select Variable_Key,Variable_Name from Variable_Meta")
variable_meta = fetch(rs, n=-1)
variable_meta$Variable_Key <- paste("VRBL_",variable_meta$Variable_Key,sep="")

model.data$OTCME_108[model.data$OTCME_108 == 0] <- NA
model.data$OTCME_108[model.data$OTCME_108 == 'Norm'] <- 0
model.data$OTCME_108[model.data$OTCME_108 == '>200'] <- 1
model.data$OTCME_108[model.data$OTCME_108 == '>300'] <- 1

model.data$OTCME_109[model.data$OTCME_109 == 0] <- NA
model.data$OTCME_109[model.data$OTCME_109 == 'Norm'] <- 0
model.data$OTCME_109[model.data$OTCME_109 == '>7'] <- 1
model.data$OTCME_109[model.data$OTCME_109 == '>8'] <- 1

var_list = colnames(var_cast[,-c(1,2)])
out_list = colnames(out_cast[,-c(1,2)])
nvar = ncol(var_cast) - 2
nout = ncol(out_cast) - 2

limit <- c(4,rep(0,8))
#Iterations to generate the association matrix for each outcome
for(j in 1:nout){
  variables <- Assoc_table$Variable_Key[Assoc_table$Outcome_Key == out_list[j]]
  columns <- c("Drug_Name",variables,out_list[j])
  clusterdata <- model.data[,columns]
  colnames(clusterdata)[12] <- 'outcome'
  clusterdata$outcome <- as.numeric(clusterdata$outcome)
    
  colnames(clusterdata)[2:11] = merge(data.frame(Variable_Key = colnames(clusterdata)[2:11]),variable_meta,sort=FALSE)$Variable_Name
  
  clusters <- ddply(clusterdata,1:11,summarize,count = length(Drug_Name),highcount = length(Drug_Name[which(outcome>limit[j])]))
  
  clusters$Humalog <- 0
  clusters$Novolog <- 0
  clusters$Humalog[clusters$Drug_Name == 'Humalog'] <- 1
  clusters$Novolog[clusters$Drug_Name == 'Novolog'] <- 1
  clusters$Drug_Name <- NULL
  clusters$n_hum <- clusters$Humalog*clusters$count
  clusters$n_nov <- clusters$Novolog*clusters$count
  clusters$n_hum_high <- clusters$Humalog*clusters$highcount
  clusters$n_nov_high <- clusters$Novolog*clusters$highcount
  clusters$count <- NULL
  clusters$highcount <- NULL
  
  final <- ddply(clusters,1:10,summarise,n_hum = sum(n_hum),n_nov = sum(n_nov),n_hum_high = sum(n_hum_high),n_nov_high=sum(n_nov_high))
  final <- final[which(final$n_hum > 9 & final$n_nov > 9 & final$n_hum_high > 1 & final$n_nov_high > 1),]
  
  N <- nrow(final)
  final$chi.squared <- NA
  final$p_value <- NA
  for(i in 1:N){
    M <- matrix(c(a=final$n_hum[i] - final$n_hum_high[i],b=final$n_nov[i] - final$n_nov_high[i],c=final$n_hum_high[i],d=final$n_nov_high[i]),ncol=2)
    Xsq <- chisq.test(M)
    final$chi.squared[i] <- round(Xsq$statistic,4)
    final$p_value[i] <- round(Xsq$p.value,4)
  }
  
  final$Significance <- "Non-Significant"
  final$Significance[which(abs(final$p_value) < 0.05)] <- "Significant"
  
  final <- final[order(final$p_value),]
  final <- final[-which(final$p_value > 0.9),]
  
  if(nrow(final) >0){
    dbWriteTable(mydb, paste("Matrix",out_list[j],sep="_"), final,overwrite=TRUE,row.names=FALSE)
  }
  
}
