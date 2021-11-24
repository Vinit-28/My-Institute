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
    <!-- <link rel="stylesheet" href="mynavbar.css"> -->
    <!-- <link rel="stylesheet" href="./mynavigationbar.css"> -->
    <!-- <link rel="stylesheet" href="./mainPage.css"> -->
    <!-- <link rel="stylesheet" href="./searchBar.css"> -->
    <!-- <link rel="stylesheet" href="./progress.css"> -->
    <link rel="stylesheet" href="../CSS/mynavbar.css">
    <link rel="stylesheet" href="../CSS/mynavigationbar.css">
    <link rel="stylesheet" href="../CSS/mainPage.css">
    <link rel="stylesheet" href="../CSS/searchBar.css">
    <link rel="stylesheet" href="../CSS/progress.css">
    <link rel="stylesheet" href="../CSS/formsCss.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <title>Root Dashboard</title>
</head>

<body>

    <nav id="mynavbar">

        <div id="mynavLeft" style="display: flex; justify-content: center; align-items: center; flex-wrap: nowrap;">
            <div id="mytoggleButton">
                <div id="mybar"></div>
            </div>
            <div style="display: flex; justify-content: center; align-items: center; flex-wrap: nowrap;">
                <img src="../IMAGES/Logo.svg" alt="">
                <span id="instituteName" class="boxHeading">My Institute</span>
            </div>
        </div>

        <div id="mynavRight">
            <ul id="mynavUl">
                <li class="navIcons bx-tada-hover"><i class='bx bxs-moon '></i></li>
                <li class="navIcons bx-tada-hover"><i class='bx bxs-bell '></i></li>
                <li class="navIcons bx-tada-hover"><i class='bx bx-exit  ' id="logout"></i></li>
                <li class="navIcons"><img src="../IMAGES/profile.jpg" alt=""></li>
            </ul>

        </div>

    </nav>


    <div id="mydashboardContainer">

        <div id="mynavigationBar">
            <div class="mynavigationItem  activeItem" id="item1">
                <i class='mynavigationItemIcon bx bx-home'></i>
                <span class="mynavigationItemName">Dashboard</span>
            </div>
            <div class="mynavigationItem" value="#div2" >
                <i class='mynavigationItemIcon bx bx-search'></i>
                <span class="mynavigationItemName">Search Person</span>
            </div>
            <div class="mynavigationItem">
                <i class='mynavigationItemIcon bx bxs-user'></i>
                <span class="mynavigationItemName">Profile</span>
            </div>
            <div class="mynavigationItem" value="#div4">
                <i class='mynavigationItemIcon bx bxs-user-rectangle'></i>
                <span class="mynavigationItemName">Add Person</span>
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

        <div id="div2" style="display: none;">
            <div id="searchDiv">

                <form id="searchForm" action="" method="POST" style="display: flex; column-gap: 0.2rem;">
                    <input type="search" name="search" id="" placeholder="Search person..." autocomplete="OFF">
                    <button type="submit"><i class='mynavigationItemIcon bx bx-search'></i></button>
                </form>
                    
                <div id="searchResults" style="display: none;">
                    
                    <!-- This is for when a person is found ! -->
                    <div class="suggestedPerson">
                        <div class="suggestedPersonProfile"><img src="../IMAGES/profile.jpg" alt=""></div>
                        <div class="suggestedPersonDetails">
                            <span class="suggestedPersonID">1001</span>
                            <span class="suggestedPersonName">Aman Khushalani</span>
                            <span class="suggestedPersonDept">BCA 3rd. Yr. (Student)</span>
                        </div>
                    </div>

                    <!-- OR OR OR OR OR -->

                    <!-- This is for else part if could not found anything ! -->
                    <div id="notFound" style="color: red;">Couldn't find anything ! (Try again using relevant keywords.)</div>
                </div>

                <div id="selectedPersonProfile"  class="selectedFormsDiv formsDiv" style="width: 100%;">
                    <div id="profileimgdiv">
                        <img id="selectedImg" src="../IMAGES/profile.jpg" alt="" >
                    </div>
                    <form action="" method="post" class="forms" id="selectedProfileFrom">
                        <input required autocomplete="off" name="personID" type="text" placeholder="*Person ID">
                        <input required autocomplete="off" name="personName" type="text" placeholder="*Person Name">
                        <input required autocomplete="off" name="personEmail" type="email" placeholder="*Person Email">
                        <input required autocomplete="off" name="personEmail" type="text" placeholder="*Person Class">
                        
                        <select id="gender" name="personGender">
                            <option class="options" value="" selected="selected">Gender</option>
                            <option class="options" value="male">Male</option>
                            <option class="options" value="female">Female</option>
                            <option class="options" value="other">Other</option>
                        </select>
                        <select id="role" name="personRole">
                            <option class="options" value="" selected="selected">Designation</option>
                            <option class="options" value="female">Teacher</option>
                            <option class="options" value="female">Student</option>
                        </select>
        
                        <input required autocomplete="off" name="personPhone" type="number" placeholder="*Phone No.">
                        <input required autocomplete="off" name="personAadhar" type="number" placeholder="*Aadhar Card No.">
                        <input required autocomplete="off" name="personAddress" type="text" placeholder="*Address">
                        <input required autocomplete="off" name="personCity" type="text" placeholder="*City">
                        <input required autocomplete="off" name="personState" type="text" placeholder="*State">
                        <input required autocomplete="off" name="personPin" type="number" placeholder="*PIN Code">
                        <button type="submit" name="submitPerson">Update Details</button>
                        <button type="submit" name="submitPerson">Go Back </button>
                    </form>
                </div>
                

            </div>

        </div>

                
        <!-- Add Person Div -->
        
        <div id="div4" about="addStudent" class="formsDiv" style="display: none;">
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Add Person Pannel</h3>
            </div>
            <form id="addstudentform" action="" method="post" class="forms">
                <input required autocomplete="off" name="personID" type="text" placeholder="*Person ID">
                <input required autocomplete="off" name="personName" type="text" placeholder="*Person Name">
                <input required autocomplete="off" name="personPassword" type="password" placeholder="*Password">
                <input required autocomplete="off" name="personEmail" type="email" placeholder="*Person Email">
                
                <select id="gender" name="personGender">
                    <option class="options" value="" selected="selected">Gender</option>
                    <option class="options" value="male">Male</option>
                    <option class="options" value="female">Female</option>
                    <option class="options" value="other">Other</option>
                </select>
                <select id="role" name="personRole">
                    <option class="options" value="" selected="selected">Designation</option>
                    <option class="options" value="female">Teacher</option>
                    <option class="options" value="female">Student</option>
                </select>

                <input required autocomplete="off" name="personPhone" type="number" placeholder="*Phone No.">
                <input required autocomplete="off" name="personAadhar" type="number" placeholder="*Aadhar Card No.">
                <input required autocomplete="off" name="personAddress" type="text" placeholder="*Address">
                <input required autocomplete="off" name="personCity" type="text" placeholder="*City">
                <input required autocomplete="off" name="personState" type="text" placeholder="*State">
                <input required autocomplete="off" name="personPin" type="number" placeholder="*PIN Code">
                <button type="submit" name="submitPerson">Submit Details</button>
            </form>
        </div>
        
        <!-- Add Person Div End -->


    </div>
        

    <p id="userId" style="display: none;"><?php echo $_SESSION['userId'];?></p>

        
</body>

</html>



<script src="../JS/jquery.js"></script>

<script>


    $("#mynavigationBar").height( $(window).height() - 100);
    if($(window).width() >= 701)
    {
        $("#searchResults").height( $(window).height() * 65/100);
        $("#selectedPersonProfile").height( $(window).height() * 65/100);
    }
    else
    {
        $("#searchResults").height( $(window).height()  * 80/100);
        // $("#selectedPersonProfile").height( $(window).height()  * 80/100);
    }
    

    let reloaded = false;
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
            $("#mynavigationBar").css("width" ,"10rem");
            $("#mynavigationBar").css({"height" : $(window).height()+20+"px"});
            reloaded = true;

        }
        if($(window).width() >= 701)
            $("#searchResults").height( $(window).height() - ($(window).height() * 35/100));

        
    });
    
    let lastActiveItem = $("#item1");

    function manipulate(person){

        $(lastActiveItem).removeClass("activeItem");
        $($(lastActiveItem).attr("value")).css("display","none");
        
        $(person).addClass("activeItem");
        $($(person).attr("value")).css("display","block");
        
        
        lastActiveItem = $(person);
    }
    $(".mynavigationItem").click(function(){
        manipulate($(this));
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
                setTimeout(function(){
                    $("#mynavigationBar").css({"transform": "translateY(-100%)"});
                },500);
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
                    $("#mynavigationBar").css({"transform": "translateY(0%)"});
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