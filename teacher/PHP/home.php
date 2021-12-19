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
    <link rel="stylesheet" href="../CSS/mynavbar.css">
    <link rel="stylesheet" href="../CSS/mynavigationbar.css">
    <link rel="stylesheet" href="../CSS/mainPage.css">
    <link rel="stylesheet" href="../CSS/searchBar.css">
    <link rel="stylesheet" href="../CSS/progress.css">
    <link rel="stylesheet" href="../CSS/formsCss.css">
    <link rel="stylesheet" href="../CSS/downloads.css">
    <link rel="stylesheet" href="../CSS/div7.css">
    <link rel="stylesheet" href="../CSS/teacherProfileDiv.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <title>Teacher Dashboard</title>
</head>

<body style="position:relative;">

    <!-- THIS IS VERY IMPORTANT BOX, IT CONTAINS THE INFORMATION OF THE SELECTED PERSON FORM SEARCH BAR (Modal) -->

    <div id="selectedPersonProfileContainer" style="display: none;">
        <div id="selectedPersonProfile" class="selectedFormsDiv">
            <div id="profileimgdiv">
                <img id="selectedImg" src="../IMAGES/profile.jpg" alt="">
            </div>
            <form id="modalForm" method="post" class="forms">
                <input required autocomplete="off" id="update-personId" type="text" placeholder="*Person ID">
                <input required autocomplete="off" id="update-name" type="text" placeholder="*Person Name">
                <input required autocomplete="off" id="update-email" type="email" placeholder="*Person Email">

                <select id="update-gender" name="personGender">
                    <option class="options" value="gender" selected="selected">Gender</option>
                    <option class="options" value="male">Male</option>
                    <option class="options" value="female">Female</option>
                    <option class="options" value="other">Other</option>
                </select>
                <select id="update-designation" name="personRole">
                    <option class="options" value="designation" selected="selected">Designation</option>
                    <option class="options" value="teacher">Teacher</option>
                    <option class="options" value="student">Student</option>
                </select>
                <select id="update-class" name="updateStudentClass">
                    <option class="options" value="Class" selected="selected">Class</option>
                </select>

                <input required autocomplete="off" id="update-phoneNo" type="number" placeholder="*Phone No.">
                <input required autocomplete="off" id="update-adharCardNo" type="number" placeholder="*Aadhar Card No.">
                <input required autocomplete="off" id="update-address" type="text" placeholder="*Address">
                <input required autocomplete="off" id="update-city" type="text" placeholder="*City">
                <input required autocomplete="off" id="update-state" type="text" placeholder="*State">
                <input required autocomplete="off" id="update-pinCode" type="number" placeholder="*PIN Code">

                <input disabled style="display: none;" required autocomplete="off" id="depositedFees" type="text"
                    placeholder="*State">
                <input disabled style="display: none;" required autocomplete="off" id="remainingFees" type="text"
                    placeholder="*PIN Code">

                <!-- <button type="button" id="updateDetails" >Update Details</button> -->
                <!-- <button type="button" id="closeModal" >Go Back</button> -->
            </form>

        </div>

    </div>


    </div>


    <!-- (END) THIS IS VERY IMPORTANT BOX, IT CONTAINS THE INFORMATION OF THE SELECTED PERSON FORM SEARCH BAR (Modal) -->


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
            <div class="mynavigationItem" id="item1" value="#teacherProfileDiv">
                <i class='mynavigationItemIcon bx bxs-user'></i>
                <span class="mynavigationItemName">Profile</span>
            </div>
            <div class="mynavigationItem" value="#div2">
                <i class='mynavigationItemIcon bx bx-search'></i>
                <span class="mynavigationItemName">Search Person</span>
            </div>
            <div class="mynavigationItem" value="#div4">
                <i class='mynavigationItemIcon bx bxs-user-rectangle'></i>
                <span class="mynavigationItemName">Upload Assignments</span>
            </div>
            <div class="mynavigationItem" value="#div5">
                <i class='mynavigationItemIcon bx bx-laptop'></i>
                <span class="mynavigationItemName">Launch Live Class</span>
            </div>
            <div class="mynavigationItem" value="#div6">
                <i class='mynavigationItemIcon bx bx-download'></i>
                <span class="mynavigationItemName">Upload Files</span>
            </div>
            <div class="mynavigationItem" value="#uploadTest">
                <i class='mynavigationItemIcon bx bxs-edit-alt'></i>
                <span class="mynavigationItemName">Upload Test</span>
            </div>
        </div>







        <!-- Personal Profile Div -->

        <div value="#teacherProfileDiv" id="teacherProfileDiv" class="formsDiv" style="display: none;">
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Personl Profile</h3>
            </div>
            <div id="">
                <?php echo '<img id="teacherProfileImg" src="' . $_SESSION['userProfile'] . '" alt="">'; ?>
            </div>
            <form id="teacherProfileForm" action="" method="post" class="forms">
                <input autocomplete="off" id="personalPersonId" type="text" placeholder="*Teacher ID">
                <input id="newProfile" type="file">
                <input autocomplete="off" id="personalName" type="text" placeholder="Teacher Name">

                <input required autocomplete="off" id="personalEmail" type="email" placeholder="Teacher Email">

                <input autocomplete="off" id="personalPhoneNo" type="number" placeholder="Teacher Phone No.">
                <input autocomplete="off" id="personalAddress" type="text" placeholder="Teacher Address">
                <input autocomplete="off" id="personalCity" type="text" placeholder="Teacher City">
                <input autocomplete="off" id="personalState" type="text" placeholder="Teacher State">
                <input autocomplete="off" id="personalPinCode" type="number" placeholder="PIN Code">
                <button type="submit" id="updatePersonalDetails">Update Profile</button>
            </form>
        </div>

        <!-- Personal Profile Div END -->















        <div id="div2" style="display: none;">
            <div id="searchDiv">

                <form id="searchForm" action="" method="POST" style="display: flex; column-gap: 0.2rem;">
                    <input type="search" name="search" id="searchKey" placeholder="Search person..." autocomplete="OFF">
                    <button id="searchPerson" type="submit"><i class='mynavigationItemIcon bx bx-search'></i></button>
                </form>

                <div id="searchResults" style="display: none;">

                    <!-- This is for when a person is found ! -->
                    <!-- <div class="suggestedPerson">
                            <div class="suggestedPersonProfile"><img src="../IMAGES/profile.jpg" alt=""></div>
                            <div class="suggestedPersonDetails">
                                <span class="suggestedPersonID">1001</span>
                                <span class="suggestedPersonName">Aman Khushalani</span>
                                <span class="suggestedPersonDept">BCA 3rd. Yr. (Student)</span>
                            </div>
                        </div> -->

                    <!-- OR OR OR OR OR -->

                    <!-- This is for else part if could not found anything ! -->
                    <!-- <div id="notFound" style="color: red;">Couldn't find anything ! (Try again using relevant keywords.)
                        </div> -->
                </div>

                <div id="selectedPersonProfile" class="selectedFormsDiv" style="width: 100%; display: none; ">
                    <div id="modalImg">
                        <img id="selectedImg" src="../IMAGES/profile.jpg" alt="">
                    </div>
                    <form action="" method="post" class="forms">
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
                        <input required autocomplete="off" name="personAadhar" type="number"
                            placeholder="*Aadhar Card No.">
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


        <!-- Upload Assignments Div -->

        <div id="div4" about="uploadAssignments" class="formsDiv" style="display:none;">
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Upload Assignment</h3>
            </div>
            <div id="">
                <button id="uploadAssignment" style="margin: 0.4rem 0.2rem 0.4rem 0.2rem; font-weight:bold"
                    class="downloadOptions">Upload Assignment</button>
                <button id="showAssignments" style="margin: 0.4rem 0.2rem 0.4rem 0.2rem; font-weight:bold"
                    class="downloadOptions">Show Assignments</button>
                <button id="deleteAssignments" style="margin: 0.4rem 0.2rem 0.4rem 0.2rem; font-weight:bold"
                    class="downloadOptions">Delete Assignments</button>
                <button id="updateAssignment" style="margin: 0.4rem 0.2rem 0.4rem 0.2rem; font-weight:bold"
                    class="downloadOptions">Update Assignment</button>
            </div>

            <div id="uploadAssignmentContainer">
                <!-- Upload New Assignment Form -->

                <!-- <form action="" class="forms">
                        <input required autocomplete="OFF" type="text" placeholder="Uploaded By"       >
                        <input required autocomplete="OFF" type="text" placeholder="Subject Name"      >
                        <input required autocomplete="OFF" type="text" placeholder="Assignment Title"  >
                        <input required autocomplete="OFF" type="text" placeholder="Topic Description" >
                        <div class="timeDiv">
                            <label for="">Upload File here--</label>
                            <input type="file" >
                        </div>
                        <div class="timeDiv">
                            <label for="">Deadline Date </label>
                            <input required autocomplete="OFF" type="time" name="assignmentDeadline">
                        </div>
    
                        <select name="classsForClass">
                            <option>Visiblie To</option>
                            <option value="">BCA 1st Yr.</option>
                            <option value="">BCA 2nd Yr.</option>
                            <option value="">BCA 3rd Yr.</option>
                        </select>
                        <button type="submit">Upload Assignment</button>
                    </form> -->
            </div>

        </div>

        <!-- Upload Assignments Div End -->






        <!-- Upload Student Test File Div -->

        <div id="uploadTest" about="uploadTestFile" class="formsDiv" style="display: none;">

            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Upload Test</h3>
            </div>

            <div id="uploadTestContainer">

                <form action="" class="forms">
                    <input required autocomplete="OFF" type="text" placeholder="Inviglator Name" name="hostName">
                    <input required autocomplete="OFF" type="text" placeholder="Subject Name" name="subjectName">
                    <input required autocomplete="OFF" type="text" placeholder="Topic Name" name="topicName">

                    <div class="timeDiv">
                        <label for="">From:- </label>
                        <input required autocomplete="OFF" type="time" name="startingTime">
                        <label for="">Till:- </label>
                        <input required autocomplete="OFF" type="time" name="endingTime">
                    </div>

                    <input required autocomplete="OFF" type="date" name="classDate" style="width: 100%;">
                    <select name="classForClass">
                        <option value="">BCA 1st Yr.</option>
                        <option value="">BCA 2nd Yr.</option>
                        <option value="">BCA 3rd Yr.</option>
                    </select>
                    <button onclick="{document.getElementById('testFile').click(); event.preventDefault();}">Upload .xls
                        file</button>
                    <input style="display: none;" id="testFile" accept=".xls" required autocomplete="OFF" type="file"
                        placeholder="Joining Link" name="classLink">
                    <button type="button">See file pattern</button>
                    <button type="submit">Upload</button>
                </form>

            </div>

        </div>

        <!-- Upload Student Test File End -->







        <!-- Live Class Section -->

        <div id="div5" style="display: none;">
            <!-- <div id="div5" > -->
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Live Classes</h3>
            </div>

            <div id="downloadOptionContainer">
                <button id="launchClass" class="downloadOptions">Launch Class</button>
                <button id="showHostedClasses" class="downloadOptions">Hosted Classes</button>
                <button id="deleteHostedClasses" class="downloadOptions">Delete Class</button>
                <button id="upcomingLiveClasses" class="downloadOptions">Upcoming Class</button>
            </div>

            <div id="liveClassSection">

                <!-- <div id="liveClassContainer"> -->

                <!-- This is a whole live class form -->
                <!-- <form class="classItem" action="" method="post">
                            
                            <div class="classSelector">
                                <input  type="checkbox" name="">
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

                            <div class="joinClassButton">
                                <a href="" class="classJoinButton">Join Class</a>
                            </div>
                            
                        </form> -->

                <!-- </div> -->


                <!-- Launch/Create Live Class Section -->
                <!-- <div id="liveClassLaunchContainer" style="display: none;">

                        <form action="" class="forms">
                            <input required autocomplete="OFF" type="text" placeholder="Host Name" name="hostName">
                            <input required autocomplete="OFF" type="text" placeholder="Subject Name" name="subjectName">
                            <input required autocomplete="OFF" type="text" placeholder="Topic Name" name="topicName">
                            <input required autocomplete="OFF" type="text" placeholder="Topic Description" name="topicDescription">

                            <div class="timeDiv">
                                <label for="">From:- </label>
                                <input required autocomplete="OFF" type="time" name="startingTime">
                                <label for="">Till:- </label>
                                <input required autocomplete="OFF" type="time" name="endingTime">
                            </div>

                            <input required autocomplete="OFF" type="date" name="classDate" style="width: 100%;">
                            <input required autocomplete="OFF" type="url" placeholder="Joining Link" name="classLink">
                            <select name="classForClass">
                                <option value="">BCA 1st Yr.</option>
                                <option value="">BCA 2nd Yr.</option>
                                <option value="">BCA 3rd Yr.</option>
                            </select>
                            <button type="submit">Upload</button>
                        </form>

                    </div> -->

            </div>

        </div>

        <!-- Live Class Section END -->


        <!-- Upload Files Section Div -->

        <div id="div6" style="display: none;">
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Upload Files</h3>
            </div>
            <div id="downloads">
                <div id="downloadOptionContainer">
                    <button id="addFileButton" class="downloadOptions">Add File</button>
                    <button id="showFilesButton" class="downloadOptions">Show Files</button>
                    <button id="deleteFilesButton" class="downloadOptions">Delete File</button>
                </div>
                <div id="downloadContainer">

                    <!-- Add File Form -->
                    <!-- <form id="addFileForm" method="post" style="display: none;">

                            <input required autocomplete="OFF" type="text" id="fileTitle" placeholder="File Title">
                            <input type="file" id="uploadedFile">
                            <select name="fileVisibility" id="fileVisibility">
                                <option selected value="all">Everyone</option>
                                <option value="All Teacher">All Teachers</option>
                                <option value="All Students">All Students</option>
                            </select>
                            <button type="submit" id="uploadButton">Upload File</button>

                        </form> -->


                    <!-- Show Download File Form (Checkboxes)-->
                    <!-- <form id="downloadFileForm" action="" method="post"> -->
                    <!-- Upload File Card -->
                    <!-- <div class="containerItem">
                                <input type="checkbox" name="" id="">
                                <a href="">File 1 <sup>Uploaded by Root</sup> </a>
                            </div>
                        </form> -->

                </div>

            </div>

        </div>

        <!-- Upload Files Section Div End -->










        <!-- add class Section Div -->

        <div id="div7" style="display: none;">
            <div class="boxHeadingDiv">
                <h3 class="boxHeading">Class</h3>
            </div>
            <div id="addclasssection">
                <div id="addclasssectionContainer">
                    <button id="addClass" class="downloadOptions">Add Class</button>
                    <button id="showClass" class="downloadOptions">Show Class</button>
                    <button id="deleteClass" class="downloadOptions">Delete Class</button>
                    <button id="updateClass" class="downloadOptions">Update Class</button>
                </div>

                <div id="ClassContainer">

                    <!-- Add Class Form -->
                    <!-- <form id="addClassForm" action="" method="post" style="display: none;">

                            <input required id="className-add" required autocomplete="OFF" type="text" placeholder="Class Name">
                            <input required id="fees-add" required autocomplete="OFF" type="text" placeholder="Class Fees">
                            <button class="addClassButton" type="submit" id="addClass-save" >Create Class</button>

                        </form> -->


                    <!-- Show Class Form -->
                    <!-- <form id="showClassForm" action="" method="post" style="display: none;"> -->

                    <!-- Class Card -->
                    <!-- <div class="containerItem">
                                <input type="checkbox" name="" id="classname">
                                <p>Class Name &nbsp;&nbsp;&nbsp;CLass Fees</p>
                            </div> -->

                    <!-- </form> -->


                    <!-- Update Class Form -->
                    <!-- <form id="updateClassForm" action="" method="post" style="display: none;">
                            <input required id="className-update" required autocomplete="OFF" type="text" placeholder="Updated Class Name">
                            <input required id="fees-update" required autocomplete="OFF" type="text" placeholder="Updated Class Fees">
                            <button class="saveUpdatedClassInfo" type="submit" id="saveUpdatedClassInfo" >Save Changes</button>
                        </form> -->

                </div>

            </div>

        </div>
        <!-- add class Section Div end -->


    </div>


    <p id="userId" style="display: none;">
        <?php echo $_SESSION['userId']; ?>
    </p>
    <p id="sessionId" style="display: none;">
        <?php echo $_SESSION['sessionId']; ?>
    </p>
    <p id="instituteId" style="display: none;">
        <?php echo $_SESSION['instituteId']; ?>
    </p>
    <p id="authority" style="display: none;">
        <?php echo $_SESSION['authority']; ?>
    </p>

</body>

</html>

<script src="../JS/jquery.js"></script>
<script src="../JS/main1.js"></script>
<script src="../JS/main2.js"></script>

<script>
    let windowWidth = $(window).width();


    $("#mynavigationBar").height($(window).height());

    if (windowWidth >= 701) {

        $($("#mynavigationBar").children()[0]).css("margin-top", $("#mynavbar").height() * 22 / 100);
        $("#mynavigationBar").height($(window).height() * 88 / 100);

        $("#downloads").height($(window).height() * 65 / 100);
        $("#downloadContainer").height($("#downloads").height() * 95 / 100);
        $("#liveClassSection").height($("#downloads").height() * 95 / 100);
        $("#searchResults").height($(window).height() * 65 / 100);
    } else {
        $($("#mynavigationBar").children()[0]).css("margin-top", $("#mynavbar").height() * 23 / 100);
        $("#downloadContainer").height($(window).height() * 60 / 100);
        $("#searchResults").height($(window).height() * 80 / 100);
    }


    $(window).resize(function () {
        if (windowWidth >= 690 && windowWidth >= 710) {
            window.location.reload("true");
        }
        if (windowWidth > 700) {
            if (windowWidth <= 849 && windowWidth >= 701) {
                $("#mynavigationBar").css("width", "12rem");
            } else {
                $("#mynavigationBar").css("width", "13rem");
            }
            $("#mynavigationBar").css({
                "height": $(window).height() * 88 / 100,
                "position": "relative"
            });
            $("#searchResults").height($(window).height() - ($(window).height() * 35 / 100));
        }



    });


    let lastActiveItem = $("#item1");
    $("#item1").addClass("activeItem");
    fillUpPersonalDetails();

    function manipulate(person) {

        $(lastActiveItem).removeClass("activeItem");
        $($(lastActiveItem).attr("value")).css("display", "none");

        $(person).addClass("activeItem");
        $($(person).attr("value")).css("display", "block");

        lastActiveItem = $(person);

        if ($(person).attr("value") == "#teacherProfileDiv") {
            fillUpPersonalDetails();
        }
        else if ($(person).attr("value") == "#div4") {
            // fillUpPersonalDetails();
            // openUploadNewAssignmentForm();
        }
    }
    $(".mynavigationItem").click(function () {
        manipulate($(this));
        if($(window).width()<=700){moveUp();}
        if(btn.classList.contains('cross'))
        {
            btn.classList.remove('cross');
            setTimeout(function () {
                btn.classList.remove('open')
            }, 200);
        }
        else
        {
            btn.classList.remove('open');
            setTimeout(function () {
                btn.classList.remove('cross')
            }, 200);
        }
        position = !position
    });



    // Download File Button Handeler
    let currentForm = "#downloadFileForm";
    let previousForm = "#addFileForm";

    function changeForm() {
        $(currentForm).css("display", "none");
        $(previousForm).css("display", "block");
        let temp = previousForm;
        previousForm = currentForm;
        currentForm = temp;
    };
    // Download File Button Handeler End


    // Launch class Button Handeler
    let liveClassPreviousForm = "#liveClassLaunchContainer";
    let liveClassCurrentForm = "#liveClassContainer";

    function changeLiveClassForm() {
        window.open(

            'https://meet.google.com/',
            '_blank'
        );

        $(liveClassCurrentForm).css("display", "none");
        $(liveClassPreviousForm).css("display", "block");
        let temp = liveClassPreviousForm;
        liveClassPreviousForm = liveClassCurrentForm;
        liveClassCurrentForm = temp;
    };
    // Launch class Button Handeler END


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

    document.getElementById("selectedPersonProfileContainer").style.height = ($(window).height() > $(document).height()) ? $(window).height() : $(document).height() + "px";

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