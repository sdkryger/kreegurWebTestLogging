<?php
session_start();

include 'api/functions.php';
$dbPath = $_SESSION['dbPath'];




?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            Kreegur - test logging system
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link href="css/jquery-ui.css" rel="stylesheet">
        <style>
            body {
                font-family:arial;
            }
        </style>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <td>
                        ID
                    </td>
                    <td>
                        Description
                    </td>
                    <td>
                        Start time
                    </td>
                    <td>
                        End time
                    </td>
                    <td>
                        Action
                    </td>
                </tr>
            </thead>
            <tbody id="tbodyTasks">
            </tbody>
        </table>
        <button class="myButtons someOtherClass" id="buttonNewTask">New task</button>
        <script src = "js/jquery-3.3.1.min.js"></script>
		<script src = "js/jquery-ui.min.js"></script>
        <script>
            $(document).ready(function(){
                updateTasks();
                $(".myButtons").button();
                
            });
            function updateTasks(){
                $.post(
                    'api/tasks.php',
                    function(data){
                        alert(JSON.stringify(data));
                        $("#tbodyTasks").empty();
                        $.each(data.tasks,function(index, value){
                            var html = '<tr><td>'+value.id+'</td>';
                            html += '<td>'+value.description+'</td>';
                            html += '<td>'+value.startTime+'</td>';
                            if(value.endTime == null)
                                html += '<td>On-going</td>';
                            else
                                html += '<td>'+value.endTime+'</td>';
                            html += '<td><button class="myButtons finishTask" id="finish'+value.id+'">Finish</button>';
                            html += '<button class="myButtons viewTask" id="view'+value.id+'">View</button></td>';
                            html += '</tr>';
                            $("#tbodyTasks").append(html);
                            $(".myButtons").button();
                        });
                    },
                    'json'
                );
            }
        </script>
    </body>

</html>