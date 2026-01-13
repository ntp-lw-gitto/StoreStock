<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "project"; // Replace "db_name" with the actual name of your database.
// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_errno) {
    Echo $mysqli->connect_errno.": ".$mysqli->connect_error;
    Echo nl2br("\n");
}else{
    Echo "Connect was successful.";
    Echo nl2br("\n");
}
$q = 'SELECT * FROM category';
                    if($result=$mysqli->query($q)){
                        echo '<table border="1">';
                        echo '<tr><th>CAT_NO</th><th>NAME</th><th>DES</th></tr>';
                        while($row=$result->fetch_array()){
                            echo "<tr>";
                            echo "<td>".$row['category_id']."</td>";
                            echo "<td>".$row['category_name']."</td>";
                            echo "<td>".$row['description']."</td>";
                            echo "</tr>";
                        }
                        echo '</table>';
                        $result->free();
                    }else{
                        echo 'Query error: '.$mysqli->error;
                    }
?>
