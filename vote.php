<?php
    session_start();
    $poll_id = $_GET["poll_id"];
    $update = $_GET["update"]=="yes" ? true : false;

    include('storage.php');
    $pollStorage = new Storage(new JsonIO('polls.json'));
    $usersStorage = new Storage(new JsonIO('users.json'));

    $poll = $pollStorage -> findById($poll_id);

    $error = NULL;
    if (count($_POST) > 0){
        if (isset($_POST["submitted"]) && count($_POST) == 1)
        $error = "Error: No option chosen!";

        if (!isset($error)){
            if ($poll['isMultiple'] == "False"){
                $poll['answers'][$_POST['option']] += 1;
            }
            else{
                foreach($poll['options'] as $o){
                    if (array_search($o, $_POST)){
                        $poll['answers'][$o] += 1;
                    }
                }
            }
            // if (!array_search($_SESSION['user']['id'], $poll['voted'])){
            //     $poll['voted'][] = $_SESSION['user']['id'];
            // }
            $poll['voted'][] = $_SESSION['user']['id'];
            $pollStorage->update($poll_id, $poll);
        }
    } 
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting</title>
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
    <h1>Voting</h1>
    <div>
        Creation date:     <?= $poll["createdAt"] ?><br>
        Deadline:          <?= $poll["deadline"] ?>
    </div>
    <br><br>
    <p> <?= $poll["question"] ?> </p>
    <form action="vote.php?poll_id=<?= $poll_id ?>&update=<?=$_GET["update"]?>" method="post" novalidate>
        <input type="hidden" name="submitted" value="true" >
        <?php if ($poll['isMultiple'] == "False"): ?>
            <?php foreach ($poll["options"] as $o): ?>
            <input type="radio" name="option" value="<?= $o ?>"> <?= $o ?> <br>
            <?php endforeach ?>
        <?php else: ?>
            <?php foreach ($poll["options"] as $idx => $o): ?>
            <input type="checkbox" name=<?= "option_" . $idx?> value="<?= $o ?>"> <?= $o ?> <br>
            <?php endforeach ?>
        <?php endif ?>
        <button type="submit">Submit</button>
    </form>
    
    <p>
    <?php if (isset($error)): ?>
        <span style="color:red"><?= $error ?></span>
    <?php elseif (isset($_POST["submitted"])): ?>
        <?= "Vote submitted successfully!" ?>
    <?php endif ?>
    </p>

</body>
</html>