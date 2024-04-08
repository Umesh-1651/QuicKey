


<?php require("script.php") ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuicKey | Login </title>
    <link rel="stylesheet" href="login.css">
 <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Bungee+Hairline&family=Bungee+Outline&family=Delius+Swash+Caps&family=IBM+Plex+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&family=Lilita+One&family=Lobster+Two:ital,wght@0,400;0,700;1,400;1,700&family=Mallanna&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Rajdhani:wght@300;400;500;600;700&family=Tac+One&family=Zilla+Slab:ital,wght@1,300&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/6fb7e7e04c.js" crossorigin="anonymous"></script>
    <?php
        if($username_err!=null){
            ?> <style>#uerr{display:block ;
                width:200px;
                font-size: 10px;
                margin: 0;
                margin-bottom:10px;
                background-color: black;
                margin-left: 160px;
                border: 1px solid #ccc;
                border-radius: 40px;
                padding: 5px;}</style><?php
        }
        if($pass_err!=null){
            ?> <style>#perr{display:block ;
                width:200px;
                font-size: 10px;
                margin: 0;
                margin-bottom:10px;
                background-color: black;
                margin-left: 160px;
                border: 1px solid #ccc;
                border-radius: 40px;
                padding: 5px;}</style><?php
        }
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Bungee+Outline&family=Delius+Swash+Caps&family=Lobster+Two:ital,wght@0,400;0,700;1,400;1,700&family=Mallanna&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
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
    font-weight:  500;
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
    
}
.logo{
    font-family: "IBM Plex Mono",monospace;
    font-size: 35px;
    padding:15px;
    animation: slide-down2 2s;
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
            <p>Hey there!,<br>Login to Launch!<p>
            <form class="loginform" action="#" method="post">
                <label class="ulab">Username</label>
                <input type="text" id="username" class="username" value="<?php echo $username ?>" name="username" placeholder="Username" >
                <p id="uerr"><?php echo $username_err ?></p>
                <div class="Pass">
                    <label>Password</label>
                    <input type="password" id="password" name="password" value="<?php echo $pass ?>" placeholder="Password">
                    <i class="fa-regular fa-eye" id="eye" onclick="pass()"></i>
                    <p id="perr"> <?php echo $pass_err ?></p>
                </div>
                <input type="submit" class="submit" name="login" value="Login">
            </form>
            <br><br>
            <p>New to Here?, <a href="signup.php" >SignUp!</a></p>
    </div>
    <script>
        var a=1;
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
        function check(){
            let a = document.getElementById('password').value;
            let b = document.getElementById('username').value;
            let msg = document.getElementById('message');
            if(a.length==0 || b.length==0){
                alert("Fields cannot be empty");
            }
        }
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
