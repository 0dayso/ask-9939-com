<?php

//Adding an adapter mapping will make returns of the mapped typed be intercepted
//and mapped in adapters/%adapterName%Adapter.php. This works by using get_class
//So for example, if you return a PEAR resultset object, it is an instance of DB_result
//And we want this to be processed as a recordset in adapters/peardbAdapter.php,
//hence the following line:
$gateway->addAdapterMapping('db_result', 'peardb');
//For PDO (PHP 5.1 specific)
$gateway->addAdapterMapping('pdostatement', 'pdo');
//For oo-style MySQLi
$gateway->addAdapterMapping('mysqli_result', 'mysqli');
//For filtered array 
//And for filtered typed array (see adapters/lib/Arrayf.php and Arrayft.php)
$gateway->addAdapterMapping('arrayf', 'arrayf');
$gateway->addAdapterMapping('arrayft', 'arrayft');
//And you can add your own after this point... (note lowercase for both args!)

?>