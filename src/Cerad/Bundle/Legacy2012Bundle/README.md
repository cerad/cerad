Legacy 2012 Bundle
====================

Holder for previous svn controlled code.

Actually a combination of three sets of code.
1. osso2012, pre symfony, main difference from 2007 is the inclusion of project_id
2. osso2012x - S2.0 code actually used for the national games 2012 as well as s5games 2012.  Includes tournament scoring.
3. osso2012y - S2.1 code done between July 2012 and the start of Fall 2012 season.  Moved entities to yaml.  Did a bit of tweaking.

Projects
37 -              games  160, Winter 2011
52 - Persons 612, games  849, NatGames2012
61 -              games  206, S5Games2011
62 - Persons 154, games  194, S5Games2012
70 - Persons 216, games    0, Fall 2011
77 -              games  227, Winter 2012
78 -              games  797, Spring 2012
79 -              games   65, Send off 2012
80 - Persons 228, games 1100, Fall 2012

Looks like I moved from AccountPerson to PersonPerson sometime in the lead up to NatGames

Want to reuse the account/person information for the Area5B Spring tournament as well as the s5games 2013 tournament.

Using osso2012x_20130215.sql as reference database

When I started to tweak things after ng2012 I back fitted the changes to the 2012x database.  
At least I am pretty sure I did;

Whiles some entities were adjusted when creating the legacy2012 bundle, the database itself was left unchanged.



