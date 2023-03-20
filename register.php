<?php
    //uncomment this if it doesn't work
    //session_start();
    include_once('storage.php');
    include_once('authentication.php');

    $usersStorage = new Storage(new JsonIO('users.json'));
    $auth = new Auth($usersStorage);

    function validate($data, &$errors) {
        
        if ($data['username'] == ''){
            $errors['username'] = "Username is required!";
        } else if (strlen($data['username']) < 3){
            $errors['username'] = "Username must be at least 3 characters long!";
        }
        if ($data['email'] == '') {
            $errors['email'] = "E-mail address is required!";
        } else if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)){
            $errors['email'] = "Invalid e-mail format!";
        }
        if ($data['password1'] == ''){
            $errors['password1'] = "You must give a password!";
        } else if (strlen($data['password1']) < 3){
            $errors['passwor1'] = "Your password must be at least 3 characters long!";
        }
        if ($data['password2'] == ''){
            $errors['password2'] = "Please confirm password!";
        } else if ($data['password1'] != '' && $data['password1'] != $data['password2']){
            $errors['password2'] = "The given password does not match the previous one!";
        }
        
    
        return count($errors) === 0;
    }
    
    
    $errors = [];
    $data = [];
    $data['username'] = $_POST['username'] ?? '';
    $data['email'] = $_POST['email'] ?? '';
    $data['password1'] = $_POST['password1'] ?? '';
    $data['password2'] = $_POST['password2'] ?? '';
    if ($_POST) {
        if (validate($data, $errors)) {
            if ($auth->user_exists($data['username'])) {
                $errors['global'] = "This user already registered an account on out platform!";
            } else {
                $auth->register($data);
                header("Location: signin.php");
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
    <title>Regisztrálás</title>
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
            color: red;
            text-align: center;
            font-size: 15px;
            font-style: italic;
        }
  </style>
</head>
<body>
    <h1>Register</h1>
    
    <br>
    <br>

    <form action="register.php" method="post" novalidate>
        <!--<input type="hidden" name="submitted" value="true" >-->
        <label for="username">Username: </label> <br>
        <input id="username" type="text" name="username" size="50" value="<?= $data['username'] ?>"> 
        <?php if(isset($errors['username'])): ?><span style="color: red"><?= $errors['username'] ?></span><?php endif ?><br>
        <label for="email">E-mail: </label> <br>
        <input id="email" type="email" name="email" value="<?= $data['email'] ?>">
        <?php if(isset($errors['email'])): ?><span style="color: red"><?= $errors['email'] ?></span><?php endif ?><br>
        <label for="password1">Password: </label> <br>
        <input id="password1" type="password" name="password1" size="50">
        <?php if(isset($errors['password1'])): ?><span style="color: red"><?= $errors['password1'] ?></span><?php endif ?><br>
        <label for="password2">Confirm password: </label> <br>
        <input id="password2" type="password" name="password2" size="50"> 
        <?php if(isset($errors['password2'])): ?><span style="color: red"><?= $errors['password2'] ?></span><?php endif ?><br><br><br>
        <button type="submit">Register</button>
    </form>

    <br>
    <br>

    <?php if (isset($errors['global'])): ?>
    <p><?= $errors['global'] ?></p>
    <?php endif ?>

</body>
</html>