 <?
 include _once('/var/www/html/src/php/CDPriorityQueve.php');

 $PQ = new CDPriorityQueve();
 $PQ -> insert('A',1);
 $PQ -> insert('B',4);
 $PQ -> insert('C',2);
 $PQ -> insert('D',3);
 $PQ -> insert('F',10);
 echo PQ -> top();

?>