<?php
    session_start();
    $logged_in = isset($_SESSION['user']);
    $is_admin = $logged_in && ($_SESSION['user']['username'] == "admin");

    include('storage.php');
    $pollStorage = new Storage(new JsonIO('polls.json'));
    $usersStorage = new Storage(new JsonIO('users.json'));

    $polls = $pollStorage -> findAll();
    $open_polls = array_filter($polls, function($p) {
        return $p["deadline"] >= date("Y-m-d");
    });
    $closed_polls = array_filter($polls, function($p) {
        return $p["deadline"] < date("Y-m-d");
    });
    uasort($open_polls, 'compareCreationDates');
    uasort($closed_polls, 'compareCreationDates');
    $polls = array_merge($open_polls, $closed_polls);
    $nr_polls = count($polls);
    

    function compareCreationDates($p1, $p2){
        $d1 = strtotime($p1["createdAt"]);
        $d2 = strtotime($p2["createdAt"]);
        if ($d1 < $d2)
            return True;
        return False;
    }

    function voted($pid){
        global $pollStorage;
        $poll = $pollStorage->findById($pid);
        foreach($poll['voted'] as $uid){
            if($_SESSION["user"]["id"] == $uid){
                return true;
            }
        }
        return false;
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szavazz ha tudsz!</title>
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
        }
  </style>
</head>
<body>
    <h1>Polls - Main Page</h1>
    <?php if (!$logged_in): ?>
        <a href="signin.php">Log in</a> | <a href="register.php">Register</a>
    <?php else: ?>
        <a href="logout.php">Log out</a>
    <?php endif ?>
    <br><br><br>
    <p>
        Voting is our superpower. We help manifest it!<br>
        Register, if you want to vote, or just look around and get the taste of it. <br> 
        Should you be the boss of supermen, called 'admin',<br> enjoy the extended functionalities of this app, such as creating/deleting polls or updating existing ones.<br>
        <span style="color:#517d81">Let's get started!</span>
    </p><br>
    <br>
    <ul style="list-style-type: none">
        <?php foreach ($polls as $poll_id => $p): ?>
        <li style="color: black; text-align: center">
            Poll: <?= $poll_id ?>  Vote nr.: <?= count($p["voted"]) ?>  Created: <?= $p["createdAt"] ?>  Deadline: <?= $p["deadline"] ?>  
            <?php if ($p["deadline"] < date("Y-m-d")): ?>
                <button> 
                    <a href=<?= "result.php?poll_id=" . $poll_id ?>>Results</a>
                </button>
                <?php if ($is_admin): ?>
                    <button>
                        <a href=<?= "delete.php?poll_id=" . $poll_id ?>> Delete </a>
                    </button>
                <?php endif ?>
            <?php else: ?>
                <button>
                    <?php if ($logged_in): ?> 
                        <?php if(!voted($poll_id)): ?>
                            <a href=<?= "vote.php?poll_id=" . $poll_id . "&update=no"?>> Vote </a>
                        <?php else: ?>
                            <a href=<?= "vote.php?poll_id=" . $poll_id . "&update=yes"?>> Update vote </a>
                        <?php endif ?>
                    <?php else: ?>
                        <a href="signin.php"> Vote </a>
                    <?php endif ?>
                </button>
                <?php if ($is_admin): ?>
                    <button>
                        <a href=<?= "delete.php?poll_id=" . $poll_id ?>> Delete </a>
                    </button>
                <?php endif ?>
            <?php endif ?>
        </li>
        <?php endforeach ?>
    </ul>
    <br>
    <p>
        <?php if (!$logged_in): ?> 
            <button>
                <a href="signin.php">Create poll</a>
            </button>
        <?php else:?>
            <?php if(!$is_admin): ?>
                <button disabled> Create poll </button>
            <?php else: ?>
                <button>
                    <a href="create.php">Create poll</a>
                </button>
            <?php endif ?>
        <?php endif ?>   
    </p>
</body>
</html>