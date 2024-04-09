<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuicKey | SignUp </title>
    <link rel="stylesheet" href="signup.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://kit.fontawesome.com/6fb7e7e04c.js" crossorigin="anonymous"></script>
    <?php

session_start();
include("connection.php");
include("functions.php");

$error_msg = "";
$username = null;
$passwd = null;
$cnfp = null;
$email = null;
if(isset($_POST['signup'])){
$username = $_POST['username'];
$passwd = $_POST['password'];
$cnfp = $_POST['cnfp'];
$email = $_POST['email'];
    if(empty($username) || empty($passwd) || empty($cnfp) || empty($email)) {

        $error_msg = "Fields cannot be empty";
        ?><style>#message{display:block}</style><?php
      } elseif (preg_match('/[A-Z]/', $username)) { // Check if username contains capital letters
          $error_msg = "Username cannot contain capital letters";
        ?><style>#message{display:block}</style><?php
      }
     elseif($passwd!== $cnfp) {
        $error_msg = "Please Make Sure Passwords are matched!";
        ?><style>#message{display:block}</style><?php
   
} else {
    try {
        // Your SQL query to insert or update data
        // For example, inserting a new user with unique email and username
        $pdo->beginTransaction();

        $hashedpass = password_hash($passwd, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users(uid,uname, pass, email) VALUES (default,:uname, :pass, :email)");
        $stmt->bindParam(':uname', $username);
        $stmt->bindParam(':pass', $hashedpass);
        $stmt->bindParam(':email', $email);

        // Execute the query
        $stmt->execute();

        // Create a table for the user
        $tableName = "user_" . strtolower($username);
        $createTableQuery =     "CREATE TABLE IF NOT EXISTS $tableName (
                                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                service VARCHAR(255) NOT NULL,
                                username VARCHAR(255),
                                password VARCHAR(255) NOT NULL,
                                email VARCHAR(255),
                                is_active TINYINT(1) NOT NULL DEFAULT 0, -- 0 for inactive, 1 for active
                                is_premium ENUM('yes', 'no') NOT NULL DEFAULT 'no', -- 'yes' for premium, 'no' for non-premium
                                time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                            )";
        $pdo->exec($createTableQuery);

        // If the execution is successful, do further processing or redirect the user
        // For example, redirect the user to the dashboard
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        if(isset($pdo) && $pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
    
        // Handle other types of database errors
        if(isset($e->errorInfo[1])) {
            if ($e->errorInfo[1] == 1062) { // 1062 is the MySQL error code for a duplicate entry error
                // Check if the error message contains information about the 'uname' or 'email' column
                if (strpos($e->getMessage(), 'unique_username')) {
                    // Handle unique 'uname' constraint violation
                    $error_msg = "Username is already taken.";
                } elseif (strpos($e->getMessage(), 'unique_email')) {
                    $error_msg = "Email is already registered, Please login instead.";
                } else {
                    // Handle other types of unique constraint violations
                    $error_msg = "Unique constraint violation occurred";
                }
            } elseif ($e->errorInfo[1] == 1050) { // 1050 is the MySQL error code for a table already exists error
                // Handle table already exists error
                $error_msg = "User table already exists, please choose a different username.";
            } else {
                // Handle other types of database errors
                $error_msg = "Database error occurred: " . $e->getMessage();
            }
        } else {
            $error_msg = "Database error occurred";
        }
       
        ?><style>#message{display:block}</style><?php
    }
    }
}
?>

<style>
        .header{
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 80px;
    padding-left: 30px;
    background: rgb(19, 17, 17);
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 100;
}
.header p a{
    width: 150px;
    margin-top: 10px;
    color: black;
    background-color: antiquewhite;
    padding: 5px 5px;
    margin-bottom: 30px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-family: 'Courier New', Courier, monospace;
    transition: .3s;
}
.header a{
    text-decoration: none;
    color:white;
    font-size:35px;
}
.logo{
    font-family: 'Courier New', Courier, monospace;
    font-size: 35px;
    padding:15px;
    animation: slide-down 2s;
    font-weight:lighter;
}
.logo:hover{
    cursor:pointer;
}
@keyframes slide-down{
    from{
        margin-top: -900px;
    }
    to{
        margin-top: 0%;
    }
}
@keyframes slide-down{
    from{
        margin-top: -900px;
    }
    to{
        margin-top: 0%;
    }
}
.logo p::selection{
    color: beige;
    background-color:black;
}
.login p a,.signup p a{
    font-family: "Zilla Slab", serif;
    color: black;
    font-size: 20px;
    text-decoration: none;
    border: 2px solid white;
    border-radius: 40px;
    padding:10px;
    letter-spacing: 2px;
    background-color: beige;
    transition: 0.3s;
}
.login{
    animation: slide-down 2s;
    margin-left: 1400px;
}
.signup{
    animation: slide-down 2s;
     margin-right: 100px;
}
.login p a:hover,.signup p a:hover{
    background-color: black;
    color: beige;
    
}
.login p a::selection,.signup p a::selection{
    color: beige;
    background-color:black;
}
.hamenu{
    color:white;
    font-size:36px;
    padding:10px;
    margin-right:100px;
}
.side-menu{
    position:absolute;
    top:0px;
    right:-1000px;
    z-index:1000;
    position:fixed;
    height:100%;
    width:400px;
    background:black;
    box-shadow:0 0 6px rgba(255,255,255,0.5);
    transition: .5s ease-in-out;
}
.side-menu .menu {
    font-family: "Zilla Slab", serif;
    color:beige;
    font-size:35px;
    padding: 14px 35px;
}
.side-menu li {
    margin-top: 6px;
    padding: 14px 20px;
    list-style-type: none;
    font-size:20px;
    border: 0px solid rgba(255, 255, 255, 0.5);
    transition: border .3s ease-in-out;
}
.side-menu li p{
    font-family: 'Courier New', Courier, monospace;
    display:inline;
    margin-left:10px;
}
.side-menu i{
    padding:10px;
    padding-left:20px;
    font-size:20px;
}
.side-menu li:hover{
    border: 5px solid rgba(255, 255, 255, 0.5);
}
.closemenu{
    position:absolute;
    color:white;
    font-size:40px;
    padding:10px;
    margin-left:280px;
}
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Bungee+Outline&family=Delius+Swash+Caps&family=Lobster+Two:ital,wght@0,400;0,700;1,400;1,700&family=Mallanna&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
</head>
<body>
<header class="header">
        <a href="index.php" class="logo"><p>QuicKey</p></a>
        <div class="side-menu">
            <a class="closemenu" onclick=hideSidebar()><i class="fa-solid fa-xmark"></i></a>
            <ul>
                <p class="menu">QuicKey</p>
                <li><a href="dashboard.php"><i class="fa-solid fa-terminal"></i><p>Dashboard</p></a></li>
                <li><a href="login.php"><i class="fa-solid fa-right-to-bracket"></i><p>Login</p></a></li>
                <li><a href="signup.php"><i class="fa-solid fa-user-plus"></i><p>Signup</p></a></li>
                <li><a href="#"><i class="fa-solid fa-file-signature"></i><p>Contact Us</p></a></li>

            </ul>
        </div> 
        <a class="hamenu" onclick=showSidebar()><i class="fa-solid fa-bars"></i></a>
    </header>
    <div class="container">
            <p>Hey there!,<br>SignUp to Start!<p>
            <form class="loginform" action="" method="post">
                <label class="ulab">Username</label>
                <input type="text" id="username" class="username" name="username" placeholder="Username" value="<?php echo $username ?>" required>
                <div class="Pass">
                    <label>Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="fa-regular fa-eye" id="eye" onclick="pass()"></i>
                </div>
                <div class="Pass">
                    <label id = "cnp">Confirm Password</label>
                    <input type="password" id="cnfp" name="cnfp" placeholder="Confirm Password" required>
                    <i class="fa-regular fa-eye" id="eye1" onclick="pass1()"></i>
                    
                </div>
                <label id = "em">E-Mail</label>
                <input type="email" id="email" class="email" name="email" placeholder="Email Address" value="<?php echo $email?>"  required>
                <p id="message">
                    <?php echo $error_msg ?></p>
                <input type="submit" class="submit" name="signup" value="SignUp">
            </form>
            
            <p class="para">Already Have an Account?, <a href="login.php" >Login!</a></p>
    </div>
    <script>
         var a=1;
         var b=1;
        function pass(){
            if(a == 1){
                document.getElementById('password').type='text';
                document.getElementById('eye').classList.remove('fa-eye');
                document.getElementById('eye').classList.add('fa-eye-slash');
                a=0;
            }
            else{
                document.getElementById('password').type='password';
                document.getElementById('eye').classList.remove('fa-eye-slash');
                document.getElementById('eye').classList.add('fa-eye');
                a=1;
            }
        }
        function pass1(){
            if(a == 1){
                document.getElementById('cnfp').type='text';
                document.getElementById('eye1').classList.remove('fa-eye');
                document.getElementById('eye1').classList.add('fa-eye-slash');
                a=0;
            }
            else{
                document.getElementById('cnfp').type='password';
                document.getElementById('eye1').classList.remove('fa-eye-slash');
                document.getElementById('eye1').classList.add('fa-eye');
                a=1;
            }
        }

        // function checkpass(){
        //     let a = document.getElementById('password').value;
        //     let b = document.getElementById('cnfp').value;
        //     let c = document.getElementById('username').value;
        //     let msg = document.getElementById('message');
        //     if(a.length!=0 && b.length!=0 && c.length!=0){
        //         if(a === b) {
        //             msg.style.display="block";
        //             msg.textContent = "Passwords Matched";
        //         }
        //         else{
        //             msg.style.display="block";
        //             msg.textContent = "Passwords Do Not Match";
        //         }
        //     }
        //     else{
        //         alert("Fields cannot be empty");
        //         msg.textContent = "Please Enter Password Correctly!";
        //     }
        // }
        function showSidebar(){
            const sidebar = document.querySelector('.side-menu')
            sidebar.style.right = '0px';
        }
        function hideSidebar(){
            const sidebar = document.querySelector('.side-menu')
            sidebar.style.right = '-1000px';
        }
    </script>
</body>
</html>
