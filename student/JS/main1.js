

// Declaring Some Global Variables //
let studentData = {};




// Function to make a AJAX request to the Server //
function makeAJAXRequest(requesType, serverUrl, data, onLoadFunction, async=true){

    // Creating the XHR Object //
    let xhrObject = new XMLHttpRequest();
    xhrObject.open(requesType, serverUrl, async);
    xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    // After getting the Response from the Server this Function will be executed //
    xhrObject.onload = onLoadFunction;

    // Making the Request //
    xhrObject.send("request="+JSON.stringify(data));
}







// ---------------------------------- Update Personal Details ---------------------------------- //


// Function to get the Isntitute Details From the Database //
function getStudentDetails(){

    let data = {
        "task" : "Get Student Data", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent, 
        "userId" : document.getElementById("userId").textContent, 
        "sessionId" : document.getElementById("sessionId").textContent,
        "authority" : document.getElementById("authority").textContent,
        "fromDate" : "01",
        "fromMonth" : "1",
        "fromYear" : "2022",
        "toDate" : "15",
        "toMonth" : "1",
        "toYear" : "2022",
        "forUser" : document.getElementById("userId").textContent,
    };

    let onLoadFunction = function(){
        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                studentData = response.studentData[0];
                console.log(studentData.studentAttendance);
            }
            else{
                alert(responseText);
            }
        }
    }

    // Making the AJAX Request //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction, false);
    return studentData;
}


// Function to fill up the Self Profile Section //
function fillUpPersonalDetails(){
    
    // Getting the Institute Data //
    studentData = getStudentDetails();
    
    // Getting the tag Elements //
    let studentProfileDiv = document.getElementById("studentProfileDiv");
    let studentId = document.getElementById("personalPersonId");
    let studentName = document.getElementById("personalName");
    let studentEmail = document.getElementById("personalEmail");
    let studentPhoneNo = document.getElementById("personalPhoneNo");
    let studentAddress = document.getElementById("personalAddress");
    let studentCity = document.getElementById("personalCity");
    let studentState = document.getElementById("personalState");
    let studentPinCode = document.getElementById("personalPinCode");


    // Assigining Values to their attributes //
    studentProfileDiv.style.display = "block";
    studentId.value = studentData.userId;
    studentId.disabled = true;
    studentName.value = studentData.name;
    studentEmail.value = studentData.email;
    studentPhoneNo.value = studentData.phoneNo;
    studentAddress.value = studentData.address;
    studentCity.value = studentData.city;
    studentState.value = studentData.state;
    studentPinCode.value = studentData.pinCode;
}


// Function to update the Self Profile Details in the Database //
function updatePersonalDetails(e){

    e.preventDefault();

    // Creating Some Variables //
    let data = {
        "task" : "Update My Profile", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent, 
        "authority" : document.getElementById("authority").textContent,
        "sessionId" : document.getElementById("sessionId").textContent,
        "updatedStudentName" : document.getElementById("personalName").value,
        "updatedStudentEmail" : document.getElementById("personalEmail").value,
        "updatedStudentPhoneNumber" : document.getElementById("personalPhoneNo").value,
        "updatedStudentAddress" : document.getElementById("personalAddress").value,
        "updatedStudentCity" : document.getElementById("personalCity").value,
        "updatedStudentState" : document.getElementById("personalState").value,
        "updatedStudentPinCode" : document.getElementById("personalPinCode").value,
    };

    
    let xhr = new XMLHttpRequest();
    let formData = new FormData();
    let profileImg = document.getElementById("studentProfileImg").src;  
    let Image = document.getElementById("newProfile");
    
    formData.append("request", JSON.stringify(data));   
    
    // If image is Selected || Profile Picture is updated //
    if( Image.files.length > 0 ){
        let profileImg = Image.files[0];      
        formData.append("profileImg", profileImg);
    }    
    

    xhr.timeout = 10000;
    xhr.open("POST", '../../Server/Utilities/InstituteSpecificUtilities.php'); 
    
    // Function to be executed When the request has made and got the response from the server //
    xhr.onload = function(){

        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                alert(response.message);
                // Reloading the Page to see the Updated changes //
                window.location.reload();
            }
            else{
                alert(responseText);
            }
        }
    }

    xhr.send(formData);
}


// Binding the updatePersonalDetails button with its handler //
document.getElementById("updatePersonalDetails").addEventListener("click", updatePersonalDetails);











// --------------------------------- Download Files --------------------------------- //


// Function to Make a card for the Download File //
function getDownloadFileCard(fileDetails, counter){


    // Creating Tags // 
    let div = document.createElement("div");
    let a = document.createElement("a");
    let sup = document.createElement("sup");

    // Assigning values to the tag's attributes //
    div.classList.add("containerItem");
    a.href = fileDetails.filePathHref;
    a.innerText = "#" + counter + " " + fileDetails.fileTitle;
    a.target = "_blank";
    sup.innerText = " ( Uploaded by " + fileDetails.uploadedBy + " )";

    // Wrapping up the tags //
    a.appendChild(sup);
    div.appendChild(a);
    return div;
}



// Function to Show Download Files //
function showDownloadFiles(){
    // Getting and Creating Tags //
    let downloads = document.getElementById("downloads");
    let downloadContainer = document.getElementById("downloadContainer");
    

    studentData = getStudentDetails();

    // Assigning values to their attributes //
    downloads.innerHTML = "";
    downloadContainer.innerHTML = "";
    downloads.appendChild(downloadContainer);

    // Creating Some Data Varaiables //
    let data = {
        "task" : "Show Uploaded Files", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent,   
        "sessionId" : document.getElementById("sessionId").textContent,
    };


    let onLoadFunction = function(){

        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);

                let counter = 1;
                for(let key in response.uploadedFiles){
                    let fileVisibility = response.uploadedFiles[key].fileVisibility.toLowerCase();
                    // Appending the Input Tags (Checkboxes) in the form //
                    if( fileVisibility == "everyone" || fileVisibility == "all students" || fileVisibility == studentData.class.toLowerCase() ){
                        downloadContainer.appendChild(getDownloadFileCard(response.uploadedFiles[key], counter));
                        counter+=1;
                    }
                }
                if(response.uploadedFiles.length == 0){
                    let mynullmessage = "No Files to Show !";
                    downloadContainer.style.color = 'red';
                    downloadContainer.style.textAlign = 'center';
                    downloadContainer.innerHTML = mynullmessage;
                    // alert("No Files to Show !!!");
                }
            }
            else{
                alert(responseText);
            }
        }
    };

    // Making AJAX Request //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction);
}










// --------------------------------- Live Classes --------------------------------- //




// Function to Check whether the Current Date and the Specified Date is Same or Not //
function isDateSame(specifiedDate){
    let currDateTime = new Date();
    specifiedDate = new Date(specifiedDate);

    return (specifiedDate.getDate() == currDateTime.getDate() && specifiedDate.getMonth() == currDateTime.getMonth() && specifiedDate.getFullYear() == currDateTime.getFullYear());
}



// Function to Check whether the Current Time is in the range of Specified Time Duration //
function isTimeInRange(startingTime, endingTime){
    
    let currDateTime = new Date();
    let currTime = currDateTime.getHours() + ":" + currDateTime.getMinutes();

    return (currTime >= startingTime && currTime <= endingTime);
}




// Function to Make a Live Class Card //
function getLiveClassCard(liveClassDetails){

    // Creating Tags //
    let classItem = document.createElement("div");
    let classSelector = document.createElement("div");
    let classSelectorSubjectName = document.createElement("div");
    let hostName = document.createElement("div");
    let classDescription = document.createElement("div");
    let classTitle = document.createElement("div");
    let classDate = document.createElement("div");
    let classTime = document.createElement("div");
    let joinClassDiv = document.createElement("div");
    let classSubtopics = document.createElement("ul");
    let pTopicDescription = document.createElement("p");
    let classJoinButton = document.createElement("a");

    
    // Assigning values to their Attributes //
    classItem.classList.add("classItem");
    classSelector.classList.add("classSelector");
    classSelectorSubjectName.classList.add("classSelector");
    hostName.classList.add("hostName");
    classItem.classList.add("classItem");
    classDescription.classList.add("classDescription");
    classDate.classList.add("classDate");
    classTime.classList.add("classTime");
    classSubtopics.classList.add("classSubtopics");
    classJoinButton.classList.add("classJoinButton");

    // style
    joinClassDiv.style.display = "flex";
    joinClassDiv.style.justifyContent = "center";
    classSelector.style.fontWeight = "bold"

    classSelectorSubjectName.innerText = liveClassDetails.subjectName; 
    hostName.innerHTML = "( " + liveClassDetails.teacherName + " )"; 
    classTitle.innerText = liveClassDetails.topicName;
    pTopicDescription.innerText = liveClassDetails.topicDescription;
    classDate.innerText = liveClassDetails.classDate;
    classTime.innerText = "Timing :- " + liveClassDetails.startingTime + " to " + liveClassDetails.endingTime;
    classJoinButton.target = "_blank";
    classJoinButton.href = liveClassDetails.joiningLink;
    classJoinButton.innerText = "Join Class";
    classJoinButton.style.textAlign = "center";


    // Wrapping up the tags //
    joinClassDiv.appendChild(classJoinButton);
    classSubtopics.appendChild(pTopicDescription);
    classSubtopics.appendChild(classDate);
    classSubtopics.appendChild(classTime);
    classDescription.appendChild(classTitle);
    classDescription.appendChild(classSubtopics);
    classSelector.appendChild(classSelectorSubjectName);
    classSelector.appendChild(hostName);

    classItem.appendChild(classSelector);
    classItem.appendChild(classDescription);
    classItem.appendChild(joinClassDiv);
    
    // Disable the Join Class Link if the current date & time is not in the range of scheduled Live Class date & time //
    if( ! (isDateSame(liveClassDetails.classDate) && isTimeInRange(liveClassDetails.startingTime, liveClassDetails.endingTime) ) ){
        classJoinButton.style.pointerEvents = joinClassDiv.style.pointerEvents = "none";
        classJoinButton.style.backgroundColor = "#76a3ddd7";
    }

    return classItem;
}




// Function to check whether a Live Class is an Upcoming Live Class or Not //
function isClassUpcomingClass(classDate, startingTime, endingTime){

    let currDateTime = new Date();
    classDate = new Date(classDate);
    let currTime = currDateTime.getHours() + ":" + currDateTime.getMinutes();

    // Cases that can come which will define it's a live class :-
    // 1. If live class is scheduled in upcoming days but not for today
    // 2. If live class is scheduled for today
    // 3. If live class is scheduled for today and the current time is lesser than the staring time of the live class 
    // 4. If live class is scheduled for today and the current time is in between the staring time of the live class and the ending time of the live class

    if( currDateTime.getDate() <= classDate.getDate() && currDateTime.getMonth() <= classDate.getMonth() && currDateTime.getFullYear() <= classDate.getFullYear() ){
        
        if( currDateTime.getDate() < classDate.getDate() || currTime <= startingTime || (currTime >= startingTime && currTime <= endingTime) ) return true;
    }

    return false;
}


// Function to Show all the Hsoted Classes //
function showLiveClasses(classFilter){

    // Getting The Live Class Section Tag //
    let liveClassContainer = document.getElementById("liveClassContainer");
    liveClassContainer.innerHTML = "";

    studentData = getStudentDetails();

    // Creating Some Variables //
    let data = {
        "task" : "Get Live Classes", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent,   
        "authority" : document.getElementById("authority").textContent,
        "sessionId" : document.getElementById("sessionId").textContent,
    };

    let onLoadFunction = function(){
        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);

                for(let key in response.liveClasses){

                    let classVisibility = response.liveClasses[key].liveClassVisibility.toLowerCase();

                    if( isClassUpcomingClass(response.liveClasses[key].classDate, response.liveClasses[key].startingTime, response.liveClasses[key].endingTime) && (classVisibility == "everyone" || classVisibility == "all students" || studentData.class.toLowerCase() == classVisibility ) ){
                        
                        liveClassContainer.appendChild(getLiveClassCard(response.liveClasses[key]));
                    }
                }
                // If No Live Classes are scheduled //
                if( response.liveClasses.length < 1 ){
                    var mynullmessage = "No Live Classes Scheduled !";
                    liveClassContainer.style.color = 'red';
                    liveClassContainer.style.display = 'flex';
                    liveClassContainer.style.justifyContent = 'center';
                    liveClassContainer.innerHTML = mynullmessage;

                    // alert("No Live Classes Scheduled !!!");
                }
            }
            else{
                alert(responseText);
            }
        }
    }

    // Making AJAX Request //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction);
}







function updatePercentages(targetID, number) {

    var target = document.getElementById(targetID);
    var counter = 0
    var ID = setInterval(function () {
        if (target.innerHTML != parseInt(number)+"%") {
            target.innerHTML = counter + "%"
            counter += 1
        }
        else {
            target.innerHTML = number + "%";
            clearInterval(ID);
        }

    }, 40);
}





function InitializeStudentDetails(){

    // Setting up the Greetings //
    let currDate = new Date();
    let greetings = document.getElementById("greetings");
    studentData = getStudentDetails();

    greetings.innerText = (currDate.getHours() < 12)? "Good Morning" : (currDate.getHours() < 17)? "Good Afternoon" : "Good Evening";
    greetings.innerText += (" " + studentData.name);
    
    

    // Seeting up the Fee and Attendance Percentages //
    document.getElementById("submitedFees").innerText = "Submitted =" + " " + studentData.feeSubmitted;
    document.getElementById("remainingFees").innerText = "Remaining =" + " " + (studentData.totalFee - studentData.feeSubmitted);
    document.getElementById("totalFees").innerText = "Total =" + " " + studentData.totalFee;
    
    let feePercentage = (studentData.feeSubmitted/studentData.totalFee) * 100;
    updatePercentages('feesProgress' , feePercentage.toFixed(1));
    updatePercentages('attendanceProgress' , 99.9);

}






InitializeStudentDetails()