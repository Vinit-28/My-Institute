<?php

// Checking the Authenticity of the User //
session_start();

require "../../Server/Utilities/DatabaseConfigurations.php";
require "../../Server/Utilities/UserUtilities.php";

$databaseConnectionObject = get_DatabaseConnectionObject("App_Database");

if (!(isset($_SESSION["isUserLogedIn"]) && isset($_SESSION["userId"]) && isset($_SESSION["sessionId"]) && (isUserOnline($databaseConnectionObject, $_SESSION["userId"], $_SESSION["sessionId"])))) {

    session_destroy();
    header('Location: ../../index.php');
} else {
    $_SESSION['userProfile'] = getUserProfilePath($databaseConnectionObject, $_SESSION['instituteId'], $_SESSION['userId']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <link rel="stylesheet" href="../CSS/quizApp.css">
    <link rel="stylesheet" href="../CSS/library.css">
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
    <link rel="stylesheet" href="../CSS/uploadAssignment.css">

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <title>Student Dashboard</title>
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
                <li class="navIcons">
                    <?php echo "<img src='" . $_SESSION['userProfile'] . "' alt=''>"; ?>
                </li>
            </ul>

        </div>

    </nav>


    <div id="mydashboardContainer">

        <div id="mynavigationBar">
            <div class="mynavigationItem  activeItem" id="item1" value="#dashboard">
                <i class='mynavigationItemIcon bx bx-home'></i>
                <span class="mynavigationItemName">Dashboard</span>
            </div>
            <div class="mynavigationItem" value="#studentProfileDiv">
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
            <div class="mynavigationItem" value="#uploadAssignment">
                <i class='mynavigationItemIcon bx bx-cloud-upload'></i>
                <span class="mynavigationItemName">Submit Assignments</span>
            </div>
            <div class="mynavigationItem" value="#div7">
                <i class='mynavigationItemIcon bx bxs-book-content'></i>
                <span class="mynavigationItemName">Mock Test</span>
            </div>
            <div class="mynavigationItem" value="#div8">
                <i class='mynavigationItemIcon bx bxs-user-detail'></i>
                <span class="mynavigationItemName">Resume Maker</span>
            </div>
            <div class="mynavigationItem" value="#library">
                <i class='mynavigationItemIcon bx bx-book-open'></i>
                <span class="mynavigationItemName">Library</span>
            </div>
            <div class="mynavigationItem" value="#studentExam">
                <i class='mynavigationItemIcon bx bxs-edit-alt'></i>
                <span class="mynavigationItemName">Student Test</span>
            </div>
        </div>








        <!-- Student Dash Board  -->
        <div id="dashboard" class="formsDiv">

            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Good Afternoon Vinit</h3>
            </div>

            <div id="progressContainer">

                <div class="individualProgressCard">
                    <div class="progressCardHeading">Fees Details</div>
                    <div class="progressHolder">

                        <div class="progressCircle">
                            <div class="circle">
                                <div class="outerCircle"></div>
                                <div id="feesProgress" class="inner"></div>
                            </div>
                        </div>

                        <div id="feesDetailsContainer">
                            <div id="submitedFees"> Submitted = 25000</div>
                            <div id="remainingFees">Remaining = 25000</div>
                            <div id="remainingFees">Total = 50000</div>
                        </div>

                    </div>
                </div>

                <div class="individualProgressCard">
                    <div class="progressCardHeading">Attendance Records</div>
                    <div class="progressHolder">

                        <div class="progressCircle">
                            <div class="circle">
                                <div class="outerCircle"></div>
                                <div id="attendanceProgress" class="inner"></div>

                            </div>
                        </div>

                        <div id="feesDetailsContainer">
                            <div id="">Present = 210</div>
                            <div id="">Leaves = 8</div>
                            <div id="">Total = 280</div>
                        </div>

                    </div>
                </div>


            </div>



        </div>

        <script src="../JS/percentage.js"></script>



        <!-- Personal Profile Div -->

        <div id="studentProfileDiv" class="formsDiv" style="display: none;">
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







        <!-- Live Class Section -->

        <div id="div5" style="display: none;">
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Live Classes</h3>
            </div>
            <div>

                <div id="liveClassContainer">

                    <!-- This is a whole live class form -->
                    <!-- <div class="classItem">
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
                    </div> -->
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



        <!-- Upload Section Div -->
        <div id="uploadAssignment" class="formsDiv" style="display: none;">

            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Assignments</h3>
            </div>

            <div id="assignmentContainer">

                <!-- Assignment Card -->
                <!-- <div class="assignmentItem">
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

                        <div class="cardButtons">
                            <a href="" class="classJoinButton">Upload Assignment</a>
                        </div>
                        <div class="cardButtons">
                            <a href="" class="classJoinButton">View Submitted File</a>
                        </div>
                    </div>  -->


            </div>


        </div>
        <!-- Upload Section Div End-->





        <!-- Quiz Section Div -->

        <div id="div7" style="display: none;">

            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Quiz App</h3>
            </div>
            <form action="" class="forms" method="post">
                <select name="subject" id="subjectOfQuiz">
                    <option value="">Select the subject</option>
                    <option value="Linux">Linux</option>
                    <option value="BASH">Bash_</option>
                    <option value="PHP">PHP</option>
                    <option value="Docker">Docker</option>
                    <option value="HTML">HTML</option>
                    <option value="MySql">MySql</option>
                    <option value="Laravel">Laravel</option>
                    <option value="Javascript">Javascript</option>
                </select>
                <select name="questionNumber" id="numberOfQuestions">
                    <option value="">Select the question Numbers</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                </select>
                <button type="button" onclick="startQuiz()">Start Quiz !</button>
            </form>

            <script>
                function startQuiz() {
                    var subject = document.getElementById('subjectOfQuiz').value;
                    var limit = document.getElementById('numberOfQuestions').value;
                    if (subject == "" || limit == "") {
                        alert(subject + " -- " + limit);
                        event.preventDefault();
                    } else {
                        localStorage.setItem('subject', document.getElementById('subjectOfQuiz').value);
                        localStorage.setItem('limit', document.getElementById('numberOfQuestions').value);
                        window.open("./quizPage.html", "_blank");
                    }
                }
            </script>

        </div>

        <!-- Quiz Section Div End-->





        <!-- Resume maker Div -->

        <div id="div8" style="display: none;">
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Resume Maker</h3>
            </div>
            <div class="selectedFormsDiv">
                <form action="" method="post" class="resumeForm" style="width: 100%;">

                    <input id="name" required autocomplete="OFF" type="text" placeholder="*Your Name">
                    <input id="tag" required autocomplete="OFF" type="text" placeholder="*Tag (Eg: Student)">
                    <div style="display: flex; flex-wrap: wrap; align-items: center; column-gap: 1rem;">
                        <label for="image">Select your image</label>
                        <input type="file" id="image">

                    </div>
                    <div class="educationBlock">
                        <label class="heading" for="">Enter Contact Details</label>
                        <input id="email" required autocomplete="OFF" type="email" placeholder="*Email">
                        <input id="phone" required autocomplete="OFF" type="number" placeholder="*Phone Number">
                        <input id="address" required autocomplete="OFF" type="text" placeholder="*Street Address"
                            maxlength="50">
                        <input id="city" required autocomplete="OFF" type="text" placeholder="*City">
                        <input id="pincode" required autocomplete="OFF" type="number" placeholder="*Pin Code" size="10">
                        <input id="state" required autocomplete="OFF" type="text" placeholder="*State">
                    </div>

                    <div class="educationBlock">
                        <label class="heading" for="">Education</label>
                        <label>Senior Secondary Details</label>
                        <input required autocomplete="OFF" id="class12name" type="text" placeholder="*Institute Name">
                        <input required autocomplete="OFF" id="class12board" type="text" placeholder="*Board Name">
                        <input required autocomplete="OFF" id="class12subject" type="text" placeholder="*Subject">
                        <input required autocomplete="OFF" id="class12percent" type="number" placeholder="*Percentage">
                        <div>
                            <label for="from12">From</label>
                            <input required type="date" id="from12">
                        </div>
                        <div>
                            <label for="to12">To</label>
                            <input required type="date" id="to12">
                        </div>

                        <label>Secondary Details</label>
                        <input required autocomplete="OFF" id="class10name" type="text" placeholder="*Institute Name">
                        <input required autocomplete="OFF" id="class10board" type="text" placeholder="*Board Name">
                        <input required autocomplete="OFF" id="class10percent" type="number" placeholder="*Percentage">
                        <div>
                            <label for="from10">From</label>
                            <input required type="date" id="from10">
                        </div>
                        <div>
                            <label for="to10">To</label>
                            <input required type="date" id="to10">
                        </div>

                    </div>


                    <div class="educationBlock">
                        <label class="heading" for="">Social Profile Links</label>
                        <input class="tagname" id="github" name="github" autocomplete="OFF" type="text"
                            placeholder="Github Username">
                        <input id="githuburl" id="githuburl" disabled name="githuburl" autocomplete="OFF" type="url"
                            placeholder="Github Profile URL">

                        <input class="tagname" id="linkedin" name="linkedin" autocomplete="OFF" type="url"
                            placeholder="LinkedIn Username">
                        <input disabled id="linkedinurl" autocomplete="OFF" type="url"
                            placeholder="Linkedin Profile URL">

                    </div>



                    <div class="educationBlock">
                        <label class="heading" for="secondaryenddate">*Professional Summary</label>
                        <textarea required id="professionalsummary" placeholder="*50 Words professional summary"
                            maxlength="300" cols="30" rows="6"></textarea>
                    </div>

                    <div class="educationBlock">
                        <label class="heading">Select Your Qualities (Any 4)</label>
                        <div>
                            <input type="checkbox" onclick="count(1)" name="check" value="Fast Learner"> Fast Learner
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(2)" name="check" value="Leadership"> Leadership
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(3)" name="check" value="Communication"> Communication
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(4)" name="check" value="Focus"> Focus
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(5)" name="check" value="Integrity"> Integrity
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(6)" name="check" value="Responsibility">
                            Responsibility
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(7)" name="check" value="Punctual"> Punctual
                        </div>
                        <div>
                            <input type="checkbox" onclick="count(8)" name="check" value="Teamwork"> Teamwork
                        </div>
                    </div>


                    <div class="educationBlock">
                        <label class="heading" for="">Project Details (Optional)</label>
                        <input id="project1name" class="tagname" required autocomplete="OFF" type="text"
                            placeholder="Project 1 Name">
                        <textarea required id="project1summary" disabled placeholder="*50 Words project 1 summary"
                            maxlength="300" cols="30" rows="3"></textarea>

                        <input id="project2name" class="tagname" required autocomplete="OFF" type="text"
                            placeholder="Project 2 Name">
                        <textarea required id="project2summary" disabled placeholder="*50 Words project 2 summary"
                            maxlength="300" cols="30" rows="3"></textarea>

                        <input id="project3name" class="tagname" required autocomplete="OFF" type="text"
                            placeholder="Project 3 Name">
                        <textarea required id="project3summary" disabled placeholder="*50 Words project 3 summary"
                            maxlength="300" cols="30" rows="3"></textarea>
                    </div>


                    <div class="educationBlock">
                        <label for="">Enter the skill in the box and press Insert button to insert skill and select from
                            the dropbox and press Delete button to delete a skill ! (Maximum 10)</label>

                        <input autocomplete="OFF" type="text" name="skill" id="skillInsert"
                            placeholder="Enter your skill" maxlength="20">

                        <select name="skills" id="skillSelect">
                            <option>Skills You have selected</option>
                        </select>
                        <div>
                            <button class="clickButton" type="button" onclick="insertSkill()">Insert</button>
                            <button class="clickButton" onclick="deleteSkill()">Delete</button>
                        </div>

                    </div>

                    <button type="button" onclick="createResume()">Create Resume</button>
                </form>
            </div>

        </div>


        <!-- Resume maker Div END-->




        <!-- Library Div END-->

        <div id="library" style="display: none;">
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Student Library</h3>
            </div>
            <div id="libraryContainer">
                <form class="searchForm" style="display: flex; column-gap: 0.2rem;">
                    <input type="search" id="bookName" placeholder="Search book..." autocomplete="OFF">
                    <button id="searchbookbutton"><i class='mynavigationItemIcon bx bx-search'></i></button>
                </form>

                <div id="booksContainer">

                </div>

            </div>
        </div>
        <script src="../JS/library.js"></script>
        <!-- Library Div END-->



        <!-- Student Exam Div END-->
        <link rel="stylesheet" href="../CSS/studentExam.css">
        <div id="studentExam" class="formsDiv" style="display: none;">
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Student Exam</h3>
            </div>
            <div id="testDetails">
                <div class="testCard">
                    <div class="subjectHeading">C++</div>
                    <div class="testHostedBy">Test by: &nbsp;Tarun Sharma</div>
                    <div class="timeBetween">Test Time: 12:00 - 01:00</div>
                    <div class="dateOn">Test Date : 12/12/12</div>
                    <button value="testid" onclick="executeText()" class="startTestButton">Starta test</button>
                </div>
                <div id="testScript" style="display: none;"></div>
            </div>
        </div>

        <!-- Student Exam Div END-->




    </div>

    <p id="userId" style="display: none;"><?php echo $_SESSION['userId']; ?></p>
    <p id="sessionId" style="display: none;"><?php echo $_SESSION['sessionId']; ?></p>
    <p id="instituteId" style="display: none;"><?php echo $_SESSION['instituteId']; ?></p>
    <p id="authority" style="display: none;"><?php echo $_SESSION['authority']; ?></p>

</body>

</html>


<script src="../JS/jquery.js"></script>
<script src="../JS/main1.js"></script>
<script src="../JS/main2.js"></script>
<script src="../JS/resume.js"></script>
<script src="../JS/studentTest.js"></script>


<!-- FOR input tags -->
<script>
    $(".tagname").on("input", function () {
        if (($(this).prop("value")).length > 0)
            $($(this).next()).prop("disabled", false);
        else {
            $($(this).next()).prop("value", "");
            $($(this).next()).prop("disabled", true);
        }
    });
</script>


<script>
    $(".clickButton").click(function (e) {
        e.preventDefault();
    })

    var skills = []

    function insertSkill() {
        obj = document.getElementById("skillSelect");
        newSkill = document.getElementById("skillInsert").value;

        if (skills.length < 10) {
            if (skills.includes(newSkill))
                alert("Skill Repeated");
            else {
                var temp = document.createElement('option');
                temp.value = newSkill;
                temp.innerHTML = newSkill;
                obj.appendChild(temp);

                document.getElementById("skillInsert").value = "";
                skills.push(newSkill)
            }

        } else
            alert("Enough Skills Taken !")

    }

    function deleteSkill() {

        obj = document.getElementById("skillSelect");
        for (i = 0; i < obj.length; i++) {
            if (obj[i].value == obj.value) {
                skills.splice(
                    skills.indexOf(obj.value), 1
                )
                obj.removeChild(obj[i]);
                break;
            }
        }
    }



    var compentensies = ["", "Fast Learner", "Leadership", "Communication", "Focus", "Integrity", "Responsibility", "Punctual", "Teamwork"]

    var checkboxes = [];

    function count(value) {
        if (checkboxes.length < 4)
            if (checkboxes.includes(value) == false)
                checkboxes.push(value);
            else
                checkboxes.pop(value);
        else {
            alert("Not more values are allowed");
            $($("input[type=checkbox]").children()['prevObject'][value - 1]).prop("checked", false);
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

        $("#div8").height($(window).height() * 80 / 100);
        $("#downloads").height($(window).height() * 65 / 100);
        $("#downloadContainer").height($("#downloads").height());

        $("#uploadAssignment").height($(window).height() * 80 / 100);

        $("#library").height($(window).height() * 80 / 100);
        $("#libraryContainer").height($('#library').height() * 85 / 100);
        $("#booksContainer").height($('#libraryContainer').height() * 85 / 100);
    } else {
        $($("#mynavigationBar").children()[0]).css("margin-top", $("#mynavbar").height() * 23 / 100);

        $("#div8").height($(window).height() - 130);
        $("#downloads").height($(window).height() - 170);
        $("#downloadContainer").height($("#downloads").height() - 30);

        $("#liveClassContainer").height($(window).height() * 80 / 100);

        $("#uploadAssignment").height($(window).height() * 80 / 100);

        $("#library").height($(window).height() * 80 / 100);
        $("#libraryContainer").height($('#library').height() * 85 / 100);
        $("#booksContainer").height($('#libraryContainer').height() * 85 / 100);
    }




    $(window).resize(function () {
        if (windowWidth >= 701) {



            $("#mynavigationBar").css({
                "height": $(window).height() * 88 / 100,
                "position": "relative"
            });

            $($("#mynavigationBar").children()[0]).css("margin-top", $("#mynavbar").height() * 22 / 100);
            $("#liveClassContainer").height($(window).height() * 70 / 100);

            $("#downloads").height($(window).height() * 65 / 100);
            $("#downloadContainer").height($("#downloads").height() - 30);
        } else {


            $($("#mynavigationBar").children()[0]).css("margin-top", $("#mynavbar").height() * 23 / 100);

            $("#downloads").height($(window).height() - 170);
            $("#downloadContainer").height($("#downloads").height() - 30);

            $("#liveClassContainer").height($(window).height() * 60 / 100);
        }



        if (windowWidth <= 710 && windowWidth >= 690) {
            window.location.reload("true");
        }
        if (windowWidth <= 700 && windowWidth >= 690) {
            $("#mynavigationBar").css({
                "width": "100%"
            });
            window.location.reload("true");
        } else if (windowWidth >= 701 && windowWidth <= 710) {
            if (windowWidth >= 701 && windowWidth <= 849) {
                $("#mynavigationBar").css("width", "12rem");
            } else if (windowWidth >= 850) {
                $("#mynavigationBar").css("width", "13rem");
            }
            setTimeout(function () {
                window.location.reload("true");
            }, 100);

        }


    });



    let lastActiveItem = $("#item1");
    $("#item1").addClass('activeItem');
    // By Default TAb //


    function manipulate(person) {

        $(lastActiveItem).removeClass("activeItem");
        $($(lastActiveItem).attr("value")).css("display", "none");

        $(person).addClass("activeItem");
        $($(person).attr("value")).css("display", "block");

        console.log($(person).attr("value"));

        if ($(person).attr("value") == "#div2") {
            fillUpPersonalDetails();
        } else if ($(person).attr("value") == "#div6") {
            showDownloadFiles();
        } else if ($(person).attr("value") == "#div5") {
            showLiveClasses();
        } else if ($(person).attr("value") == "#studentProfileDiv") {
            fillUpPersonalDetails();
        }
        else if ($(person).attr("value") == "#uploadAssignment") {
            showAssignmentsTab();
        }
        lastActiveItem = $(person);
    }
    $(".mynavigationItem").click(function () {
        manipulate($(this));
        if ($(window).width() <= 700) { moveUp(); }
        if (btn.classList.contains('cross')) {
            btn.classList.remove('cross');
            setTimeout(function () {
                btn.classList.remove('open')
            }, 200);
        }
        else {
            btn.classList.remove('open');
            setTimeout(function () {
                btn.classList.remove('cross')
            }, 200);
        }
        position = !position
    });



    let position = false;
    let navBarWidth = $("#mynavigationBar").css("width");
    let btn = document.querySelector('#mytoggleButton');


    function moveLeft() {
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
    function moveRight() {
        $("#mynavigationBar").animate({
            "width": navBarWidth
        }, 10);


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
    function moveUp() {
        $("#mynavigationBar").css({
            "height": "0px"
        });
        setTimeout(function () {
            $("#mynavigationBar").css({
                "transform": "translateY(-100%)"
            });
        }, 500);
    }
    function moveDown() {
        setTimeout(function () {
            $("#mynavigationBar").css({
                "transform": "translateY(0%)"
            });
            $("#mynavigationBar").css({
                "height": $(window).height() + 20 + "px"
            });
        }, 50);

    }

    $("#mytoggleButton").click(function () {

        if (position) {

            btn.classList.remove('cross');
            setTimeout(function () {
                btn.classList.remove('open')
            }, 200);


            if ($(window).width() <= 700) { moveUp() }
            else { moveRight() }

            position = false;


        }
        else {

            btn.classList.add('open');
            setTimeout(function () {
                btn.classList.add('cross')
            }, 200);


            if ($(window).width() <= 700) { moveDown() }
            else { moveLeft() }

            position = true;
        }

    });

</script>

<script>
    function logoutUser() {

        let xhrObject = new XMLHttpRequest();
        xhrObject.open("POST", "../../Server/server.php");
        xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        let credentials = {
            "task": "logout",
            "userId": document.getElementById("userId").textContent
        };
        xhrObject.onload = function () {

            if (this.status != 200) {
                alert("Something Went Wrong!");
            } else {
                window.location = "../../PHP/Logout.php";
            }
        };
        xhrObject.send("request=" + JSON.stringify(credentials));
    }

    document.getElementById('logout').addEventListener('click', logoutUser);
</script>


<?php
}
?>