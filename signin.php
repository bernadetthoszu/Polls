<?php
    session_start();
    include_once('storage.php');
    include_once('authentication.php');

    $usersStorage = new Storage(new JsonIO('users.json'));
    $auth = new Auth($usersStorage);


    function validate($post, &$data, &$errors) {
        
        if ($post['username'] == ''){
            $errors['username'] = "Please give username!";
        } else if (strlen($post['username']) < 3){
            $errors['username'] = "Usernames are at least 3 characters long!";
        }
        if ($post['password'] == ''){
            $errors['password'] = "Please give password!";
        } else if (strlen($post['password']) < 3){
            $errors['password'] = "Passwords are at least 3 characters long!";
        }

        $data = $post;
    
        return count($errors) === 0;
    }
    
    
    $errors = [];
    $data = [];
    if ($_POST) {
        if (validate($_POST, $data, $errors)) {
            $auth_user = $auth->authenticate($data['username'], $data['password']);
            if (!$auth_user) {
                $errors['global'] = "Failed login!";
            } else {
                $auth->login($auth_user);
                header("Location: index.php");
                exit();
            }
        }
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
    
    <br>
    <br>

    <h1>Login</h1>

    <br>
    <br>

    <form action="signin.php" method="post" novalidate>
        <!--<input type="hidden" name="submitted" value="true" >-->
        <label for="username">Username: </label> <br>
        <input id="username" type="text" name="username" size="50"> <br>
        <label for="password">Password: </label> <br>
        <input id="password" type="password" name="password" size="50"> <br><br><br>
        <button type="submit">Login</button>
    </form>

    <br>
    <br>

    <p>
        <?php foreach ($errors as $e):?>
            <span style="color:red"><?= $e ?></span><br>
        <?php endforeach?>
    </p>

</body>
</html>