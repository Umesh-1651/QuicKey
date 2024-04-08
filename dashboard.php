<?php 
    session_start();
    include("connection.php");
    include("functions.php");

    $user_data = check_login($pdo);
    $tableName = "user_" . strtolower($user_data['uname']);
    $is_p = $user_data['is_premium'];

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
    <title>Dashboard | QuicKey</title>
    
    <script src="https://kit.fontawesome.com/6fb7e7e04c.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
.header{
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 70px;
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
    background-color: beige;
    font-weight:  500;
    padding: 5px 5px;
    margin-bottom: 30px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-family: 'Courier New', Courier, monospace;
    transition: .3s;
}
.header p a:hover{
    color: beige
    ;
    background-color: black;
    font-weight: 500;
    letter-spacing: .5px;
    cursor: pointer;
}
.logo{
    position: relative;
    font-size: 30px;
    color: rgb(255, 255, 255);
    text-decoration: none;
    font-weight:lighter;
    transition: .3s;
    opacity: 1;
    cursor: default;
    /* animation: slideRight 1s ease forwards; */
}
.header p{
    padding:10px 50px;
    color: beige
    ;
}
.header a{
    text-decoration: none;
    color:white;
    margin: 10px;
}
.container{
    display: relative;
    height: 100vh;
    width: 100%;
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
p{
    color:beige;
    font-family: 'Courier New', Courier, monospace;
    font-size: large;
}
/* .lp1{
    margin: 40px 10px;
} */
.mid1{
    position:absolute;
    margin: 80px 30px;
    width: 510px;
    height: 310px;
    border: 2px solid rgb(0, 0, 0);
    border-radius: 40px;
    background-color: rgba(39, 38, 38, 0.841);
    transition: .3s;
    animation: slide-right 2.5s;
}
.m1p1{
    width: 260px;
    margin: 10px;
    margin-left: 160px;
    font-size: 20px;
}
.slab{
    display: inline;
    color: beige;
    font-family: 'Courier New', Courier, monospace;
    font-size: 20px;
    margin-left: 70px;
    margin-top:0px;
}
.para1{
    display: block;
    color: beige;
    font-family: "IBM Plex Mono",sans-serif;
    font-size: 12px;
    text-align: center;
    margin-bottom: 15px;
}
.service{
    display: inline-block ;
    margin-left: 20px;
    background-color: black;
    color: beige;
    text-align: center;
    padding: 4px 15px;
    margin-top: 0px;
    margin-bottom: 30px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
.ulab{
    display: inline;
    color: beige
    ;
    font-family: 'Courier New', Courier, monospace;
    font-size: 20px;
    margin-left: 70px;
    margin-top:20px;
}
.username{
    display: inline-block ;
    margin-left: 70px;
    background-color: black;
    color: beige
    ;
    text-align: center;
    padding: 4px 15px;
    margin-bottom: 30px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
.Pass{
    position: relative;
    display: inline-block;
}
.Pass label{
    color: beige;
    font-family: 'Courier New', Courier, monospace;
    font-size: 20px;
    margin-left: 70px;
    
}
.Pass input{
    display: inline;
    margin-left: 70px;
    background-color: black;
    color: beige;
    text-align: center;
    padding: 4px 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 30px;
    box-sizing: border-box;
}
#eye{
    position: absolute;
    top: 7px;
    left: 410px;
    color: beige
    ;
}
#eye:hover{
    cursor: pointer;
}
.save,.delete{
    display: block;
    width: 190px;
    margin-left:160px;
    color: rgb(0, 0, 0);
    background-color: beige;
    font-weight:  700;
    text-align: center;
    padding: 3px 1px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 40px;
    font-family: 'Courier New', Courier, monospace;
    transition: .3s;
    font-size: 20px;
}
.save:hover, .delete:hover{
    color: beige
    ;
    background-color: black;
    font-weight: 800;
    letter-spacing: 2px;
    cursor: pointer;
}
.mid2{
    position:absolute;
    margin: 410px 30px;
    width: 510px;
    height: 290px;
    border: 2px solid rgb(0, 0, 0);
    border-radius: 40px;
    background-color: gray;
    background-color: rgba(39, 38, 38, 0.841);
    transition: .3s;
    animation: slide-up 2.5s;
}

.m2p1{
    width: 260px;
    margin: 10px;
    margin-left: 190px;
    font-size: 20px;
}
.para2{
    display: block;
    color: beige;
    font-family: "IBM Plex Mono",sans-serif;
    font-size: 12px;
    text-align: center;
    margin-bottom: 0px;
}
.get{
    display: block;
    width: 210px;
    margin-top: 0px;
    margin-left:150px;
    color: rgb(0, 0, 0);
    background-color: beige;
    font-weight:  700;
    text-align: center;
    padding: 5px 20px;
    margin-bottom: 30px;
    border: 1px solid #ccc;
    border-radius: 40px;
    font-family: 'Courier New', Courier, monospace;
    transition: .3s;
    font-size: 20px;
}
.get:hover{
    color: beige
    ;
    background-color: black;
    font-weight: 800;
    letter-spacing: 2px;
    cursor: pointer;
}
.slab2{
    display: inline;
    color: beige;
    font-family: 'Courier New', Courier, monospace;
    font-size: 20px;
    margin-left: 70px;
    margin-top:0px;
}
.service2{
    display: inline-block ;
    margin-left: 20px;
    background-color: black;
    color: beige;
    text-align: center;
    padding: 4px 15px;
    margin-top: 20px;
    margin-bottom: 30px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
.ulab2{
    display: inline;
    color: beige
    ;
    font-family: 'Courier New', Courier, monospace;
    font-size: 20px;
    margin-left: 70px;
    margin-top:20px;
}
.username2{
    display: inline-block ;
    margin-left: 70px;
    background-color: black;
    color: beige
    ;
    text-align: center;
    padding: 4px 15px;
    margin-bottom: 30px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
.mid3{
    position:absolute;
    margin: 80px 580px;
    width: 510px;
    height: 310px;
    border: 2px solid rgb(0, 0, 0);
    border-radius: 40px;
    background-color: rgba(39, 38, 38, 0.841);
    transition: .3s;
    animation: slide-down 2.5s;
}
.m3p1{
    width: 230px;
    font-size:20px;
    margin-left:150px;
    margin-top:10px;
    margin-bottom:px;
}
.slab3{
    display: inline;
    color: beige;
    font-family: 'Courier New', Courier, monospace;
    font-size: 20px;
    margin-left: 50px;
    margin-top:10px;
}
.ulab3{
    display: inline;
    color: beige;
    font-family: 'Courier New', Courier, monospace;
    font-size: 20px;
    margin-left: 50px;
    margin-top:20px;
}
.service3{
    display: inline-block ;
    margin-left: 50px;
    background-color: black;
    color: beige;
    text-align: center;
    padding: 4px 15px;
    margin-top: 20px;
    margin-bottom: 30px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
.username3{
    display: inline-block ;
    margin-left: 100px;
    background-color: black;
    color: beige
    ;
    text-align: center;
    padding: 4px 15px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
.mid3 .Pass label{
    font-size: 18px;
    margin-left:50px;
    display: inline;
}
.mid3 .Pass input{
    display: inline-block;
    margin-left: 20px;
    padding: 4px 15px;
}

.para3{
    display: block;
    color: beige;
    font-family: "IBM Plex Mono",sans-serif;
    font-size: 12px;
    text-align: center;
    margin: 10px;
}
#eye3{
    position: absolute;
    top: 5px;
    left: 415px;
    color: beige
    ;
}
.mid4{
    position:absolute;
    margin: 410px 580px;
    width: 510px;
    height: 290px;
    border: 2px solid rgb(0, 0, 0);
    border-radius: 40px;
    background-color: gray;
    background-color: rgba(39, 38, 38, 0.841);
    transition: .3s;
}

.m4p1{
    width: 260px;
    margin: 10px;
    font-family: "IBM Plex Mono",sans-serif;
    margin-left: 190px;
    margin-top:80px;
    font-size: 20px;
}
.para4{
    display: block;
    color: beige;
    font-family: "IBM Plex Mono",sans-serif;
    font-size: 20px;
    text-align: center;
    margin-left:20px;
    margin-bottom: 0px;
}
.mid4block{
    position:absolute;
    display:block;
    margin: 410px 580px;
    width: 510px;
    height: 290px;
    border: 2px solid rgb(0, 0, 0);
    border-radius: 40px;
    background-color: black;
    z-index:10;
    transition: .3s;
}
.mid1:hover,.mid2:hover,.mid3:hover{
    border-color: #ccc;
}

.output{
    display:block;
    position:absolute;
    margin: 75px;
    right:-50px;
    width: 350px;
    height: 640px;
    border: 2px solid white;
    border-radius: 40px;
    background-color: rgba(39, 38, 38, 0.841);
    animation: slide-left 2.5s;
}
.op1{
    font-size:20px;
    margin-top: 10px;
    margin-left: 150px;
}
.opara{
    display: block;
    color: beige;
    font-family: "IBM Plex Mono",sans-serif;
    font-size: 15px;
    text-align: center;
}
.screen{
    margin-top: 10px;
    width: 340px;
    height: 540px;
    margin-left: 4.5px;
    border: 1px solid rgb(0, 0, 0);
    border-radius: 40px;
    z-index: 20;
    background-color: rgb(0, 0, 0);
}
.screen::-webkit-scrollbar {
    display: none;
}
.screen p{
    margin-top: 20px;
    margin-left: 25px;
    font-size: 20px;
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
        <a href="index.php" class="logo">QUICKEY</a>
        <div class="side-menu">
            <a class="closemenu" onclick=hideSidebar()><i class="fa-solid fa-xmark"></i></a>
            <ul>
                <p class="menu">Hey there!,<?php echo $user_data['uname'] ?></p>
                <li><a href="dashboard.php"><i class="fa-solid fa-terminal"></i><p>Dashboard</p></a></li>
                <li><a href="login.php"><i class="fa-solid fa-right-to-bracket"></i><p>Login</p></a></li>
                <li><a href="signup.php"><i class="fa-solid fa-user-plus"></i><p>Signup</p></a></li>
                <li><a href="#"><i class="fa-solid fa-file-signature"></i><p>Contact Us</p></a></li>

            </ul>
        </div> 
        <a class="hamenu" onclick=showSidebar()><i class="fa-solid fa-bars"></i></a>
    </header>
    <div class="container">
        <!-- <div class="left">
            <p class="lp1">Your Recently Saved Passwords Will Be Displayed Here.</p>
            <div class="lmid1"></div>
        </div> -->
        <div class="mid1">
            <p class="m1p1">Save New Password</p>
            <p class="para1">(You can have multiple user accounts for a single service.<br> But make sure of different Usernames! )</p>
            <form class="savedata"  action="" method="post">
                <label class="slab">Service Name</label>
                <input type="text" id="service" class="service"  name="se" placeholder="Service Name">
                <label class="ulab">Username</label>
                <input type="text" id="username" class="username"  name="un" placeholder="Username" >
               
                <div class="Pass">
                    <label>Password</label>
                    <input type="password" id="password" name="pass" placeholder="Password">
                    <i class="fa-regular fa-eye" id="eye" onclick="pass()"></i>
                    
                </div>
                <input type="submit" class="save" name="savedata" value="Save">
            </form>
        </div>
        <div class="mid2">
            <p class="m2p1">Get Password</p>
            <p class="para2">(Enter username or service for all of its passwords<br>enter both for more specific.)</p>
            <form class="getdata" action="#" method="post">
                <label class="slab2">Service Name</label>
                <input type="text" id="service" class="service2"  name="service" placeholder="Service Name" >
                <label class="ulab2">Username</label>
                <input type="text" id="username" class="username2"  name="username" placeholder="Username" >
                <input type="submit" class="get" name="getdata" value="Get">
            </form>
        </div>
        <div class="mid3">
            <p class="m3p1">Delete Credential</p>
            <p class="para3">(Please make Sure of deleting your Credential<br>as You cannot retreive it again!)</p>
            <form class="deletdata"  action="" method="post">
                <label class="slab3">Service Name</label>
                <input type="text" id="service" class="service3"  name="se" placeholder="Service Name" >
                <label class="ulab3">Username</label>
                <input type="text" id="username" class="username3"  name="un" placeholder="Username" >
               
                <div class="Pass">
                    <label>QuicKey Password</label>
                    <input type="password" id="password" name="pass" placeholder="Password">
                    <i class="fa-regular fa-eye" id="eye3" onclick="pass()"></i>
                    
                </div>
                <input type="submit" class="delete" name="deletdata" value="Delete">
            </form>
        </div>
        <div class="mid4block"  <?php if ($user_data['is_premium'] == 'yes') echo 'style="display: none;"'; else echo 'style="display: block;"';?> >
            <p class="para1"><br><br>This Section is for premium users!</p>
            <form class="getPremium" action="#" method="post">
                
                <input type="submit" class="get" name="getPremium" value="Get Premium!.">
            </form>
        </div>
        <div class="mid4">
            <p class="m4p1">It's April Fool!</p>
            <p class="para4">(I'm Just Joking, Soon Premium Features will be available!<br>Thanks for Your Patience!😊)</p>
            <form class="getPremium" action="#" method="post">
            <input type="submit" class="get" name="remPremium" value="Remove Premium!.">
            </form>
        </div>
        <div class="output">
            <p class="op1">Result</p>
            <p class="opara">(Result of your selections will be displayed here.)</p>
            <div class="screen" style="max-height:650;overflow-y: auto;">
            <p><?php
                // Check if there is any output stored in session variables
                if (isset($_SESSION['output'])) {
                    echo $_SESSION['output'];
                    // Clear the session variable after displaying the output
                    unset($_SESSION['output']);
                }
                
                    else{
                        // Check if there are no results from the database query
                        if (isset($_POST['getdata']) && empty($data)) {
                            echo "Failed to fetch data. There are no records matching your criteria.";
                        }
                        elseif (isset($_POST['getdata']) && !empty($data) && count($data) === 0) {
                            echo "Failed to fetch data. The entered username is not found.";
                        }
                        else{
                            echo "Hey there, Currently there is nothing to Show here!";
                        }
                    }
                
            ?></p>
            </div>
        </div>
    </div>
    <script>
    function pass() {
        var passwordInput = document.getElementById('password');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            document.getElementById('eye').classList.remove('fa-eye');
            document.getElementById('eye').classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            document.getElementById('eye').classList.remove('fa-eye-slash');
            document.getElementById('eye').classList.add('fa-eye');
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