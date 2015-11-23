-- Group 9 - Comparative Efectiveness Research

use Group9db;
-- Patient master import

Load data local infile '~/Scrivania/myproject/bgse-dashboard-project-group9/data2/Archive_3/Patient_Master.csv' 
Into table Patient_Master 
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Patient_Comorb import


Load data local infile '~/Scrivania/myproject/bgse-dashboard-project-group9/data2/Archive_3/Patient_Comorb.csv' 
Into table Patient_Comorb
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Encounter_Master import

Load data local infile '~/Scrivania/myproject/bgse-dashboard-project-group9/data2/Archive_3/Encounter_Master.csv' 
Into table Encounter_Master 
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Encounter_Outcome import

Load data local infile '~/Scrivania/myproject/bgse-dashboard-project-group9/data2/Archive_3/Encounter_Outcome.csv' 
Into table Encounter_Outcome
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Encounter_Prescription import

Load data local infile '~/Scrivania/myproject/bgse-dashboard-project-group9/data2/Archive_3/Encounter_Prescription.csv' 
Into table Encounter_Prescription
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Outcome_Meta import

Load data local infile '~/Scrivania/myproject/bgse-dashboard-project-group9/data2/Archive_3/Outcome_Meta.csv' 
Into table Outcome_Meta 
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Variable_Meta import

Load data local infile '~/Scrivania/myproject/bgse-dashboard-project-group9/data2/Archive_3/Variable_Meta.csv' 
Into table Variable_Meta 
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


Load data local infile '~/Scrivania/myproject/bgse-dashboard-project-group9/data2/Archive_3/Cohort_Patient.csv' 
Into table Cohort_Patient
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;

-- Queries for inserts into Cohort_Variable

DELIMITER $$
CREATE PROCEDURE cohort_var_ins()
BEGIN
insert into Cohort_Variable (Cohort_Pt_Key, Variable_Key) 
Select B.Cohort_Pt_Key,C.Variable_Key from
Patient_Comorb A, Cohort_Patient B, Variable_Meta C 
where A.Patient_ID = B.Patient_ID
and A.Comorb_Name  = C.Variable_Name
and C.Variable_Type = 'Comorbidity'
union
Select distinct B.Cohort_Pt_Key,C.Variable_Key from
Patient_Master A, Cohort_Patient B, Variable_Meta C, Encounter_Prescription D, Encounter_Master E
where A.Patient_ID = B.Patient_ID
and A.Patient_ID = E.Patient_ID
and E.Encounter_ID = D.Encounter_ID
and C.Variable_Name  = D.Prescription_Name
and C.Variable_Type = 'Prescription'
union
select A.Cohort_Pt_Key, B.Variable_Key from
Cohort_Patient A, Variable_Meta B
where Replace(A.Age,'[','Age.[') = B.Variable_Name
union
select A.Cohort_Pt_Key, B.Variable_Key from
Cohort_Patient A, Variable_Meta B
where A.Gender = B.Variable_Name
union
select A.Cohort_Pt_Key, B.Variable_Key from
Cohort_Patient A, Variable_Meta B
where A.Race = B.Variable_Name;
END $$
DELIMITER ;

call cohort_var_ins;


-- Query for comorbidities 
Select B.Cohort_Pt_Key,C.Variable_Key from
Patient_Comorb A, Cohort_Patient B, Variable_Meta C 
where A.Patient_ID = B.Patient_ID
and A.Comorb_Name  = C.Variable_Name
and C.Variable_Type = 'Comorbidity';


-- Query for prescriptions 
Select distinct B.Cohort_Pt_Key,C.Variable_Key from
Patient_Master A, Cohort_Patient B, Variable_Meta C, Encounter_Prescription D, Encounter_Master E
where A.Patient_ID = B.Patient_ID
and A.Patient_ID = E.Patient_ID
and E.Encounter_ID = D.Encounter_ID
and C.Variable_Name  = D.Prescription_Name
and C.Variable_Type = 'Prescription'; 

select A.Cohort_Pt_Key, B.Variable_Key from
Cohort_Patient A, Variable_Meta B
where Replace(A.Age,'[','Age.[') = B.Variable_Name;

select A.Cohort_Pt_Key, B.Variable_Key from
Cohort_Patient A, Variable_Meta B
where A.Gender = B.Variable_Name;

select A.Cohort_Pt_Key, B.Variable_Key from
Cohort_Patient A, Variable_Meta B
where A.Race = B.Variable_Name;
























-- Queries for inserts into Cohort_Outcome

DELIMITER $$
CREATE PROCEDURE cohort_out_ins()
BEGIN
insert into Cohort_Outcome (Cohort_Pt_Key, Outcome_Key, Variable_Value) 
Select A.Cohort_Pt_Key, D.Outcome_Key, sum(C.Outcome_Value) from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and replace(C.Outcome_Name,'_','.') = D.Outcome_Name
and D.Outcome_Type = 'Utilization.Sum'
group by A.Cohort_Pt_Key

union

Select A.Cohort_Pt_Key, D.Outcome_Key, sum(CASE
 WHEN C.Outcome_Value ='NO' then 0
 else 1 END) as Outcome_Value 
from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and D.Outcome_Name = 'No.Of.Readmissions'
and C.Outcome_Name = 'readmitted'
group by A.Cohort_Pt_Key

union

Select A.Cohort_Pt_Key, D.Outcome_Key, sum(CASE
 WHEN C.Outcome_Value ='<30' then 1
 else 0 END) as Outcome_Value 
from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and D.Outcome_Name = 'No.Of.Readmissions.within.30.days'
and C.Outcome_Name = 'readmitted'
group by A.Cohort_Pt_Key

union

Select A.Cohort_Pt_Key, D.Outcome_Key, avg(C.Outcome_Value) from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and replace(C.Outcome_Name,'_','.') = D.Outcome_Name
and D.Outcome_Type = 'Utilization.Avg'
group by A.Cohort_Pt_Key

union

Select A.Cohort_Pt_Key, D.Outcome_Key, avg(C.Outcome_Value) from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and replace(C.Outcome_Name,'_','.') = D.Outcome_Name
and D.Outcome_Type = 'Laboratory'
group by A.Cohort_Pt_Key;
END $$
DELIMITER ;

call cohort_out_ins;

select * from Cohort_Outcome limit 50;


Select A.Cohort_Pt_Key, D.Outcome_Key, sum(C.Outcome_Value) from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and replace(C.Outcome_Name,'_','.') = D.Outcome_Name
and D.Outcome_Type = 'Utilization.Sum'
group by A.Cohort_Pt_Key;

Select A.Cohort_Pt_Key, D.Outcome_Key, sum(CASE
 WHEN C.Outcome_Value ='NO' then 0
 else 1 END) as Outcome_Value 
from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and D.Outcome_Name = 'No.Of.Readmissions'
and C.Outcome_Name = 'readmitted'
group by A.Cohort_Pt_Key;

Select A.Cohort_Pt_Key, D.Outcome_Key, sum(CASE
 WHEN C.Outcome_Value ='<30' then 1
 else 0 END) as Outcome_Value 
from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and D.Outcome_Name = 'No.Of.Readmissions.within.30.days'
and C.Outcome_Name = 'readmitted'
group by A.Cohort_Pt_Key;

Select A.Cohort_Pt_Key, D.Outcome_Key, avg(C.Outcome_Value) from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and replace(C.Outcome_Name,'_','.') = D.Outcome_Name
and D.Outcome_Type = 'Utilization.Avg'
group by A.Cohort_Pt_Key;

Select A.Cohort_Pt_Key, D.Outcome_Key, avg(C.Outcome_Value) from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and replace(C.Outcome_Name,'_','.') = D.Outcome_Name
and D.Outcome_Type = 'Laboratory'
group by A.Cohort_Pt_Key;
