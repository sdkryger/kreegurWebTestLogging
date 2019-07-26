<?php
session_start();
header('Content-Type: application/json');
$response = new stdClass();
$response->error = true;
$response->message = "Unable to execute";

include 'functions.php';
$dbPath = $_SESSION['dbPath'];

class MyDB extends SQLite3
    {
        function __construct()
        {
            global $dbPath;
            $this->open($dbPath.'main.db');
        }
    }
   
    $db = new MyDB();
    if(!$db){
        $response->message = $db->lastErrorMsg();
    } else {
        $response->message =  "Opened database successfully\n";
        $response->error = false;
    }
    if(isset($_REQUEST['action'])){
        $action = $_REQUEST['action'];
        if($action == 'newTask'){
            if(isset($_REQUEST['description'])){
                $description = $_REQUEST['description'];
                $response->message = "Will try to add the new task: ".$description;
                $stmt = $db->prepare("insert into tasks (description,startTime) values (:description,:startTime)");
                $stmt->bindValue(':description', $description, SQLITE3_TEXT);
                $stmt->bindValue(':startTime', timestamp(), SQLITE3_TEXT);
                $stmt->execute();
                $sql = "select last_insert_rowid() from tasks limit 1";
                $results = $db->query($sql);
                while($row = $results->fetchArray()){
                    //print_r($row);
                    $id = (int)$row[0];
                    $response->message .= " and the new id is ".$id;
                    $newDB = $dbPath.'taskDbs/'.$id.'.db';
                    $dbNew = new SQLite3($newDB);
                }
            } else {
                $response->error = true;
                $response->message = "Must specify a description";
            }
        } elseif ($action == 'endTask'){
            if(isset($_REQUEST['id'])){
                $id = (int)$_REQUEST['id'];
                $response->message = "Will complete task with id of ".$id;
                $stmt = $db->prepare("update tasks set endTime = :endTime where id = :id");
                $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
                $stmt->bindValue(':endTime', timestamp(), SQLITE3_TEXT);
                $stmt->execute();
            } else {
                $response->error = true;
                $response->message = "must specify id of task to end";
            }
        
        } elseif ($action == 'deleteTask'){
            if(isset($_REQUEST['id'])){
                $id = (int)$_REQUEST['id'];
                $response->message = "Will delete task with id of ".$id;
                $stmt = $db->prepare("update tasks set deleted = :endTime where id = :id");
                $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
                $stmt->bindValue(':endTime', timestamp(), SQLITE3_TEXT);
                $stmt->execute();
            } else {
                $response->error = true;
                $response->message = "must specify id of task to delete";
            }
        
        } else {
            $response->error = true;
            $response->message = "Not a recognized action";
        }
    } else { // no specified action so responsd with all tasks
        $sql = <<<EOF
            SELECT * from tasks where deleted is null order by startTime desc;
EOF;

        $results = $db->query($sql);
        //echo "is php working??<br>";
        $tasks = array();
        while($row = $results->fetchArray()){
            //print_r($row);
            $task = new stdClass();
            $task->id = $row['id'];
            $task->description = $row['description'];
            $task->startTime = $row['startTime'];
            $task->endTime = $row['endTime'];
            //$task->deleted = $row['deleted'];
            array_push($tasks, $task);
        }
        $db->close();
        //echo "Yup. working right to the end";
        $response->tasks = $tasks;
    }

$response->executionTimeSeconds = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
echo json_encode($response);
?>