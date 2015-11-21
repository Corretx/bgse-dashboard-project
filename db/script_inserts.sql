-- Group 9 - Comparative Efectiveness Research


-- Patient master import

Load data local infile '~/Documents/Csv/Patient_Master.csv' 
Into table Patient_Master 
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Patient_Comorb import

Load data local infile '~/Documents/Csv/Patient_Comorb.csv' 
Into table Patient_Comorb
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Encounter_Master import

Load data local infile '~/Documents/Csv/Encounter_Master.csv' 
Into table Encounter_Master 
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Encounter_Outcome import

Load data local infile '~/Documents/Csv/Encounter_Outcome.csv' 
Into table Encounter_Outcome
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Encounter_Prescription import

Load data local infile '~/Documents/Csv/Encounter_Prescription.csv' 
Into table Encounter_Prescription
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Outcome_Meta import

Load data local infile '~/Documents/Csv/Outcome_Meta.csv' 
Into table Outcome_Meta 
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Variable_Meta import

Load data local infile '~/Documents/Csv/Variable_Meta.csv' 
Into table Variable_Meta 
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;






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

