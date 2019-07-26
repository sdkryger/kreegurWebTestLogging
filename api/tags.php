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
        if($action == 'newTag'){
            if(isset($_REQUEST['description']) && isset($_REQUEST['taskId'])){
                $description = $_REQUEST['description'];
                $taskId = (int)$_REQUEST['taskId'];
                $response->message = "Will try to add the new tag: ".$description;
                $scalingM = 1.0;
                $scalingB = 0.0;
                if(isset($_REQUEST['scalingM']))
                    $scalingM = (float)$_REQUEST['scalingM'];
                if(isset($_REQUEST['scalingB']))
                    $scalingB = (float)$_REQUEST['scalingB'];
                
                $stmt = $db->prepare("insert into tags (description,taskId,scalingM,scalingB) values (:description,:taskId,:scalingM,:scalingB)");
                $stmt->bindValue(':description', $description, SQLITE3_TEXT);
                $stmt->bindValue(':taskId', $taskId, SQLITE3_INTEGER);
                $stmt->bindValue(':scalingM', $scalingM, SQLITE3_FLOAT);
                $stmt->bindValue(':scalingB', $scalingB, SQLITE3_FLOAT);
                
                $stmt->execute();
                $sql = "select last_insert_rowid() from tags limit 1";
                $results = $db->query($sql);
                while($row = $results->fetchArray()){
                    //print_r($row);
                    $id = (int)$row[0];
                    $response->message .= " and the new id is ".$id;
                    $dbTag = $dbPath.'taskDbs/'.$taskId.'.db';
                    $dbTag = new SQLite3($dbTag);
                    $sql = "CREATE TABLE tag".$id." (timestamp text, value text);";
                    $response->message .= ", ".$sql;
                    $dbTag->exec($sql);
                    $dbTag->close();
                }
            } else {
                $response->error = true;
                $response->message = "Must specify a description and taskId";
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
            SELECT * from tags where deleted is null;
EOF;

        $results = $db->query($sql);
        //echo "is php working??<br>";
        $tags = array();
        while($row = $results->fetchArray()){
            //print_r($row);
            $tag = new stdClass();
            $tag->id = $row['id'];
            $tag->description = $row['description'];
            $tag->taskId = $row['taskId'];
            $tag->lastUpdated = $row['lastUpdated'];
            $tag->latestValue = $row['latestValue'];
            $tag->scalingM = $row['scalingM'];
            $tag->scalingB = $row['scalingB'];
            //$tag->deleted = $row['deleted'];
            array_push($tags, $tag);
        }
        $db->close();
        //echo "Yup. working right to the end";
        $response->tags = $tags;
    }

$response->executionTimeSeconds = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
echo json_encode($response);
?>