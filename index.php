<?php

include "config.php";


$header = "Content-Type: application/json;charset=utf-8";
ini_set('display_errors', 'On');


require "rb.php";

R::setup('mysql:host='.MYSQL_HOST.';dbname='.MYSQL_DATABASE, MYSQL_USER, MYSQL_PASS);

$status = NULL;
$answer = NULL;

$maximum_message_length = 102400;
$maximum_username_length = 20;

$json = file_get_contents('php://input');
$data = json_decode($json);



if (isset($data->action)) {

    header($header);
    
    switch ($data->action){

        case "get":

            if (isset($data->chat)) {

                if (isset($data->date)) {
                    $messages = R::getAll("SELECT * FROM `message` WHERE `date` > '".$data->date."' AND `chat` = '".$data->chat."' ORDER BY `date` ASC");
                    if ($messages) {
                        $answer = json_encode($messages);
                        $status = 200;
                    }
                    else {
                        $answer = "Es konnten keine passenden Nachrichten zu diesem Datum gefunden werden.";
                        $status = 404;
                    }
                }
                else {
                    $messages = R::getAll("SELECT * FROM `message` WHERE `date` > '".(time() - 3600)."' AND `chat` = '".$data->chat."' ORDER BY `date` ASC");
                    if ($messages) {
                        $answer = json_encode($messages);
                        $status = 200;
                    }
                    else {
                        $answer = "Es konnten keine passenden Nachrichten in diesem Chatraum gefunden werden.";
                        $status = 404;
                    }
                }

            }
            else {
                $status = 400;
                $answer = "Die Anfrage enthielt keine Informationen zum Chatroom.";
            }
            break;
        
        case "get_last":

            if (isset($data->chat)) {


                $message = R::getCell("SELECT `message` FROM `message` WHERE `chat` = '".$data->chat."' ORDER BY `date` DESC LIMIT 1");
                if ($message) {
                    $answer = $message;
                    $status = 200;
                }
                else {
                    $answer = "Es konnten keine passenden Nachrichten in diesem Chatraum gefunden werden.";
                    $status = 404;
                }

            }
            else {
                $status = 400;
                $answer = "Die Anfrage enthielt keine Informationen zum Chatroom.";
            }
            break;

        case "add":

            if (isset($data->user) && isset($data->message) && isset($data->chat)) {
                $message = R::dispense("message");
                $message->user = substr($data->user, 0, $maximum_username_length);
                $message->message = substr($data->message, 0, $maximum_message_length);
                $message->chat = $data->chat;
                $message->date = time();
                $id = R::store($message);
                $status = 200;
                $answer = "Die Nachricht wurde hinzugefügt.";
            }
            else {
                $status = 400;
                $answer = "Die Anfrage war fehlerhaft. Die Nachricht konnte nicht gesendet werden.";
            }
            break;

        case "new":

            if (isset($data->user)) {
                $message = R::dispense("message");
                $message->user = "Phoenix Chat (beta)";
                $message->message = "Willkommen im Chat des Phoenix Gymnasiums, ".substr($data->user, 0, $maximum_username_length)."!";
                $message->chat = rand(100000,999999);
                $message->date = time();
                $id = R::store($message);
                $status = 200;
                $answer = $message->chat;
            }
            else {
                $status = 400;
                $answer = "Die Anfrage war fehlerhaft. Es konnte kein neuer Chatroom erstellt werden.";
            }
            break;

    }

}
else {
    $status = 303;
    $answer = "<!DOCTYPE html>\n<html>\n<head>\n<meta http-equiv=\"Refresh\" content=\"0; URL='https://github.com/steedalot/phoenixchat'\">\n</head>\n<body></body>\n</html>";
}

http_response_code($status);
echo $answer;


?>