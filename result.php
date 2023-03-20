<?php
    include('storage.php');
    $pollStorage = new Storage(new JsonIO('polls.json'));

    $poll_id = $_GET["poll_id"];
    $poll = $pollStorage -> findById($poll_id); 
    
    $nr_votes = array_sum($poll['answers']);

    function getPercentage($n){
        global $nr_votes;
        return $n * 100 / $nr_votes;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll</title>
    <style>
        @import url("https://cdn.jsdelivr.net/gh/elte-fi/www-assets@19.10.16/styles/mdss.min.css");
        ul {
            color: red;
        }
        div {
            padding:5px;
            border-radius: 1em;
        }
        p {
            text-align: center;
            font-size: 15px;
            font-style: italic;
        }
  </style>
</head>
<body>
    <h1>Results - <?= $poll_id ?></h1>

    <br>
    <br>

    <p> <?= $poll["question"] ?> </p>
    <p>
        <?php if ($poll["isMultiple"] == "True"): ?>
            (Multiple options allowed.)
        <?php else: ?>
            (Single choice required.)
        <?php endif ?>
    </p><br>

    <br>
    <br>

    <p>
        <?php foreach ($poll['answers'] as $q=>$a): ?>
            <?= $q ?>: &nbsp&nbsp <?= $a ?> (<?= getPercentage($a) ?>%) <br>
        <?php endforeach ?>
    </p>
    
</body>
</html>