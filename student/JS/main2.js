


// Function to get the Uploaded assignments from the Institute's Database //
function getUploadedAssignments(asyncRequest=true){

    let uploadedAssignments = {};
    getStudentDetails();
    // Creating Some Variables //
    let data = {
        "task" : "Get Uploaded Assignments", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent, 
        "authority" : document.getElementById("authority").textContent,
        "sessionId" : document.getElementById("sessionId").textContent,
        "studentClass" : studentData.class
    };
    let onLoadFunction = function(){
        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                uploadedAssignments = response.assignments;
            }
            else{
                uploadedAssignments = {};
            }
        }
    }

    // Making the Request to the Server //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction, asyncRequest);
    return uploadedAssignments;
}



// Function to submit the Assignment //
function submitAssignment(assignmentId){

    // Hidden input tag //
    let fileInput = document.getElementById(assignmentId);

    fileInput.onchange = function(e){
        e.preventDefault();
        
        // If the student is sure to submit the Assignment //
        if( confirm("Are you sure want to submit assignment ? ") ){
            
            // Creating the Data Variables //
            let data = {
                "task" : "Submit Assignment", 
                "loggedInUser" : document.getElementById("userId").textContent, 
                "instituteId" : document.getElementById("instituteId").textContent, 
                "authority" : document.getElementById("authority").textContent,
                "sessionId" : document.getElementById("sessionId").textContent,
                "submittedDateTime":Date(),
                "assignmentId":assignmentId,
            };

            let xhr = new XMLHttpRequest();
            let formData = new FormData();
            let submissionFile = e.target.files[0];      
            
            formData.append("request", JSON.stringify(data));   
            formData.append("submissionFile", submissionFile);
            
            xhr.timeout = 8000;
            xhr.open("POST", '../../Server/Utilities/InstituteSpecificUtilities.php'); 
            
            // Function to be executed When the request has made and got the response from the server //
            xhr.onload = function(){

                if( this.status != 200 ){
                    alert("Something Went Wrong!");
                }
                else{
                    let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
                    if( responseText.includes("Success") || responseText.includes("Failed") ){
                        let response = JSON.parse(responseText);
                        alert(response.message);
                        showAssignmentsTab();
                    }
                    else{
                        alert(responseText);
                    }
                }
            }
            xhr.send(formData);
        }
    }

    // Calling the click function to open up file dialog box to choose the submission file //
    fileInput.click();
}



// Funtion to get the Proper Date Format //
function getProperDateTime(dateObject){

    let dateTime = dateObject.toString();
    return dateTime.replace("GMT+0530 (India Standard Time)","");
}


// Function to Make a Uploaded File Card //
function getAssignmentCard(assignmentDetails){

    let assignmentItem = document.createElement("div");
    let classSelector = document.createElement("div");
    let classHeading = document.createElement("div");
    let hostName = document.createElement("div");
    let classDescription = document.createElement("div");
    let classTitle = document.createElement("div");
    let classSubtopics = document.createElement("ul");
    let pDescription = document.createElement("p");
    let classDate = document.createElement("div");
    let classTime = document.createElement("div");
    let uploadAssButtonDiv = document.createElement("div");
    let viewSubmissionButtonDiv = document.createElement("div");
    let aUploadAss =  document.createElement("a");
    let aViewSub =  document.createElement("a");
    let fileInput = document.createElement("input");

    let uploadedDateTime = new Date(assignmentDetails.uploadedDateTime);
    let deadlineTime = new Date(assignmentDetails.assignmentDeadline);


    // Adding Classes and Assigning Values to their Attributes //
    assignmentItem.classList.add("assignmentItem");
    classSelector.classList.add("classSelector");
    classHeading.classList.add("classHeading");
    hostName.classList.add("hostName");
    classDescription.classList.add("classDescription");
    classTitle.classList.add("classTitle");
    classSubtopics.classList.add("classSubtopics");
    classDate.classList.add("classDate");
    classTime.classList.add("classTime");
    uploadAssButtonDiv.classList.add("cardButtons");
    viewSubmissionButtonDiv.classList.add("cardButtons");
    aUploadAss.classList.add("classJoinButton");
    aViewSub.classList.add("classJoinButton");

    classHeading.innerText = assignmentDetails.subjectName;
    hostName.innerText = " " + "( " + assignmentDetails.uploadedBy + " )";
    classTitle.innerText = assignmentDetails.assignmentTitle;
    pDescription.innerText = assignmentDetails.assignmentDescription;
    classDate.innerText = "Uploaded Time : " + getProperDateTime(uploadedDateTime);
    classTime.innerText = "Deadline Time : " + getProperDateTime(deadlineTime);
    aUploadAss.innerText = "Upload Assignment";
    aViewSub.innerText = "View Submitted File";
    fileInput.type = "file";
    fileInput.id = assignmentDetails.assignmentId;
    fileInput.style.display = "none";
    

    // Wrapping up the Tags //
    uploadAssButtonDiv.appendChild(aUploadAss);
    uploadAssButtonDiv.appendChild(fileInput);
    viewSubmissionButtonDiv.appendChild(aViewSub);
    classSubtopics.appendChild(pDescription);
    classSubtopics.appendChild(classDate);
    classSubtopics.appendChild(classTime);
    classDescription.appendChild(classTitle);
    classDescription.appendChild(classSubtopics);
    classSelector.appendChild(classHeading);
    classSelector.appendChild(hostName);
    
    assignmentItem.appendChild(classSelector);
    assignmentItem.appendChild(classDescription);
    assignmentItem.appendChild(uploadAssButtonDiv);
    assignmentItem.appendChild(viewSubmissionButtonDiv);

    hostName.onclick = function(){
        window.open(assignmentDetails.assignmentFileLinkHref, "_blank");
    }


    aViewSub.href = assignmentDetails.submittedFileLinkHref;
    aViewSub.target = "_blank";

    if( !assignmentDetails.isSubmitted ){
        aUploadAss.onclick = ()=>{submitAssignment(assignmentDetails.assignmentId);};
        aViewSub.style.backgroundColor = "#acc9f0d6";
        aViewSub.style.pointerEvents = "none";
    }
    else{
        aUploadAss.style.backgroundColor = "#acc9f0d6";
        aUploadAss.style.pointerEvents = "none";
    }

    return assignmentItem;
}



// Function to check whether an assignment's Deadline has passed or Not //
function isAssignmentDeadlineCrossed(assignmentDeadline){
    
    let currDateTime = new Date();
    assignmentDeadline = new Date(assignmentDeadline);
    let currTime = currDateTime.getHours() + ":" + currDateTime.getMinutes();
    let deadlineTime = assignmentDeadline.getHours() + ":" + assignmentDeadline.getMinutes();

    // Cases that can come which will define that Assignment can be Submitted :-
    // 1. If Assignment Deadline is in upcoming days but not for today
    // 2. If Assignment Deadline is for today
    // 3. If Assignment Deadline is for today and the current time is lesser than the deadline time of the Assignment 

    if( currDateTime.getDate() <= assignmentDeadline.getDate() && currDateTime.getMonth() <= assignmentDeadline.getMonth() && currDateTime.getFullYear() <= assignmentDeadline.getFullYear() ){
        
        if( currDateTime.getDate() < assignmentDeadline.getDate() || currTime <= deadlineTime ) return true;
    }

    return false;
}



// Function to show the Assignments to the Student //
function showAssignmentsTab(){

    let assignmentContainer = document.getElementById("assignmentContainer");
    let uploadedAssignments = getUploadedAssignments(false);
    assignmentContainer.innerHTML = "";
    for(let key in uploadedAssignments){
        if( isAssignmentDeadlineCrossed(uploadedAssignments[key].assignmentDeadline) ){
            assignmentContainer.appendChild(getAssignmentCard(uploadedAssignments[key]));
        }
    }

    if( !assignmentContainer.children.length ){
        let mynullmessage = "No Uploaded Assignments !";
        assignmentContainer.style.color = 'red';
        assignmentContainer.style.textAlign = 'center';
        assignmentContainer.style.justifyContent = 'center';
        assignmentContainer.innerHTML = mynullmessage;
        // alert("No Uploaded Assignments !!!");
    }
}