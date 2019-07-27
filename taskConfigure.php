<?php
session_start();

include 'api/functions.php';
$dbPath = $_SESSION['dbPath'];
if(isset($_REQUEST['id']))
    (int)$id = $_REQUEST['id'];
else
    $id = -1;



?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            Kreegur - template
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link href="css/jquery-ui.css" rel="stylesheet">
        <style>
            body {
                font-family:arial;
            }
            .myButtons{
                min-width:100px;
            }
            td {
                padding:5px;
            }
        </style>
    </head>
    <body>
    
        <script src = "js/jquery-3.3.1.min.js"></script>
		<script src = "js/jquery-ui.min.js"></script>
        <script>
            var taskId = <?php echo $id; ?>;
            var tags = [];
            $(document).ready(function(){
                alert("Ready to load config for task: "+taskId);
                updateTags();
            });
            function updateTags(){
                $.post(
                    'api/tags.php',
                    {
                        taskId: taskId
                    },
                    function(data){
                        console.log(JSON.stringify(data));
                        tags = data.tags;
                    },
                    'json'
                );
            }
        </script>
    </body>
</html>