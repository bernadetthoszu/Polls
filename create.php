<?php
    session_start();

    include('storage.php');
    $pollStorage = new Storage(new JsonIO('polls.json'));
    $usersStorage = new Storage(new JsonIO('users.json'));

    function validate($data, &$errors){
        if ($data['question'] == ''){
            $errors['question'] = "The question is required!";
        }
        if ($data['options'] == '') {
            $errors['options'] = "You must give possible options!";
        } else if (count(getOptions($data['options'])) < 2) {
            $errors['options'] = "There must be at least 2 options given.";
        }
        if ($data['isMultiple'] == ''){
            $errors['isMultiple'] = "Please choose the answer type!";
        }
        if ($data['deadline'] == ''){
            $errors['deadline'] = "The poll needs to have a deadline!";
        }

        return count($errors) === 0;
    }



    function getOptions($stropt){
        $options = explode("\r\n", $stropt);
        return $options;
    }

    $data = [];
    $data['id'] = '';
    $data['question'] = $_POST['question'] ?? '';
    $data['options'] = $_POST['options'] ?? '';
    $data['isMultiple'] = $_POST['isMultiple'] ?? '';
    $data['createdAt'] = $_POST['createdAt'] ?? '';
    $data['deadline'] = $_POST['deadline'] ?? '';
    $errors = [];
    if (count($_POST) > 0){
        if (validate($data, $errors)){
            $data['options'] = getOptions($data['options']);
            $data['answers'] = [];
            foreach ($data['options'] as $o){
                $data['answers'][$o] = 0;
            }
            $data['voted'] = [];
            $pollStorage->add($data);
        }
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create poll</title>
    <style>
        @import url("https://cdn.jsdelivr.net/gh/elte-fi/www-assets@19.10.16/styles/mdss.min.css");
        ul {
        color: red;
        }

        div {
        padding:5px;
        border-radius: 1em;
        }
    </style>
</head>
<body>

    <br>
    <br>

    <h1>Create poll</h1>

    <form action="create.php" method="post" novalidate>
        <p><label for="question">Vote text: </label> <br>
        <input id="question" type="text" name="question" size="33" value="<?= count($errors) > 0 ? $data['question'] : '' ?>"></p>
        <?php if(isset($errors['question'])): ?><span style="color: red"><?= $errors['question'] ?></span><?php endif ?><br>
        <p><label for="options">Options: </label> <br>
        <textarea id="options" name="options" rows="4" cols="50"></textarea></p>
        <?php if(isset($errors['options'])): ?><span style="color: red"><?= $errors['options'] ?></span><?php endif ?><br>
        <p><input type="radio" name="isMultiple" value="False" <?= $data['isMultiple']=="False" &&  count($errors) > 0 ? "checked" : '' ?>> Single choice<br> <!--hogyan csinálj egy radio input-pot állapottartóvá-->
        <input type="radio" name="isMultiple" value="True" <?= $data['isMultiple']=="True" &&  count($errors) > 0 ? "checked" : '' ?>> Multiple choice</p>
        <?php if(isset($errors['multiple'])): ?><span style="color: red"><?= $errors['multiple'] ?></span><?php endif ?><br>
        <p><label for="deadline">Deadline: </label>
        <input id="deadline" type="date" name="deadline" value="<?=  count($errors) > 0 ? $data['deadline'] : '' ?>"></p>
        <?php if(isset($errors['deadline'])): ?><span style="color: red"><?= $errors['deadline'] ?></span><?php endif ?><br>
        <input type="hidden" name="createdAt" value=<?= date("Y-m-d") ?>>
        <p><button type="submit">Create</button></p>
    </form>

</body>
</html>