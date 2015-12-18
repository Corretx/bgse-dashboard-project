use Group9db;

-- Creating new table
DROP TABLE IF EXISTS Population_in_Selection;
Create table Population_in_Selection 
as Select * from Cohort_Patient;

-- Creating views

DROP VIEW IF EXISTS `Humalog_female`;

CREATE VIEW Humalog_female AS
Select age, count(gender) as gender 
from Cohort_Patient 
where Drug_Name = 'Humalog' 
and Gender = 'female' 
group by age;

DROP VIEW IF EXISTS `Humalog_male`;

CREATE VIEW Humalog_male AS
Select age, count(gender) as gender 
from Cohort_Patient 
where Drug_Name = 'Humalog' 
and Gender = 'male' 
group by age;

DROP VIEW IF EXISTS `Novolog_male`;

CREATE VIEW Novolog_male AS
Select age, count(gender) as gender 
from Cohort_Patient 
where Drug_Name = 'Novolog' 
and Gender = 'male' 
group by age;

DROP VIEW IF EXISTS `Novolog_female`;

CREATE VIEW Novolog_female AS
Select age, count(gender) as gender 
from Cohort_Patient 
where Drug_Name = 'Novolog' 
and Gender = 'female' 
group by age;


DROP VIEW IF EXISTS `Age_Gender`;

CREATE VIEW Age_Gender AS
Select age, count(gender) as gender 
from Cohort_Patient 
group by age;


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
union all
Select distinct B.Cohort_Pt_Key,C.Variable_Key from
Patient_Master A, Cohort_Patient B, Variable_Meta C, Encounter_Prescription D, Encounter_Master E
where A.Patient_ID = B.Patient_ID
and A.Patient_ID = E.Patient_ID
and E.Encounter_ID = D.Encounter_ID
and C.Variable_Name  = D.Prescription_Name
and C.Variable_Type = 'Prescription'
union all
select A.Cohort_Pt_Key, B.Variable_Key from
Cohort_Patient A, Variable_Meta B
where Replace(A.Age,'[','Age.[') = B.Variable_Name
union all
select A.Cohort_Pt_Key, B.Variable_Key from
Cohort_Patient A, Variable_Meta B
where A.Gender = B.Variable_Name
union all
select A.Cohort_Pt_Key, B.Variable_Key from
Cohort_Patient A, Variable_Meta B
where A.Race = B.Variable_Name;
END $$
DELIMITER ;

call cohort_var_ins;


-- Queries for inserts into Cohort_Outcome

DELIMITER $$
CREATE PROCEDURE cohort_out_ins()
BEGIN
insert into Cohort_Outcome (Cohort_Pt_Key, Outcome_Key, Outcome_Value) 
Select A.Cohort_Pt_Key, D.Outcome_Key, sum(C.Outcome_Value) from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and replace(C.Outcome_Name,'_','.') = D.Outcome_Name
and D.Outcome_Type = 'Utilization.Sum'
group by A.Cohort_Pt_Key, D.Outcome_Key

union all

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

union all

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

union all

Select A.Cohort_Pt_Key, D.Outcome_Key, avg(C.Outcome_Value) from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and replace(C.Outcome_Name,'_','.') = D.Outcome_Name
and D.Outcome_Type = 'Utilization.Avg'
group by A.Cohort_Pt_Key

union all


select Cohort_Pt_Key,Outcome_Key,Outcome_Value from
(Select Cohort_Pt_Key,Outcome_Key,Outcome_Value, max(Frequency) from
(SELECT A.Cohort_Pt_Key, D.Outcome_Key, C.Outcome_Value, count(*) as Frequency
from
Cohort_Patient A, Encounter_Master B, Encounter_Outcome C, Outcome_Meta D  
where A.Patient_ID = B.Patient_ID
and C.Encounter_ID = B.Encounter_ID
and C.Outcome_Name = D.Outcome_Name
and D.Outcome_Type = 'Laboratory'
and C.Outcome_Value <> 'None'
GROUP BY A.Cohort_Pt_Key, D.Outcome_Key, C.Outcome_Value) A
group by Cohort_Pt_Key,Outcome_Key) B


union all

Select A.Cohort_Pt_Key, C.Outcome_Key, 1 as Outcome_Value 
from
Cohort_Patient A, Encounter_Master B, Outcome_Meta C
where A.Patient_ID = B.Patient_ID
and C.Outcome_Type = 'Mortality'
and B.Discharge_Type = 'Expired'
;
END $$
DELIMITER ;

call cohort_out_ins;
