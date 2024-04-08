<?php 
    session_start();
    include("connection.php");
    include("functions.php");

    // if(isset($_POST['savedata'])){
        
    //     $un = $_POST['un'];
    //     $se = $_POST['se'];
    //     $pass = $_POST['pass'];
    //     try {
    //         if($username_err!=null){
    /*             ?> <style>#uerr{display:none ;
    //                 width:200px;
    //                 font-size: 10px;
    //                 margin: 0;
    //                 margin-bottom:10px;
    //                 background-color: black;
    //                 margin-left: 160px;
    //                 border: 1px solid #ccc;
    //                 border-radius: 40px;
    //                 padding: 5px;}</style><?php*/
                
    //         }
    //         // Begin the transaction
    //         $pdo->beginTransaction();

    //         // Prepare the insert statement
    //         $stmt = $pdo->prepare("INSERT INTO $tableName (id,service, username, password) VALUES (default,:service, :username, :password)");

    //         // Bind parameters
    //         $stmt->bindParam(':service', $se);
    //         $stmt->bindParam(':username', $un);
    //         $stmt->bindParam(':password', $pass);

    //         // Execute the query
    //         $stmt->execute();
    //         //Commit the transaction
    //         $pdo->commit();


    //         // Redirect or perform further actions
    //         header("Location: dashboard.php");
            
    //         exit();
    //     } catch (PDOException $e) {
    //         // Rollback the transaction if an error occurs
    //         $pdo->rollBack();
            
    //         // Handle the error (display an error message, log the error, etc.)
    //         echo "Error: " . $e->getMessage();
    //     }
    // }
    //TO Save data
    function save_data($pdo, $tableName, $se, $un, $pass) {
        try {
            $pdo->beginTransaction();
            $hashed = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO $tableName (service, username, password) VALUES (:service, :username, :password)");
    
            $stmt->bindParam(':service', $se);
            $stmt->bindParam(':username', $un);
            $stmt->bindParam(':password', $pass);

            $stmt->execute();
            $pdo->commit();
            
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            return false; 
        }
    }
    //To Get Data
    function get_data($pdo, $tableName, $se, $un) {

        $query = "SELECT * FROM $tableName WHERE 1";
        if (!empty($se)) {
            $query .= " AND service = :service";
        }
        if (!empty($un)) {
            $query .= " AND username = :username";
        }
    
        try {
            $stmt = $pdo->prepare($query);
    
            if (!empty($se)) {
                $stmt->bindParam(':service', $se);
            }
            if (!empty($un)) {
                $stmt->bindParam(':username', $un);
            }
    
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            
            return false; 
        }
    }
    function delete_data($pdo, $tableName, $se, $un, $pass) {
        // Prepare the SQL query to delete data
        $sql = "DELETE FROM $tableName WHERE service = :se AND username = :un AND password = :pass";
        
        // Prepare the SQL statement
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':se', $se, PDO::PARAM_STR);
        $stmt->bindParam(':un', $un, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);

        $stmt->execute();
        
        // Check if any rows were affected (i.e., if deletion was successful)
        if ($stmt->rowCount() > 0) {
            return true; // Deletion successful
        } else {
            return false; // Deletion failed
        }
    }
    if (isset($_POST['savedata'])) {
       
        $se = $_POST['se'];
        $un = $_POST['un'];
        $pass = $_POST['pass'];
        if(empty($se) || empty($un) || empty($pass)) {

            $_SESSION['output'] ="Please Enter Valid Details before Saving Data.";
            
        }
        elseif (save_data($pdo, $tableName, $se, $un, $pass)) {
            $_SESSION['output'] = "Your credentials are saved successfully.";
        } else {
            $_SESSION['output'] = "Failed to save credentials. Please try again later.";
        }
    
        header("Location: dashboard.php");
        exit();
    }
    // For Get Data Form
    if (isset($_POST['getdata'])) {

        $se = $_POST['service'];
        $un = $_POST['username'];
            // Set the session output variable to display the 'Result:' message
            $_SESSION['output'] = "Result:<br><br>";
        
            $data = get_data($pdo, $tableName, $se, $un);
            
            if ($data !== false) {
                foreach ($data as $row) {
                    $_SESSION['output'] .= "Service: " . $row['service'] . "<br> Username: " . $row['username'] . "<br> Password: " . $row['password'] . "<br><br>";
                }
            } else {
                $_SESSION['output'] .= "Failed to fetch data. Please try again later.";
            }
            
            // Check if there are no results from the database query
            if (isset($_POST['getdata']) && empty($data)) {
                $_SESSION['output'] .= "Failed to fetch data. There are no records matching your criteria.";
            }
            elseif (isset($_POST['getdata']) && !empty($data) && count($data) === 0) {
                $_SESSION['output'] .= "Failed to fetch data. The entered username is not found.";
            }
        header("Location: dashboard.php");
        exit();
    }
    if (isset($_POST['deletdata'])) { 
    
        $se = $_POST['se'];
        $un = $_POST['un'];
        $pass = $_POST['pass'];
        $hashedPass = $user_data['pass'];
        if(empty($se) || empty($un) || empty($pass)) {
            $_SESSION['output'] = "Please Enter Valid Details before Deleting Data.";
        }
        else if(!(password_verify($pass,$hashedPass))){
            $_SESSION['output'] = "Invalid QuicKey Password!";
        }
        elseif (delete_data($pdo, $tableName, $se, $un, $pass)) {
            $_SESSION['output'] = "Your credentials have been deleted successfully.";
        } else {
            $_SESSION['output'] = "Failed to delete credentials. Please try again later.";
        }
        header("Location: dashboard.php");
        exit();
    }
    if (isset($_POST['getPremium'])) { 
        $updateQuery = "UPDATE users SET is_premium = 'yes' WHERE uname = :uname";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':uname', $user_data['uname']); 
        $stmt->execute();
        header("Location: dashboard.php");
    }
    if (isset($_POST['remPremium'])) { 
        $updateQuery = "UPDATE users SET is_premium = 'no' WHERE uname = :uname";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':uname', $user_data['uname']); 
        $stmt->execute();
        header("Location: dashboard.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab | QuicKey</title>
    
    <script src="https://kit.fontawesome.com/6fb7e7e04c.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" type="image/png" href="qfav.png" >
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Bungee+Outline&family=Delius+Swash+Caps&family=IBM+Plex+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&family=Lobster+Two:ital,wght@0,400;0,700;1,400;1,700&family=Mallanna&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Rajdhani:wght@300;400;500;600;700&family=Zilla+Slab:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <style>
        *{
    margin: 0;
    padding: 0;
}
body{
    display: flex;
    background-image: url(stars1.png);
    background-size: 150% 150%;
    height: 100vh;
    animation: bg 100s ease-in-out infinite;
    overflow: hidden;
}
#uerr,#perr{
    font-size: 10px;
    margin: 0;
    background-color: black;
    margin-left: 160px;
    border: 1px solid #ccc;
    border-radius: 40px;
    padding: 5px;
    display: inline-block;

}

.container{
    display: flex;
    height: 100vh;
    width: 100%;
    font-family:"IBM Plex Mono",monospace;
}
/* .left{
    display: relative;
    position:absolute;
    margin: 100px 30px;
    width: 400px;
    height: 750px;
    border: 2px solid white;
    border-radius: 40px;
    background-color: gray;
    background-color: rgba(102, 102, 102, 0.2);
}
.lmid1{
    position:absolute;
    margin: 10px 25px;
    width: 350px;
    height: 150px;
    border: 2px solid white;
    border-radius: 40px;
    background-color: transparent;
} */

/* .lp1{
    margin: 40px 10px;
} */
.header{
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 70px;
    padding-left: 30px;
    background: rgb(19, 17, 17);
    display: flex;
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
.logo p::selection{
    color: beige;
    background-color:black;
}

@keyframes slide-down2{
    from{
        margin-top: -900px;
    }
    to{
        margin-top: 0%;
    }
}

@keyframes bg {
    0%{
        background-position: 0 50%;
    }

    50%{
        background-position: 100% 50%;
    } 

    100%{
        background-position: 0% 50%;
    }
    
}
@keyframes slide-right{
    from{
        margin-left: -900px;
    }
    to{
        margin-left: 30px;
    }
}
@keyframes slide-left{
    from{
        right: -200px;
    }
    to{
        right: -50px;
    }
}
@keyframes slide-up{
    from{
        margin-top: 900px;
    }
    to{
        margin-top: 410px;
    }
}
 @keyframes slide-down{
    from{
        margin-top: -900px;
    }
    to{
        margin-top: 80px;
    }
} 
.hamenu {
    font-size:24px;
    padding:10px;
    margin-left:1200px;
}
.side-menu{
    position:absolute;
    top:0px;
    right:-1000px;
    z-index:1000;
    position:fixed;
    height:100%;
    width:350px;
    background:black;
    box-shadow:0 0 6px rgba(255,255,255,0.5);
    transition: .5s ease-in-out;
}
.side-menu .menu {
    font-family:"IBM Plex Mono",monospace;
    color:beige;
    font-size:20px;
    padding: 20px 35px;
}
.side-menu li {
    margin-top: 6px;
    padding: 14px 20px;
    list-style-type: none;
    font-size:20px;
    border: 0px solid rgba(255, 255, 255, 0.5);
    border-radius:30px;
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
}
.closemenu i{
    color:white;
    font-size:24px;
    padding-top:22px;
    margin-left:240px;
}
.llab p{
    font-family:"IBM Plex Mono",monospace;
    font-size:30px;
    color:beige;
    margin-top:100px;
    margin-left:750px;
    text-decoration:underline;
}
.item1{
    position:absolute;
    margin-top:200px;
    left:70px;
    width:1400px;
    height:100px;
    border:2px solid white;
    color:black;
    background-color:beige;
    border-radius:40px;
    padding:10px;
    text-align:center;
}
.H1{
    font-family:"IBM Plex Mono",monospace;
    font-size:30px;
    color:black;
    margin-bottom:20px;
    text-align:center;
}
.item2{
    position:absolute;
    margin-top:350px;
    left:70px;
    width:1400px;
    color:black;
    height:100px;
    border:2px solid white;
    background-color:beige;
    border-radius:40px;
    padding:10px;
    text-align:center;
}
.H2{
    font-family:"IBM Plex Mono",monospace;
    font-size:30px;
    margin-bottom:20px;
    color:black;
    text-align:center;
}
.item3{
    position:absolute;
    margin-top:500px;
    left:70px;
    width:1400px;
    height:100px;
    color:black;
    border:2px solid white;
    background-color:beige;
    border-radius:40px;
    padding:10px;
    text-align:center;
}
.H3{
    font-family:"IBM Plex Mono",monospace;
    font-size:30px;
    color:black;
    margin-bottom:20px;
    text-align:center;
}
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php" class="logo">QUICKEY</a>
        <div class="side-menu">
            <a class="closemenu" onclick=hideSidebar()><i class="fa-solid fa-xmark"></i></a>
            <ul>
                <p class="menu">QuicKey</p>
                <li><a href="index.php"><i class="fa-solid fa-home"></i><p>Home</p></a></li>
                <li><a href="logout.php"><i class="fa-solid fa-circle-left"></i><p>Logout</p></a></li>
                <li><a href="login.php"><i class="fa-solid fa-right-to-bracket"></i><p>Login</p></a></li>
                <li><a href="signup.php"><i class="fa-solid fa-user-plus"></i><p>Signup</p></a></li>
                <li><a href="#"><i class="fa-solid fa-file-signature"></i><p>Contact Us</p></a></li>

            </ul>
        </div> 
        <a class="hamenu" onclick=showSidebar()><i class="fa-solid fa-bars"></i></a>
    </header>
    <div class="container">
        <div class="llab"><p>LAB</p></div>
        <div class="item1">
            <center><p class="H1">About Lab</p></center>
            <p>This is Where i will be updating upcoming updates!. And may require your feedback and support.</p>
        </div>
        <div class="item2">
            <center><p class="H2">Contact Us</p></center>
            <p>Contact us page will be updated in few days (i'm not lazy ^_^), because i am developing a system to communicate anonymously. </p>
        </div>
        <div class="item3">
           <center><p class="H3">Overall Experience and Premium.</p></center>
            <p>The Dashboard, Login-Signup mechanism will be updated in coming days,along with added Premium Features.</p>
        </div>
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