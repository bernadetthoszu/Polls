<?php
    //session_start();

    include('storage.php');
    $pollStorage = new Storage(new JsonIO('polls.json'));

    $poll_id = $_GET["poll_id"];
    $pollStorage->delete($poll_id);

    header("Location: index.php");
    exit();

?>
