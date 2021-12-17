


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

                console.log(this.responseText);
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
    classDate.innerText = assignmentDetails.assignmentDeadline;
    classTime.innerText = assignmentDetails.assignmentDeadline;
    aUploadAss.innerText = "Upload Assignment";
    aViewSub.innerText = "View Submitted File";
    fileInput.type = "file";
    fileInput.id = assignmentDetails.assignmentId;
    fileInput.style.display = "none";
    
    console.log(fileInput);

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




// Function to show the Assignments to the Student //
function showAssignmentsTab(){

    let assignmentContainer = document.getElementById("assignmentContainer");
    let uploadedAssignments = getUploadedAssignments(false);
    assignmentContainer.innerHTML = "";
    console.log(uploadedAssignments)
    for(let key in uploadedAssignments){
        assignmentContainer.appendChild(getAssignmentCard(uploadedAssignments[key]));
    }

    if( !assignmentContainer.children.length ){
        alert("No Uploaded Assignments !!!");
    }
}