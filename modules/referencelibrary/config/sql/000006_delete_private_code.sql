USE COREAPP;

-- Delete private code from Priority types.

SELECT @referencelibrary:=CODEID as CODEID from CODES where PARENTCODEID is null AND NAME='REFERENCELIBRARY';
SELECT @library:=CODEID as CODEID from CODES where PARENTCODEID=@referencelibrary AND NAME='LIBRARY';
SELECT @doctypes:=CODEID as CODEID from CODES where PARENTCODEID=@library AND NAME='DOCTYPES';
SELECT @docprioritytypes:=CODEID as CODEID from CODES where PARENTCODEID=@library AND NAME='DOCPRIORITYTYPES';
DELETE FROM CODES where PARENTCODEID = @docprioritytypes AND NAME = 'PRIVATE';