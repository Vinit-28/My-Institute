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
    $_SESSION['userProfile'] = getUserProfilePath($databaseConnectionObject, $_SESSION['instituteId'], $_SESSION['userId']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/mynavbar.css">
    <link rel="stylesheet" href="../CSS/mynavigationbar.css">
    <link rel="stylesheet" href="../CSS/mainPage.css">
    <link rel="stylesheet" href="../CSS/div2.css">
    <link rel="stylesheet" href="../CSS/div8.css">
    <link rel="stylesheet" href="../CSS/progress.css">
    <link rel="stylesheet" href="../CSS/formsCss.css">
    <link rel="stylesheet" href="../CSS/downloads.css">
    <link rel="stylesheet" href="../CSS/quizApp.css">
    <link rel="stylesheet" href="../CSS/studentProfileImg.css">
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
                <li class="navIcons"><?php echo "<img src='" . $_SESSION['userProfile'] . "' alt=''>";?></li>
            </ul>

        </div>

    </nav>


    <div id="mydashboardContainer">

        <div id="mynavigationBar">
            <div class="mynavigationItem  activeItem" id="item1">
                <i class='mynavigationItemIcon bx bx-home'></i>
                <span class="mynavigationItemName">Dashboard</span>
            </div>
            <div class="mynavigationItem" value="#div2">
                <i class='mynavigationItemIcon bx bxs-user'></i>
                <span class="mynavigationItemName">Profile</span>
            </div>
            <div class="mynavigationItem" value="#div5">
                <i class='mynavigationItemIcon bx bx-laptop'></i>
                <span class="mynavigationItemName">Live Class</span>
            </div>
            <div class="mynavigationItem" value="#div6">
                <i class='mynavigationItemIcon bx bx-download'></i>
                <span class="mynavigationItemName">Download</span>
            </div>
            <div class="mynavigationItem" value="#div7">
                <i class='mynavigationItemIcon bx bxs-book-content' ></i>
                <span class="mynavigationItemName">Quizz App</span>
            </div>
            <div class="mynavigationItem" value="#div8">
                <i class='mynavigationItemIcon bx bxs-user-detail'></i>
                <span class="mynavigationItemName">Resume Maker</span>
            </div>
        </div>






            <!-- Personal Profile Div -->
        
            <div id="studentProfileDiv"  class="formsDiv" style="display: none;">
                    <div class="boxHeadingDiv">
                <h3 class="boxHeading">Personl Profile</h3>
                </div>
                <div id="">
                    <?php echo '<img id="studentProfileImg" src="' . $_SESSION['userProfile'] . '" alt="">'; ?>
                </div>
                <form id="studentProfileForm" action="" method="post" class="forms">
                    <input autocomplete="off" id="personalPersonId" type="text" placeholder="*Student ID">
                    <input id="newProfile" type="file">
                    <input autocomplete="off" id="personalName" type="text" placeholder="Student Name">

                    <input required autocomplete="off" id="personalEmail" type="email" placeholder="Student Email">

                    <input autocomplete="off" id="personalPhoneNo" type="number" placeholder="Student Phone No.">
                    <input autocomplete="off" id="personalAddress" type="text" placeholder="Student Address">
                    <input autocomplete="off" id="personalCity" type="text" placeholder="Student City">
                    <input autocomplete="off" id="personalState" type="text" placeholder="Student State">
                    <input autocomplete="off" id="personalPinCode" type="number" placeholder="PIN Code">
                    <button type="submit" id="updatePersonalDetails">Update Profile</button>
                </form>
            </div>

            <!-- Personal Profile Div END -->









        <!-- <div id="div2" style="display: none;">
            <div id="selectedPersonProfile" class="selectedFormsDiv" style="width: 100%;">
                <div id="profileimgdiv">
                    <div id="selectedImgContainer" onclick="file.click()" ondragdrop="file.dragdrop()">
                        <?php echo "<img id='selectedImg' src='" . $_SESSION['userProfile'] . "' alt=''>";?>
                        <div id="selectedImgCamera"><i class='bx bxs-camera' style="margin-top: 0.3rem;"></i></div>
                    </div>
                    <div id="studentName">Aman Khushalani</div>
                </div>
                <form action="" method="post" class="forms">
                    <input type="file" name="newImage" id="file" style="display:none"/>


                    <input required autocomplete="off" name="personID" type="text" placeholder="*Person ID" disabled>
                    <input required autocomplete="off" name="personEmail" type="email" placeholder="*Person Email">
                    <input required autocomplete="off" name="personEmail" type="text" placeholder="*Person Class" disabled>
                    
                    <select id="gender" name="personGender">
                        <option class="options" value="" selected="selected">Gender</option>
                        <option class="options" value="male">Male</option>
                        <option class="options" value="female">Female</option>
                        <option class="options" value="other">Other</option>
                    </select>
                    <input required autocomplete="off" name="personDesignation" type="text" placeholder="Designation = Student" disabled>
                    
                    <input required autocomplete="off" name="personPhone" type="number" placeholder="*Phone No.">
                    <input required autocomplete="off" name="personAadhar" type="number" placeholder="*Aadhar Card No." disabled>
                    <input required autocomplete="off" name="personAddress" type="text" placeholder="*Address">
                    <input required autocomplete="off" name="personCity" type="text" placeholder="*City">
                    <input required autocomplete="off" name="personState" type="text" placeholder="*State">
                    <input required autocomplete="off" name="personPin" type="number" placeholder="*PIN Code">
                    <button type="submit" name="submitPerson">Update Details</button>
                </form>
            </div>


        </div> -->


        <!-- Live Class Section -->

        <div id="div5" style="display: none;">
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Live Classes</h3>
            </div>
            <div>

                <div id="liveClassContainer">

                    <!-- This is a whole live class form -->
                    <div class="classItem">
                        <div class="classSelector">
                            <div class="classHeading">C++</div>
                            &nbsp;&nbsp;&nbsp;
                            <div class="hostName">(Tarun Sharma)</div>
                        </div>
                        <div class="classDescription">
                            <div class="classTitle">Oop's</div>
                            <ul class="classSubtopics">
                                <p>Polymorphism, Encapsulation , Objects</p>
                                <div class="classDate">Date :- 25-Nov-2021</div>
                                <div class="classTime">Timing :- 10:00 AM to 11:00 AM</div>
                            </ul>
                        </div>

                        <div style=" display: flex; text-align: center; justify-content: center;">
                            <a href="" class="classJoinButton">Join Class</a>
                        </div>
                    </div>
                    <!-- This is a whole live class form End-->
                    
                </div>

            </div>

        </div>

        <!-- Live Class Section END -->


        <!-- Download Section Div -->
        
        <div id="div6" style="display: none;">
            
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Downloads</h3>
            </div>
            <div id="downloads">
                <div id="downloadContainer">
                    
                    <div class="containerItem">
                        <a href="">File 1</a>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Download Section Div End-->


        <!-- Download Section Div -->
        
        <div id="div7" style="display: none;">
            
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Quiz App</h3>
            </div>
            <form action="quizApp.html" class="forms" method="post">
                <select name="subject" id="">
                    <option value="">Select the subject</option>
                    <option value="c++">C++</option>
                    <option value="c++">Java</option>
                    <option value="c++">Python3/option>
                    <option value="c++">HTML</option>
                    <option value="c++">CSS</option>
                    <option value="c++">Javascript</option>
                    <option value="c++">Reasoning</option>
                </select>
                <select name="questionNumber" id="">
                    <option value="">Select the question Numbers</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                </select>
                <button type="submit">Start Quiz !</button>
            </form>
            
            
        </div>
        
        <!-- Download Section Div End-->
        
        
        
        <!-- Resume maker Div -->

        <div id="div8" style="display: none;">
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Resume Maker</h3>
            </div>
            <div class="selectedFormsDiv">
                <form action="resume.php" method="post" class="resumeForm" style="width: 100%;">
                    
                    <input name="name" required autocomplete="OFF" type="text" placeholder="*Your Name">
                    <input name="tag" required autocomplete="OFF" type="text" placeholder="*Tag (Eg: Student)">
                    <div style="display: flex; flex-wrap: wrap; align-items: center; column-gap: 1rem;">
                        <label for="image">Select your image</label>
                        <input type="file">

                    </div>
                    <div class="educationBlock">
                        <label class="heading" for="">Enter Contact Details</label>
                        <input name="email" required autocomplete="OFF" type="email" placeholder="*Email">
                        <input name="pno" required autocomplete="OFF" type="number" placeholder="*Phone Number" >
                        <input name="address" required autocomplete="OFF" type="text" placeholder="*Street Address" maxlength="50">
                        <input name="city" required autocomplete="OFF" type="text" placeholder="*City">
                        <input name="pincode" required autocomplete="OFF" type="number" placeholder="*Pin Code" size="10">
                        <input name="state" required autocomplete="OFF" type="text" placeholder="*State">
                    </div>

                    <div class="educationBlock">
                        <label class="heading" for="">Social Profile Links</label>
                        <input class="tagname" id="github"    name="github"   autocomplete="OFF" type="text" placeholder="Github Username">
                        <input id="githuburl" disabled name="githuburl"   autocomplete="OFF" type="url" placeholder="Github Profile URL">
                        
                        <input class="tagname" id="linkedin" name="linkedin" autocomplete="OFF" type="url" placeholder="LinkedIn Username">
                        <input  disabled name="linkedinurl"   autocomplete="OFF" type="url" placeholder="Linkedin Profile URL">
                        
                        <input class="tagname" id="" name="codechefs"autocomplete="OFF" type="url" placeholder="CodeChefs Username">
                        <input   disabled name="codechefsurl"   autocomplete="OFF" type="url" placeholder="CodeChefs Profile URL">
                    </div>



                    <div class="educationBlock">
                        <label class="heading" for="secondaryenddate">*Professional Summary</label>
                        <textarea required name="professionalsummary" placeholder="*50 Words professional summary" maxlength="300"  cols="30" rows="6" ></textarea>
                    </div>
                    
                    <div class="educationBlock">
                        <label class="heading" >Select Your Qualities (Any 4)</label>
                        <div>
                            <input type="checkbox" onclick="count(1)" name="check" value="Fast Learner" >  Fast Learner
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(2)" name="check" value="Leadership" >  Leadership
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(3)" name="check" value="Communication" >  Communication
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(4)" name="check" value="Focus" >  Focus
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(5)" name="check" value="Integrity" >  Integrity
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(6)" name="check" value="Responsibility" >  Responsibility
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(7)" name="check" value="Punctual" >  Punctual
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(8)" name="check" value="Teamwork" >  Teamwork
                        </div>
                    </div>


                    <div class="educationBlock">
                        <label class="heading" for="">Project Details (Optional)</label>
                        <input name="project1Name" class="tagname" required autocomplete="OFF" type="text" placeholder="Project 1 Name">
                        <textarea required name="project1summary" disabled  placeholder="*50 Words project 1 summary" maxlength="300"  cols="30" rows="3" ></textarea>
                        
                        <input name="project2Name" class="tagname" required autocomplete="OFF" type="text" placeholder="Project 2 Name">
                        <textarea required name="project2summary" disabled  placeholder="*50 Words project 2 summary" maxlength="300"  cols="30" rows="3" ></textarea>
                        
                        <input name="project3Name" class="tagname" required autocomplete="OFF" type="text" placeholder="Project 3 Name">
                        <textarea required name="project3summary" disabled  placeholder="*50 Words project 3 summary" maxlength="300"  cols="30" rows="3" ></textarea>
                    </div>


                    <div class="educationBlock">
                        <label for="">Enter the skill in the box and press Insert button to insert skill and select from the dropbox and press Delete button to delete a skill ! (Maximum 10)</label>
                        
                        <input autocomplete="OFF" type="text" name="skill" id="skillInsert" placeholder="Enter your skill" maxlength="20">
                        
                        <select name="skills" id="skillSelect">
                            <option>Skills You have selected</option>
                        </select>
                        <div>
                            <button class="clickButton" type="button" onclick="insertSkill()">Insert</button>
                            <button class="clickButton" onclick="deleteSkill()">Delete</button>
                        </div>

                    </div>

                    <button type="submit" name="createResume">Create Resume</button>
                </form>
            </div>

        </div>
        
        <!-- Resume maker Div END-->

    </div>

    <p id="userId" style="display: none;"><?php echo $_SESSION['userId']; ?></p>
    <p id="sessionId" style="display: none;"><?php echo $_SESSION['sessionId']; ?></p>
    <p id="instituteId" style="display: none;"><?php echo $_SESSION['instituteId']; ?></p>
    <p id="authority" style="display: none;"><?php echo $_SESSION['authority']; ?></p>

</body>

</html>


<script src="../JS/jquery.js"></script>
<script src="../JS/main1.js"></script>


<!-- FOR input tags -->
<script>
        
        $(".tagname").on("input" , function(){
            if(($(this).prop("value")).length > 0)
                $($(this).next()).prop("disabled" , false);
            else
            {
                $($(this).next()).prop("value" , "");
                $($(this).next()).prop("disabled" , true);
            }
        });
</script>


<script>

    $(".clickButton").click(function(e){
        e.preventDefault();
    })

    var skills = []
    function insertSkill()
    {
        obj = document.getElementById("skillSelect");
        newSkill = document.getElementById("skillInsert").value;

        if(skills.length < 10)
        {
            if(skills.includes(newSkill))
                alert("Skill Repeated");
            else
            {
                var temp = document.createElement('option');
                temp.value =  newSkill;
                temp.innerHTML = newSkill;
                obj.appendChild(temp);
                
                document.getElementById("skillInsert").value = "";                
                skills.push(newSkill)
            }

        }
        else
            alert("Enough Skills Taken !")

    }

    function deleteSkill()
    {

        obj = document.getElementById("skillSelect");
        for(i=0 ; i < obj.length ; i++)
        {
            if(obj[i].value == obj.value)
            {
                skills.splice(
                    skills.indexOf(obj.value) , 1
                )
                obj.removeChild(obj[i]);
                break;
            }
        }
    }



    var compentensies = ["","Fast Learner" , "Leadership" , "Communication" , "Focus" , "Integrity" , "Responsibility"
        , "Punctual" , "Teamwork"]

    var checkboxes = [];

    function count(value){
        if(checkboxes.length < 4)
            if(checkboxes.includes(value) == false)
                checkboxes.push(value);
            else
                checkboxes.pop(value);
        else
        {
            alert("Not more values are allowed");
            $($("input[type=checkbox]").children()['prevObject'][value-1]).prop("checked" , false);
        }
        
    }
</script>


<script>


    let windowWidth = $(window).width();

    $("#mynavigationBar").height($(window).height());

    if (windowWidth >= 701) {

        $($("#mynavigationBar").children()[0]).css("margin-top", $("#mynavbar").height() * 22 / 100);
        $("#mynavigationBar").height($(window).height() * 88 / 100);
        $("#liveClassContainer").height($(window).height() * 70 / 100);

        $("#div8").height($(window).height() * 80/100);
        $("#downloads").height($(window).height() * 65 / 100);
        $("#downloadContainer").height($("#downloads").height());
    }
    else {
        $($("#mynavigationBar").children()[0]).css("margin-top", $("#mynavbar").height() * 23 / 100);
        
        $("#div8").height($(window).height() - 130);
        $("#downloads").height($(window).height() - 170);
        $("#downloadContainer").height($("#downloads").height() -30 );
        
        $("#liveClassContainer").height($(window).height() * 60 / 100);
    }




    $(window).resize(function () 
    {
        if (windowWidth >= 701){

            
            
            $("#mynavigationBar").css({ "height": $(window).height() * 88 / 100, "position": "relative" });

            $($("#mynavigationBar").children()[0]).css("margin-top", $("#mynavbar").height() * 22 / 100);
            $("#liveClassContainer").height($(window).height() * 70 / 100);

            $("#downloads").height($(window).height() * 65 / 100);
            $("#downloadContainer").height($("#downloads").height() - 30);
        }
        else {

            
            $($("#mynavigationBar").children()[0]).css("margin-top", $("#mynavbar").height() * 23 / 100);

            $("#downloads").height($(window).height() - 170);
            $("#downloadContainer").height($("#downloads").height() - 30);

            $("#liveClassContainer").height($(window).height() * 60 / 100);
        }



        if (windowWidth <= 700 && windowWidth >= 690) 
        {
            $("#mynavigationBar").css({"width": "100%" });
            window.location.reload("true");
        }
        else if (windowWidth >= 701 && windowWidth <= 710) 
        {
            if (windowWidth >= 701 && windowWidth <= 849 ) 
            {
                $("#mynavigationBar").css("width", "12rem");
            }
            else if (windowWidth >= 850) 
            {
                $("#mynavigationBar").css("width", "13rem");
            }
            setTimeout(function(){
                window.location.reload("true");
            },100);
            
        }


    });



    let lastActiveItem = $("#item1");

    function manipulate(person) {

        $(lastActiveItem).removeClass("activeItem");
        $($(lastActiveItem).attr("value")).css("display", "none");

        $(person).addClass("activeItem");
        $($(person).attr("value")).css("display", "block");

        if( $(person).attr("value") == "#div2" ){
            fillUpPersonalDetails();
        }
        lastActiveItem = $(person);
    }
    $(".mynavigationItem").click(function () {
        manipulate($(this));
    });



    let position = false;
    let navBarWidth = $("#mynavigationBar").css("width");
    let btn = document.querySelector('#mytoggleButton');

    $("#mytoggleButton").click(function () {

        if (position) {

            btn.classList.remove('cross');
            setTimeout(function () { btn.classList.remove('open') }, 200);


            if($(window).width()<=700)
            {
                $("#mynavigationBar").css({"height" : "0px"});
                setTimeout(function(){
                    $("#mynavigationBar").css({"transform": "translateY(-100%)"});
                },500);
            }
            else {

                $("#mynavigationBar").animate({ "width": navBarWidth }, 10);


                setTimeout(function () {
                    $(".mynavigationItemName").css("display", "flex");
                }, 200);

                setTimeout(function () {
                    $(".mynavigationItemName").css({
                        "transform": "translateX(0%)",
                        "opacity": "1"
                    });
                }, 300);

            }

            position = false;


        }
        else {
            
            btn.classList.add('open');
            setTimeout(function () { btn.classList.add('cross') }, 200);
            if($(window).width()<=700)
            {
                setTimeout(function(){
                    $("#mynavigationBar").css({"transform": "translateY(0%)"});
                    $("#mynavigationBar").css({"height" : $(window).height()+20+"px"});
                },50);

            }
            else {
                navBarWidth = $("#mynavigationBar").css("width");


                $(".mynavigationItemName").css({
                    "transform": "translateX(-300%)",
                    "opacity": "0"
                });

                setTimeout(function () {
                    $(".mynavigationItemName").css("display", "none");
                    $("#mynavigationBar").css("width", "max-content");

                }, 320);
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