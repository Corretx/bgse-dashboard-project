-- Group 9 - Comparative Efectiveness Research

use Group9db;
-- Patient master import

Load data local infile 'data/Patient_Master.csv' 
Into table Patient_Master 
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Patient_Comorb import


Load data local infile 'data/Patient_Comorb.csv' 
Into table Patient_Comorb
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Encounter_Master import

Load data local infile 'data/Encounter_Master.csv' 
Into table Encounter_Master 
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Encounter_Outcome import

Load data local infile 'data/Encounter_Outcome.csv' 
Into table Encounter_Outcome
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Encounter_Prescription import

Load data local infile 'data/Encounter_Prescription.csv' 
Into table Encounter_Prescription
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Outcome_Meta import

Load data local infile 'data/Outcome_Meta.csv' 
Into table Outcome_Meta 
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


-- Variable_Meta import

Load data local infile 'data/Variable_Meta.csv' 
Into table Variable_Meta 
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;


Load data local infile 'data/Cohort_Patient.csv' 
Into table Cohort_Patient
Fields terminated by ',' 
Enclosed by '"'
Lines terminated by '\n'
Ignore 1 rows;