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
		WEIGHT  DECIMAL(4, 1) default null );
		
	create table MUST_QUESTIONNAIREUSER (
		OWNERID  INTEGER default null ,
		SITEID  INTEGER default null ,
		CREATETIME  TIMESTAMP default now() ,
		RANDNUM  INTEGER default rand() ,
		"NO"  VARCHAR(10) primary key ,
		ID  VARCHAR(10) default null ,
		NAME  VARCHAR(30) default null ,
		GENDER  CHAR(1) default null ,
		BIRTHDAY  DATE default null ,
		EMAIL  VARCHAR(100) default null ,
		PHONE  VARCHAR(50) default null );
