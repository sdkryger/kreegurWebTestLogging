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
            .myButtons{
                min-width:100px;
            }
            td {
                padding:5px;
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
        <div id="dialogNewTask" title="New task" style="display:none;">
            Description<br>
            <input type="text" id="inputDescription">
            <div id="divEnterDescription" style="color:red;display:none;">
                Must enter a description
                
            </div>
        </div>
        <div id="dialogDeleteTask" title="Delete task" style="display:none;">
            Delete task?
            <div id="divDeleteMessage" style="color:red;display:none;">
                Error: no task selected
                
            </div>
        </div>
        <button class="myButtons someOtherClass" id="buttonNewTask">New task</button>
        <script src = "js/jquery-3.3.1.min.js"></script>
		<script src = "js/jquery-ui.min.js"></script>
        <script>
            var taskId = -1; //current task id (-1 is not set)
            $(document).ready(function(){
                updateTasks();
                $(".myButtons").button();
                $("#dialogNewTask").dialog({
                    modal:true,
                    autoOpen:false,
                    buttons: {
                        Okay: function(){
                            var descr = $("#inputDescription").val();
                            if(descr.length == 0)
                                $("#divEnterDescription").show();
                            else{
                                //alert("will try to add new task with description of: "+descr);
                                $(this).dialog('close');
                                $.post(
                                    'api/tasks.php',
                                    {
                                        action: 'newTask',
                                        description: descr
                                    },
                                    function(data){
                                        updateTasks();
                                        if(data.error)
                                            alert(data.message);
                                    },
                                    'json'
                                );
                            }   
                        },
                        Cancel: function(){
                            $(this).dialog('close');
                        }
                    },
                    open: function(event, ui){
                        $("#inputDescription").val('');
                        $("#divEnterDescription").hide();
                    }
                });
                $("#dialogDeleteTask").dialog({
                    modal:true,
                    autoOpen:false,
                    buttons: {
                        Okay: function(){
                            if(taskId == -1)
                                $("#divDeleteMessage").show();
                            else{
                                //alert("will try to add new task with description of: "+descr);
                                $(this).dialog('close');
                                $.post(
                                    'api/tasks.php',
                                    {
                                        action: 'deleteTask',
                                        id: taskId
                                    },
                                    function(data){
                                        updateTasks();
                                        if(data.error)
                                            alert(data.message);
                                        taskId = -1;
                                    },
                                    'json'
                                );
                            }   
                        },
                        Cancel: function(){
                            $(this).dialog('close');
                        }
                    },
                    open: function(event, ui){
                        $("#divDeleteMessage").hide();
                    }
                });
                $("#buttonNewTask").click(function(){
                    $("#dialogNewTask").dialog('open');
                });
                $('body').on('click','.finishTask',function(){
                    var id = $(this).attr('id').substr(6);
                    //alert("Should finish task with id of: "+id);
                    $.post(
                        'api/tasks.php',
                        {
                            action: 'endTask',
                            id: id
                        },
                        function(data){
                            updateTasks();
                            if(data.error)
                                alert(data.message);
                        },
                        'json'
                    );
                });
                $('body').on('click','.deleteTask',function(){
                    var id = $(this).attr('id').substr(6);
                    //alert("Should delete task with id of: "+id);
                    taskId = parseInt(id);
                    $("#dialogDeleteTask").dialog('open');
                });
                $('body').on('click','.configureTask',function(){
                    var id = $(this).attr('id').substr(9);
                    var url = 'taskConfigure.php?id='+id;
                    //alert("Should configure task with id of: "+id+' and url of: '+url);
                    window.open(url, '_self');
                });
            });
            function updateTasks(){
                $.post(
                    'api/tasks.php',
                    function(data){
                        //alert(JSON.stringify(data));
                        $("#tbodyTasks").empty();
                        $.each(data.tasks,function(index, value){
                            var html = '<tr><td>'+value.id+'</td>';
                            html += '<td>'+value.description+'</td>';
                            html += '<td>'+value.startTime.substr(0,19)+'</td>';
                            if(value.endTime == null)
                                html += '<td>On-going</td>';
                            else
                                html += '<td>'+value.endTime.substr(0,19)+'</td>';
                            html += '<td><button class="myButtons finishTask" id="finish'+value.id+'">Finish</button>';
                            html += '<button class="myButtons viewTask" id="view'+value.id+'">View data</button>';
                            html += '<button class="myButtons configureTask" id="configure'+value.id+'">Configure</button>';
                            html += '<button class="myButtons deleteTask" id="delete'+value.id+'">Delete</button></td>';
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