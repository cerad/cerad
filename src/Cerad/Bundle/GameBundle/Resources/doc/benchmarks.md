Basic test case consists of xml files 

Solaris 10 Machine
File NasoaSlots20130212.xml, Games Tot: 1104, Ins: 1104, Upd:  0, GTU:   0, GPU:   0, DUR: 8796
File NasoaSlots20130220.xml, Games Tot: 1297, Ins:  191, Upd: 72, GTU: 310, GPU: 343, DUR: 6266

Disable change tracking and the first case goes up to about 20 seconds. Not sure why.

Use qb to load in complete game in the second case causes 6266 > 8796
  Rebuilding query each time
  Not using parameters
  Need to verify this sort of performance on the production server since mysql might be more of a bottle neck there.

Refactored the managers into pure repos and added params for creating stuff.
File NasoaSlots20130212.xml, Games Tot: 1104, Ins: 1104, Upd: 0, GTU: 0, GPU: 0, DUR: 8347
File NasoaSlots20130220.xml, Games Tot: 1297, Ins: 191, Upd: 72, GTU: 310, GPU: 343, DUR: 5984

No real gain but no loss either.

File NasoaSlots20130212.xml, Games Tot: 1104, Ins: 1104, Upd:    0, GTU:    0, GPU:    0, DUR:  8331, MEM: 74,973,184
File NasoaSlots20130220.xml, Games Tot: 1297, Ins:  191, Upd:   72, GTU:  310, GPU:  343, DUR:  5908, MEM: 63,963,136

After each flush, clear the individual hash caches and then em->clear
File NasoaSlots20130212.xml, Games Tot: 1104, Ins: 1104, Upd:    0, GTU:    0, GPU:    0, DUR:  8725, MEM: 57671680
File NasoaSlots20130220.xml, Games Tot: 1297, Ins:  191, Upd:   72, GTU:  310, GPU:  343, DUR:  6363, MEM: 40370176

Takes a little bit longer to run but memory usage decreases significantly.  At least as reported by the stop watch component.
