questionnaire
=============

Questionnaire for ChangGung

### Install

1. cd _build
1. bower install
1. npm install
1. customize bootstrap css
	1. mv ./src/normalize.less \_build/bower_components/bootstrap/less/
	1. mv ./src/variables.less \_build/bower_components/bootstrap/less/
	1. cd \_build/bower_components/bootstrap
	1. npm install
	1. grunt dist-css
	1. cd ../../
1. grunt

### Table Schema

	create table MUST_QUESTIONNAIRE (
		OWNERID  INTEGER default null ,
		SITEID  INTEGER default null ,
		CREATETIME  TIMESTAMP default now() ,
		RANDNUM  INTEGER default rand() ,
		QUESTIONNAIRE  VARCHAR(50) default null ,
		"NO"  VARCHAR(10) default null ,
		ANSWER  VARCHAR(500) default null ,
		SCORE  INTEGER default null ,
		VERSION  INTEGER default null ,
		WEIGHT  DECIMAL(4, 1) default null,
		StaffID varchar(34) default null );

	create table MUST_QUESTIONNAIREUSER (
		OWNERID  INTEGER default null ,
		SITEID  INTEGER default null ,
		CREATETIME  TIMESTAMP default now() ,
		RANDNUM  INTEGER default rand() ,
		"NO"  VARCHAR(10) primary key ,
		NAME  VARCHAR(30) default null ,
		GENDER  CHAR(1) default null ,
		BIRTHDAY  DATE default null ,
		EMAIL  VARCHAR(100) default null ,
		PHONE  VARCHAR(50) default null,
		Volition char(1) default null,
		Weight Decimal(4, 1) default null,
		Caregiver char(1) default null,
		FirstDate date default null,
		LastDate data default null,
		Memo varchar(1500) default null );

	create table MUST_Staff (
		OWNERID  INTEGER default null ,
		SITEID  INTEGER default null ,
		CREATETIME  TIMESTAMP default now() ,
		RANDNUM  INTEGER default rand() ,
		Account char(20) primary key,
		Password char(12),
		Name varchar(30) );

