# Using a map file to pull xml attributes
$ ./console schedule:import NasoaSlots20130227.xml
Import NasoaSlots20130227.xml 0
Import Complete NASOA /home/ahundiak/datax/arbiter/SP2013/NasoaSlots20130227.xml
File NasoaSlots20130227.xml, Games Tot: 1358, Ins:    0, Upd:    0, GTU:    0, GPU:    0, DUR:   101, MEM: 11272192

# Uses moveToNextAttribute
$ ./console schedule:import NasoaSlots20130227.xml
Import NasoaSlots20130227.xml 0
Import Complete NASOA /home/ahundiak/datax/arbiter/SP2013/NasoaSlots20130227.xml
File NasoaSlots20130227.xml, Games Tot: 1358, Ins:    0, Upd:    0, GTU:    0, GPU:    0, DUR:    82, MEM: 11272192

Tiny bit faster but the memory consumption is exactly the same?

With the nextAttribute I don't need to maintain the mapping array, bit simplier code

=====================================================
*** Just process project/level/field on an empty database
$ ./console schedule:import NasoaSlots20130227.xml 1
Import NasoaSlots20130227.xml 1
Import Complete NASOA /home/ahundiak/datax/arbiter/SP2013/NasoaSlots20130227.xml
File NasoaSlots20130227.xml, Games Tot: 1358, Ins:    0, Upd:    0, GTU:    0, GPU:    0, DUR:   513, MEM: 14680064

=====================================================
New database, just checking for existing game
$ ./console schedule:import NasoaSlots20130227.xml 1
Import NasoaSlots20130227.xml 1
Import Complete NASOA /home/ahundiak/datax/arbiter/SP2013/NasoaSlots20130227.xml
File NasoaSlots20130227.xml, Games Tot: 1358, Ins:    0, Upd:    0, GTU:    0, GPU:    0, DUR:  1528, MEM: 20185088

### Took out the new game check, almost no change

=====================================================
New database, create new game
$ ./console schedule:import NasoaSlots20130227.xml 1
Import NasoaSlots20130227.xml 1
Import Complete NASOA /home/ahundiak/datax/arbiter/SP2013/NasoaSlots20130227.xml
File NasoaSlots20130227.xml, Games Tot: 1358, Ins:    0, Upd:    0, GTU:    0, GPU:    0, DUR:  1518, MEM: 20447232

Creating the game does not seem to impact max memory usage?

==========================================================
*** Full import
$ ./console schedule:import NasoaSlots20130227.xml 1
Import NasoaSlots20130227.xml 1
Import Complete NASOA /home/ahundiak/datax/arbiter/SP2013/NasoaSlots20130227.xml
File NasoaSlots20130227.xml, Games Tot: 1358, Ins: 1352, Upd:    0, GTU:    0, GPU:    0, DUR: 11204, MEM: 66584576

This is an earlier file, 350 fewer games, pretty much the same
After each flush, clear the individual hash caches and then em->clear
File NasoaSlots20130212.xml, Games Tot: 1104, Ins: 1104, Upd:    0, GTU:    0, GPU:    0, DUR:  8725, MEM: 57671680

Bottom line is that sticking with native xml does not really change import times or memory
It does however get rid of the mapping code.

==========================================================
Is it worthwhile to see if the csv is any faster given that it is a bit less reliable?

*** Basic scan with no processing
$ ./console schedule:import NasoaSlots20130227.csv 1
Import NasoaSlots20130227.csv 1
1  HOME MISCONDUCT - 24 (Robert Coates) - USB - 41
Import Complete NASOA /home/ahundiak/datax/arbiter/SP2013/NasoaSlots20130227.csv
File NasoaSlots20130227.csv, Games Tot: 1354, Ins:    0, Upd:    0, GTU:    0, GPU:    0, DUR:   186, MEM: 12320768

This took linger than the xml scan and actually used a bit more memory.

*** Loaded project/level/field
$ ./console schedule:import NasoaSlots20130227.csv 1
Import NasoaSlots20130227.csv 1
Import Complete NASOA /home/ahundiak/datax/arbiter/SP2013/NasoaSlots20130227.csv
File NasoaSlots20130227.csv, Games Tot: 1354, Ins:    0, Upd:    0, GTU:    0, GPU:    0, DUR:   515, MEM: 14680064

*** Full import
$ ./console schedule:import NasoaSlots20130227.csv 1
Import NasoaSlots20130227.csv 1
Import Complete NASOA /home/ahundiak/datax/arbiter/SP2013/NasoaSlots20130227.csv
File NasoaSlots20130227.csv, Games Tot: 1354, Ins: 1348, Upd:    0, GTU:    0, GPU:    0, DUR: 11099, MEM: 66584576
