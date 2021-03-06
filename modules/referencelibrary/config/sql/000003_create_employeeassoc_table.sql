CREATE TABLE ATTACHMENT.REFLIBEMPLOYEEASSOC (
  REFLIBRARYHEADERID bigint(20) NOT NULL,
  EMPLOYEEID bigint(20) NOT NULL,
  KEY REFLIBRARYHEADERID01_idx (REFLIBRARYHEADERID),
  KEY EMPLOYEEID01_idx (EMPLOYEEID),
  CONSTRAINT EMPLOYEEID01 FOREIGN KEY (EMPLOYEEID) REFERENCES EMPLOYEE.EMPLOYEES (EMPLOYEEID) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT HEADERID01 FOREIGN KEY (REFLIBRARYHEADERID) REFERENCES ATTACHMENT.REFERENCELIBRARYHEADER (REFLIBHEADERID) ON DELETE NO ACTION ON UPDATE NO ACTION
)  ENGINE=INNODB;