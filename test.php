<?php

echo "working at the top<br>";
phpinfo();

class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('/vagrant/main.db');
      }
   }
   
  
   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      echo "Opened database successfully\n";
   }
   
   $sql = <<<EOF
        SELECT * from users;
EOF;

    $results = $db->query($sql);
    echo "is php working??<br>";
    
    while($row = $results->fetchArray()){
        echo "username: ".$row['username'].', password: '.$row['password'].', id: '.$row['id'].'<br>';
    }
    $db->close();
    echo "Yup. working right to the end";
   

?>