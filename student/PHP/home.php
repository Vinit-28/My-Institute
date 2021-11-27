<?php

    // Checking the Authenticity of the User //
    session_start();

    require "../../Server/Utilities/DatabaseConfigurations.php";
    require "../../Server/Utilities/UserUtilities.php";
    
    $databaseConnectionObject = get_DatabaseConnectionObject("App_Database");
    
    if( !( isset($_SESSION["isUserLogedIn"]) && isset($_SESSION["userId"]) && isset($_SESSION["sessionId"]) && (isUserOnline($databaseConnectionObject, $_SESSION["userId"], $_SESSION["sessionId"])) ) ){
        
        session_destroy();
        header('Location: ../../index.php');
    }
    else{
?>

<!DOCTYPE html>
<html lang="en">

    <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/home.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <title>Dashboard</title>
</head>

<body>

    <nav id="mynavbar">

        <div id="mynavLeft">
            <div id="mytoggleButton">
                <div id="mybar"></div>
            </div>
            <img src="../IMAGES/Logo.svg" alt="">
        </div>

        <div id="mynavRight">
            <ul id="mynavUl">
                <li class="navIcons bx-tada-hover"><i class='bx bxs-moon '></i></li>
                <li class="navIcons bx-tada-hover"><i class='bx bxs-bell '></i></li>
                <li class="navIcons bx-tada-hover" id="logout"><i class='bx bx-exit  '></i></li>
                <li class="navIcons"><?php echo "<img src='" . $_SESSION['userProfile'] . "' alt=''>";?></li>
            </ul>

        </div>

    </nav>

    <div id="mydashboardContainer">

        <div id="mynavigationBar">
            <div class="mynavigationItem " id="item1">
                <i class='mynavigationItemIcon bx bx-home'></i>
                <span class="mynavigationItemName">Dashboard</span>
            </div>
            <div class="mynavigationItem">
                <i class='mynavigationItemIcon bx bxs-user'></i>
                <span class="mynavigationItemName">Profile</span>
            </div>
            <div class="mynavigationItem">
                <i class='mynavigationItemIcon bx bxs-user-plus '></i>
                <span class="mynavigationItemName">Add Teacher</span>
            </div>
            <div class="mynavigationItem">
                <i class='mynavigationItemIcon bx bx-laptop'></i>
                <span class="mynavigationItemName">Live_Classes</span>
            </div>
            <div class="mynavigationItem">
                <i class='mynavigationItemIcon bx bx-download'></i>
                <span class="mynavigationItemName">Download</span>
            </div>
        </div>

        
        <!-- <div id="divContainer"> -->

            <div id="div3" about="addTeacher" class="formsDiv">
                <div class="boxHeadingDiv">
                    <h3 class="boxHeading">Add Teacher Pannel</h3>
                </div>
                <form id="addteacherform" action="" method="post" class="forms">
                    <input disabled required autocomplete="off" type="text" placeholder="*Institute Name">
                    <input name="teacherId" required autocomplete="off" type="text" placeholder="*Teacher ID">
                    <input name="teacherName" required autocomplete="off" type="text" placeholder="*Teacher Name">
                    <input name="teacherPassword" required autocomplete="off" type="password" placeholder="*Password">
                    <input name="teacherEmail" required autocomplete="off" type="email" placeholder="*Email">
                    <input name="teacherPhone" required autocomplete="off" type="number" placeholder="*Phone No.">
                    <button type="submit" name="submitTeacher">Submit Details</button>
                </form>
            </div>

        <!-- </div> -->

        
        <!-- <div id="div4" about="addStudent" class="formsDiv">
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Add Student Pannel</h3>
            </div>
            <form id="addstudentform" action="" method="post" class="forms">
                <input required autocomplete="off" name="studentId" type="text" placeholder="*Student ID">
                <input required autocomplete="off" name="studentName" type="text" placeholder="*Student Name">
                <input required autocomplete="off" name="studentPassword" type="password" placeholder="*Password">
                <input required autocomplete="off" name="studentEmail" type="email" placeholder="*Student Email">
                
                <select id="gender" name="studentGender">
                    <option class="options" value="male" selected="selected">Male</option>
                    <option class="options" value="female">Female</option>
                    <option class="options" value="other">Other</option>
                </select>

                <input required autocomplete="off" name="studentPhone" type="number" placeholder="*Phone No.">
                <input required autocomplete="off" name="studentAddress" type="text" placeholder="*Address">
                <input required autocomplete="off" name="studentCity" type="text" placeholder="*City">
                <input required autocomplete="off" name="studentState" type="text" placeholder="*State">
                <input required autocomplete="off" name="studentPin" type="number" placeholder="*PIN Code">
                <button type="submit" name="submitTeacher">Submit Details</button>
            </form>
        </div> -->

   
    </div>
    <p style="display: none;" id="userId"><?php echo $_SESSION['userDetails']['userId'];?></p>
</body>

</html>


<script src="../JS/jquery.js"></script>

<script>

    let reloaded = false;
    // $(window).scroll(function () {
        //     var y = $(window).scrollTop();
    //     if (y > 0) {
        //         $("#mynavbar").addClass('nav-shadow');
        //     }
        //     else {
            //         $("#mynavbar").removeClass('nav-shadow');
            //     }
    // });
    $(window).resize(function () 
    {
        if($(window).width() <= 700)
        {
            setTimeout(function(){
                $("#mynavigationBar").css({"height" : "0px"});
            },50);

            if(reloaded == true)
            {
                console.log("2");
                window.location.reload("true");
                reloaded = false;
            }
        }
        else
        {
            $("#mynavigationBar").css("width" ,"10  rem");
            $("#mynavigationBar").css({"height" : $(window).height()+20+"px"});
            reloaded = true;

        }

        
    });

    $("#mynavigationBar").height( $(document).height());
    
    
    
    let lastActiveItem = $("#item1");
    $(".mynavigationItem").click(function(){
        $(lastActiveItem).removeClass("activeItem");
        $(this).addClass("activeItem");
        lastActiveItem = $(this);
    });
    

    
    let position = false;
    let navBarWidth = $("#mynavigationBar").css("width");
    let btn = document.querySelector('#mytoggleButton');
    
    $("#mytoggleButton").click(function()
    {      
        
        if(position)
        {

            btn.classList.remove('cross');
            setTimeout(function () { btn.classList.remove('open') }, 200);
            
            
            if($(window).width()<=700)
            {
                $("#mynavigationBar").css({"height" : "0px"});

            }
            else
            {
                
                $("#mynavigationBar").animate({"width" : navBarWidth },10);

                
                setTimeout(function()
                {
                    $(".mynavigationItemName").css("display" , "flex" );
                }, 200);
                
                setTimeout(function()
                {
                    $(".mynavigationItemName").css({
                        "transform" : "translateX(0%)",
                        "opacity" : "1"
                    });
                } , 300);

            }

            position = false;
            

        }
        else
        {
            console.log("opened");
            btn.classList.add('open');
            setTimeout(function () { btn.classList.add('cross') }, 200);
            
            if($(window).width()<=700)
            {
                setTimeout(function(){
                    // $("#mynavigationBar").animate({"height" : $(window).height()+20+"px"},800);
                    $("#mynavigationBar").css({"height" : $(window).height()+20+"px"});
                },50);
            }
            else
            {
                navBarWidth = $("#mynavigationBar").css("width");

                
                $(".mynavigationItemName").css({
                    "transform" : "translateX(-300%)",
                    "opacity" : "0"
                });

                setTimeout(function(){
                    $(".mynavigationItemName").css("display" , "none" );
                    $("#mynavigationBar").css("width" , "max-content" );

                } , 320);
            }
            
            position = true;            
        }

    });

</script>

<script>

    function logoutUser(){
    
        let xhrObject = new XMLHttpRequest();
        xhrObject.open("POST", "../../Server/server.php");
        xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        let credentials = {
            "task" :"logout", 
            "userId" : document.getElementById("userId").textContent
        };
        xhrObject.onload = function(){
            
            if( this.status != 200 ){
                alert("Something Went Wrong!");
            }
            else{
                window.location = "../../PHP/Logout.php";
            }
        };
        xhrObject.send("request="+JSON.stringify(credentials));
    }
       
    document.getElementById('logout').addEventListener('click', logoutUser);


</script>




<?php
    }
?>