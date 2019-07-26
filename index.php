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
        <script src = "js/jquery-3.3.1.min.js"></script>
		<script src = "js/jquery-ui.min.js"></script>
        <script>
            $(document).ready(function(){
                updateTasks();
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
                            html += '</tr>';
                            $("#tbodyTasks").append(html);
                        });
                    },
                    'json'
                );
            }
        </script>
    </body>

</html>