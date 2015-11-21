-- Group 9 - Comparative Efectiveness Research


-- create the database

DROP DATABASE Group9db;
CREATE DATABASE Group9db;

-- select the database

USE Group9db;

-- create the required tables

CREATE TABLE Patient_Master (

Patient_ID INT(11) NOT NULL,
Drug_Name NVARCHAR(10) NOT NULL,
Age NVARCHAR(10) NOT NULL,
Gender NVARCHAR(10) NOT NULL,
Race  NVARCHAR(20)NOT NULL,

PRIMARY KEY (Patient_ID)

); 


CREATE TABLE Patient_Comorb (

Pt_Comorb_Key NVARCHAR(8) NOT NULL,
Patient_ID INT(11) NOT NULL,
Comorb_Name NVARCHAR(30) NOT NULL,

PRIMARY KEY (Pt_Comorb_Key)

);


CREATE TABLE Encounter_Master (

Encounter_ID INT(11) NOT NULL,
Patient_ID INT(11) NOT NULL,
Admission_Type NVARCHAR(20),
Discharge_Type NVARCHAR(10),
Readmit_Type NVARCHAR(10),

PRIMARY KEY (Encounter_ID)

);


CREATE TABLE Encounter_Prescription (

Enc_Pres_Key INT(11) NOT NULL,
Encounter_ID INT(11) NOT NULL,
Prescription_Name NVARCHAR(30) NOT NULL,

PRIMARY KEY (Enc_Pres_Key)

);


CREATE TABLE Encounter_Outcome (

Enc_Outcome_Key INT(11) NOT NULL,
Encounter_ID INT(11) NOT NULL,
Outcome_Name NVARCHAR(30) NOT NULL,
Outcome_Value NVARCHAR(10) NOT NULL,

PRIMARY KEY (Enc_Outcome_Key)

);


CREATE TABLE Cohort_Patient (

Cohort_Pt_Key INT(11) NOT NULL,
Patient_ID INT(11) NOT NULL,
Drug_Name NVARCHAR(10) NOT NULL,
Age NVARCHAR(10) NOT NULL,
Gender NVARCHAR(10) NOT NULL,
Race  NVARCHAR(20)NOT NULL,

PRIMARY KEY (Cohort_Pt_Key)

);


CREATE TABLE Cohort_Variable (

Cohort_Variable_Key INT(11) NOT NULL,
Cohort_Pt_Key INT(11) NOT NULL,
Variable_Key INT(11) NOT NULL,

PRIMARY KEY (Cohort_Variable_Key)

);


CREATE TABLE Cohort_Outcome (

Cohort_Outcome_Key INT(11) NOT NULL,
Cohort_Pt_Key INT(11) NOT NULL,
Outcome_Key INT(11) NOT NULL,
Variable_Value NVARCHAR(10) NOT NULL,

PRIMARY KEY (Cohort_Outcome_Key)

);


CREATE TABLE Var_Out_Assoc(

Var_Out_Assoc_Key INT(11) NOT NULL,
Variable_Key INT(11) NOT NULL,
Outcome_Key INT(11) NOT NULL,
Rank INT(11) NOT NULL,

PRIMARY KEY (Var_Out_Assoc_Key)

);


CREATE TABLE Variable_Meta(

Variable_Key INT(11) NOT NULL,
Variable_Name NVARCHAR(40) NOT NULL,
Variable_Type NVARCHAR(20) NOT NULL,

PRIMARY KEY (Variable_Key)

);


CREATE TABLE Outcome_Meta(

Outcome_Key INT(11) NOT NULL,
Outcome_Name NVARCHAR(40) NOT NULL,
Outcome_Type NVARCHAR(15) NOT NULL,

PRIMARY KEY (Outcome_Key)

);


-- define integrity and referential constraints

ALTER TABLE Patient_Comorb ADD CONSTRAINT 
FOREIGN KEY (Patient_ID) REFERENCES Patient_Master(Patient_ID) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE Encounter_Master ADD CONSTRAINT 
FOREIGN KEY (Patient_ID) REFERENCES Patient_Master(Patient_ID) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE Encounter_Prescription ADD CONSTRAINT 
FOREIGN KEY (Encounter_ID) REFERENCES Encounter_Master(Encounter_ID) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE Encounter_Outcome ADD CONSTRAINT 
FOREIGN KEY (Encounter_ID) REFERENCES Encounter_Master(Encounter_ID) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE Cohort_Variable ADD CONSTRAINT 
FOREIGN KEY (Cohort_Pt_Key) REFERENCES Cohort_Patient(Cohort_Pt_Key) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE Cohort_Outcome ADD CONSTRAINT 
FOREIGN KEY (Cohort_Pt_Key) REFERENCES Cohort_Patient(Cohort_Pt_Key) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE Var_Out_Assoc ADD CONSTRAINT 
FOREIGN KEY (Variable_Key) REFERENCES Variable_Meta(Variable_Key) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE Var_Out_Assoc ADD CONSTRAINT 
FOREIGN KEY (Outcome_Key) REFERENCES Outcome_Meta(Outcome_Key) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE Cohort_Patient ADD CONSTRAINT 
FOREIGN KEY (Patient_ID) REFERENCES Patient_Master(Patient_ID) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE Cohort_Variable  ADD CONSTRAINT 
FOREIGN KEY (Variable_Key) REFERENCES Variable_Meta(Variable_Key) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE Cohort_Outcome ADD CONSTRAINT 
FOREIGN KEY (Outcome_Key) REFERENCES Outcome_Meta(Outcome_Key) ON DELETE NO ACTION ON UPDATE NO ACTION;