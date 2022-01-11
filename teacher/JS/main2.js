
// Declaring Some Global Varibales //
let uploadedFiles = {};




// ------------------------------- Upload File ------------------------------- // 


// Function to Open Upload File Form //
function openUploadFileForm(){

    // Creating Tags //
    let downloadContainer = document.getElementById("downloadContainer");
    let form = document.createElement("form");
    let fileTitle = document.createElement("input");
    let file = document.createElement("input");
    let fileVisibility = document.createElement("select");
    let defaultOptionEveryone = document.createElement("option");
    let defaultOptionAllTeachers = document.createElement("option");
    let defaultOptionAllStudents = document.createElement("option");
    let uploadButton = document.createElement("button");


    // Setting Tags Attributes //
    downloadContainer.innerHTML = "";
    form.id = "addFileForm";
    fileTitle.placeholder = "File Title";
    fileTitle.id = "fileTitle";
    file.id = "uploadedFile";
    file.type = "file";
    fileVisibility.id = "fileVisibility";
    defaultOptionEveryone.value = defaultOptionEveryone.innerText = "Everyone";
    defaultOptionAllTeachers.value = defaultOptionAllTeachers.innerText = "All Teachers";
    defaultOptionAllStudents.value = defaultOptionAllStudents.innerText = "All Students";
    uploadButton.id = "uploadButton";
    uploadButton.type = "submit";
    uploadButton.innerText = "Upload File";


    // Wrapping up the Options and the Tags //
    fileVisibility.appendChild(defaultOptionEveryone);
    fileVisibility.appendChild(defaultOptionAllTeachers);
    fileVisibility.appendChild(defaultOptionAllStudents);
    form.appendChild(fileTitle);
    form.appendChild(file);
    form.appendChild(fileVisibility);
    form.appendChild(uploadButton);

    for(let key in instituteClasses){  
        let option = document.createElement("option");
        option.value = option.innerText =instituteClasses[key].className;
        fileVisibility.appendChild(option);
    }

    downloadContainer.appendChild(form);
    
    // Function that will make ajax request to the Server to Upload a File //
    function uploadFileToTheDatabase(e){

        e.preventDefault();

        // Creating Some Variables //
        let data = {
            "task" : "Upload File", 
            "loggedInUser" : document.getElementById("userId").textContent, 
            "instituteId" : document.getElementById("instituteId").textContent,  
            "sessionId" : document.getElementById("sessionId").textContent,
            "authority" : document.getElementById("authority").textContent,
            "fileTitle" : document.getElementById("fileTitle").value,
            "file" : document.getElementById("fileTitle").value,
            "fileVisibility" : fileVisibility.options[fileVisibility.selectedIndex].value,
            "uploadDateTime" : Date().toString(),
            "uploadedBy" : document.getElementById("userId").textContent
        };
        let xhr = new XMLHttpRequest();
        let formData = new FormData();
        let fileToBeUploaded = document.getElementById("uploadedFile").files[0];      
        
        formData.append("request", JSON.stringify(data));   
        formData.append("fileToBeUploaded", fileToBeUploaded);
        
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
                    if( response.result == "Success" ){
                        showUploadedFiles();
                    }
                }
                else{
                    alert(responseText);
                }
            }
        }
        xhr.send(formData);
    }

    // Binding the Upload File Button With uploadFileToTheDatabase Handler //
    uploadButton.addEventListener("click", uploadFileToTheDatabase);
}


// Function to Make a card for the Uploaded File //
function getUploadFileCard(fileDetails){

    // Creating Tags // 
    let div = document.createElement("div");
    let input = document.createElement("input");
    let a = document.createElement("a");
    let sup = document.createElement("sup");

    // Assigning values to the tag's attributes //
    div.classList.add("containerItem");
    input.type = "checkbox";
    input.name = "showUploadedFiles";
    input.value = fileDetails.fileId;
    a.href = fileDetails.filePathHref;
    a.innerText = " " + fileDetails.fileTitle;
    a.target = "_blank";
    sup.innerText = " ( Uploaded by " + fileDetails.uploadedBy + " )";

    // Wrapping up the tags //
    a.appendChild(sup);
    div.appendChild(input);
    div.appendChild(a);
    return div;
}



// Function to Show Uploaded Files //
function showUploadedFiles(){
    // Getting and Creating Tags //
    let downloadContainer = document.getElementById("downloadContainer");
    let form = document.createElement("form");
    
    // Assigning values to their attributes //
    downloadContainer.innerHTML = "";
    form.id = "downloadFileForm";
    downloadContainer.appendChild(form);

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

                for(let key in response.uploadedFiles){
                    let fileVisibility = response.uploadedFiles[key].fileVisibility.toLowerCase();
                    // Appending the Input Tags (Checkboxes) in the form //
                    if( fileVisibility == "everyone" || fileVisibility == "all teachers" || response.uploadedFiles[key].uploadedBy == data.loggedInUser ){
                        form.appendChild(getUploadFileCard(response.uploadedFiles[key]));
                    }
                }
                if(response.uploadedFiles.length == 0){
                    let mymessagediv = document.createElement('div')
                    mymessagediv.style.color = "red"
                    mymessagediv.style.textAlign = "center"
                    mymessagediv.style.margin = "10px auto"
                    mymessagediv.innerHTML = "No Uploaded Files to Show !"
                    form.appendChild(mymessagediv)
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



// Function to get the Selected Classes from the Form Menu (Checkboxes) //
function getSelectedItems(tagName){

    let checkboxes = document.getElementsByName(tagName);
    let selectedItems = [];
    for (var checkbox of checkboxes)
    {
        if (checkbox.checked) {
            selectedItems.push(checkbox.value);
        }
    }
    return selectedItems;
}



// Function to Delete Uploaded Files from the Institute's Database //
function deleteUploadedFiles(){

    let selectedFiles = getSelectedItems("showUploadedFiles");

    if( selectedFiles.length ){
        
        // Creating Some Data Varaiables //
        let data = {
            "task" : "Delete Uploaded Files", 
            "loggedInUser" : document.getElementById("userId").textContent, 
            "instituteId" : document.getElementById("instituteId").textContent,  
            "authority" : document.getElementById("authority").textContent,
            "sessionId" : document.getElementById("sessionId").textContent,
            "selectedFiles" : selectedFiles
        };

        let onLoadFunction = function(){

            if( this.status != 200 ){
                alert("Something Went Wrong!");
            }
            else{
                let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
                if( responseText.includes("Success") ){
                    let response = JSON.parse(responseText);
                    alert(response.message);
                    showUploadedFiles();
                }
                else{
                    alert(responseText);
                }
            }
        };
        // Making AJAX Request //
        makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction);
    }
    else{
        alert("Please Select a File to Delete !!!");
    }
}



// Binding the Upload File Buttons to their Respective Handlers //
document.getElementById("addFileButton").addEventListener("click", openUploadFileForm);
document.getElementById("showFilesButton").addEventListener("click", showUploadedFiles);
document.getElementById("deleteFilesButton").addEventListener("click", deleteUploadedFiles);






// ------------------------------- Live Classes ------------------------------- // 


// Function to Create a Form for Live Class Creation //
function getLiveClassCreatorForm(){

    // Creating Tags //
    let liveClassLaunchContainer = document.createElement("div");
    let forms = document.createElement("form");
    let hostName = document.createElement("input");
    let teacherName = document.createElement("input");
    let subjectName = document.createElement("input");
    let topicName = document.createElement("input");
    let topicDescription = document.createElement("input");
    let timeDiv = document.createElement("div");
    let fromLabel = document.createElement("label");
    let startingTime = document.createElement("input");
    let endingTime = document.createElement("input");
    let tillLabel = document.createElement("label");
    let classDate = document.createElement("input");
    let joiningLink = document.createElement("input");
    let liveClassVisibility = document.createElement("select");
    let defaultOptionEveryone = document.createElement("option");
    let defaultOptionAllTeachers = document.createElement("option");
    let defaultOptionAllStudents = document.createElement("option");
    let createLiveClassButton = document.createElement("button");


    // Setting their attributes's Values //
    liveClassLaunchContainer.id = "liveClassLaunchContainer";
    forms.classList.add("forms");

    hostName.placeholder = "Host Name";
    teacherName.placeholder = "Teacher Name";
    subjectName.placeholder = "Subject Name";
    topicName.placeholder = "Topic Name";
    topicDescription.placeholder = "Topic Description";
    hostName.id = "hostName";
    hostName.value = document.getElementById("userId").textContent + "( Creator )";
    hostName.disabled = true;
    teacherName.id = "teacherName";
    subjectName.id = "subjectName";
    topicName.id = "topicName";
    topicDescription.id = "topicDescription";
    
    timeDiv.classList.add("timeDiv");
    fromLabel.innerText = "From:- "
    tillLabel.innerText = "Till:- "
    startingTime.type="time";
    endingTime.type="time";
    startingTime.id = "startingTime";
    endingTime.id = "endingTime";

    classDate.id = "classDate";
    classDate.type = "date";
    joiningLink.id = "joiningLink";
    joiningLink.type = "url";
    joiningLink.placeholder = "Joining Link";

    liveClassVisibility.id = "liveClassVisibility";
    defaultOptionEveryone.value = defaultOptionEveryone.innerText = "Everyone";
    defaultOptionAllTeachers.value = defaultOptionAllTeachers.innerText = "All Teachers";
    defaultOptionAllStudents.value = defaultOptionAllStudents.innerText = "All Students";
    
    createLiveClassButton.type = "submit";
    createLiveClassButton.id = "createLiveClassButton";
    createLiveClassButton.innerText = "Create Live Class";



    // Wrapping up the Tags //
    liveClassVisibility.appendChild(defaultOptionEveryone);
    liveClassVisibility.appendChild(defaultOptionAllTeachers);
    liveClassVisibility.appendChild(defaultOptionAllStudents);

    timeDiv.appendChild(fromLabel);
    timeDiv.appendChild(startingTime);
    timeDiv.appendChild(tillLabel);
    timeDiv.appendChild(endingTime);

    forms.appendChild(hostName);
    forms.appendChild(teacherName);
    forms.appendChild(subjectName);
    forms.appendChild(topicName);
    forms.appendChild(topicDescription);
    forms.appendChild(timeDiv);
    forms.appendChild(classDate);
    forms.appendChild(joiningLink);
    forms.appendChild(liveClassVisibility);
    forms.appendChild(createLiveClassButton);

    // Appending the liveClassVisibility Tag's Menu //
    for(let key in instituteClasses){  
        let option = document.createElement("option");
        option.value = option.innerText =instituteClasses[key].className;
        liveClassVisibility.appendChild(option);
    }

    liveClassLaunchContainer.appendChild(forms);
    return liveClassLaunchContainer;
}



// Function to open a Launch Live Class Section //
function openLaunchClassForm(){

    window.open("https:/meet.new" , "_blank");
    // Getting The Live Class Section Tag //
    let liveClassSection = document.getElementById("liveClassSection");
    liveClassSection.innerHTML = "";

    liveClassSection.appendChild(getLiveClassCreatorForm());

    // Function to Launch a Live Class and Make an Entry to the Institute's Database //
    function createLiveClass(e){

        e.preventDefault();

        // Creating Some Variables //
        let liveClassVisibility = document.getElementById("liveClassVisibility");
        let data = {
            "task" : "Create Live Class", 
            "loggedInUser" : document.getElementById("userId").textContent, 
            "instituteId" : document.getElementById("instituteId").textContent,   
            "authority" : document.getElementById("authority").textContent,
            "sessionId" : document.getElementById("sessionId").textContent,
            "hostName" : document.getElementById("userId").textContent,
            "teacherName" : document.getElementById("teacherName").value,
            "subjectName" : document.getElementById("subjectName").value,
            "topicName" : document.getElementById("topicName").value,
            "topicDescription" : document.getElementById("topicDescription").value,
            "startingTime" : document.getElementById("startingTime").value,
            "endingTime" : document.getElementById("endingTime").value,
            "classDate" : document.getElementById("classDate").value,
            "joiningLink" : document.getElementById("joiningLink").value,
            "liveClassVisibility" : liveClassVisibility.options[liveClassVisibility.selectedIndex].value,
        };

        let onLoadFunction = function(){
            if( this.status != 200 ){
                alert("Something Went Wrong!");
            }
            else{
                let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
                if( responseText.includes("Success") || responseText.includes("Failed") ){
                    let response = JSON.parse(responseText);
                    alert(response.message);
                    if( response.result == "Success" ){
                        showLiveClasses("hosted");
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

    // Binding the createLiveClassButton to its Handler //
    document.getElementById("createLiveClassButton").addEventListener("click", createLiveClass);
}



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
function getLiveClassCard(liveClassDetails, inputDisabled=false){

    // Creating Tags //
    let form = document.createElement("form");
    let classSelectorDiv = document.createElement("div");
    let classDescriptionDiv = document.createElement("div");
    let joinClassButtonDiv = document.createElement("div");
    let liveClassCardCheckbox = document.createElement("input");
    let classHeadingDiv = document.createElement("div");
    let hostNameDiv = document.createElement("div");
    let classTitleDiv = document.createElement("div");
    let classSubtopicsUL = document.createElement("ul");
    let pTopicDescription = document.createElement("p");
    let classDateDiv = document.createElement("div");
    let classTimeDiv = document.createElement("div");
    let aClassLink = document.createElement("a");

    
    // Assigning values to their Attributes //
    form.classList.add("classItem");
    classSelectorDiv.classList.add("classSelector");
    classDescriptionDiv.classList.add("classDescription");
    joinClassButtonDiv.classList.add("joinClassButton");
    
    liveClassCardCheckbox.type = "checkbox";
    liveClassCardCheckbox.name = "liveClassCard";
    liveClassCardCheckbox.disabled = inputDisabled;
    liveClassCardCheckbox.value = liveClassDetails.liveClassId;
    classHeadingDiv.classList.add("classHeading");
    classHeadingDiv.innerText = liveClassDetails.subjectName; 
    hostNameDiv.classList.add("hostName");
    hostNameDiv.innerHTML = "( " + liveClassDetails.teacherName + " )"; 

    classDescriptionDiv.classList.add("classDescription");
    classTitleDiv.classList.add("classTitle");
    classTitleDiv.innerText = liveClassDetails.topicName;
    classSubtopicsUL.classList.add("classSubtopics");
    pTopicDescription.innerText = liveClassDetails.topicDescription;
    classDateDiv.classList.add("classDate");
    classDateDiv.innerText = liveClassDetails.classDate;
    classTimeDiv.classList.add("classTime");
    classTimeDiv.innerText = "Timing :- " + liveClassDetails.startingTime + " to " + liveClassDetails.endingTime;

    joinClassButtonDiv.classList.add("joinClassButton");
    aClassLink.classList.add("classJoinButton");
    aClassLink.target = "_blank";
    aClassLink.href = liveClassDetails.joiningLink;
    aClassLink.innerText = "Join Class";
    joinClassButtonDiv.addEventListener("click", ()=>{window.open(liveClassDetails.joiningLink, "_blank");});



    // Wrapping up the tags //
    classSelectorDiv.appendChild(liveClassCardCheckbox);
    classSelectorDiv.appendChild(classHeadingDiv);
    classSelectorDiv.appendChild(hostNameDiv);

    classSubtopicsUL.appendChild(pTopicDescription);
    classSubtopicsUL.appendChild(classDateDiv);
    classSubtopicsUL.appendChild(classTimeDiv);

    classDescriptionDiv.appendChild(classTitleDiv);
    classDescriptionDiv.appendChild(classSubtopicsUL);

    joinClassButtonDiv.appendChild(aClassLink);

    form.appendChild(classSelectorDiv);
    form.appendChild(classDescriptionDiv);
    form.appendChild(joinClassButtonDiv);


    // Disable the Join Class Link if the current date & time is not in the range of scheduled Live Class date & time //
    if( ! (isDateSame(liveClassDetails.classDate) && isTimeInRange(liveClassDetails.startingTime, liveClassDetails.endingTime) ) ){
        aClassLink.style.pointerEvents = joinClassButtonDiv.style.pointerEvents = "none";
        // joinClassButtonDiv.style.backgroundColor = "#76a3ddd7";
        aClassLink.style.backgroundColor = "#76a3ddd7";
    }

    return form;
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
    let liveClassSection = document.getElementById("liveClassSection");
    let liveClassContainer = document.createElement("div");
    liveClassSection.innerHTML = "";
    liveClassContainer.id = "liveClassContainer";

    // Creating Some Variables //
    let data = {
        "task" : "Get Live Classes", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent,   
        "authority" : document.getElementById("authority").textContent,
        "sessionId" : document.getElementById("sessionId").textContent,
        "hostName" : document.getElementById("userId").textContent
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

                    
                    if( classFilter == "hosted" ){
                        if( response.liveClasses[key].hostName == data.loggedInUser )
                        liveClassContainer.appendChild(getLiveClassCard(response.liveClasses[key]));
                    }
                    else{
                        let classVisibility = response.liveClasses[key].liveClassVisibility.toLowerCase();
                        if( isClassUpcomingClass(response.liveClasses[key].classDate, response.liveClasses[key].startingTime, response.liveClasses[key].endingTime) && ( response.liveClasses[key].hostName == data.loggedInUser || classVisibility == "everyone" || classVisibility == "all teachers" ) ){
                            
                            liveClassContainer.appendChild(getLiveClassCard(response.liveClasses[key], true));
                        }
                    }
                }
                // If No Live Classes are scheduled //
                if( response.liveClasses.length < 1 ){
                    let mymessagediv = document.createElement('div')
                    mymessagediv.style.color = "red"
                    mymessagediv.style.marginTop = "10px"
                    mymessagediv.innerHTML = "No Live Classes Scheduled !"
                    liveClassContainer.appendChild(mymessagediv)
                    // alert("No Live Classes Scheduled !!!");
                }
            }
            else{
                alert(responseText);
            }
        }
    }

    liveClassSection.appendChild(liveClassContainer);

    // Making AJAX Request //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction);

}



// Function to delete Live Hosted Classes //
function deleteHostedClasses(){

    let selectedLiveClasses = getSelectedItems("liveClassCard");

    if( selectedLiveClasses.length ){

        let data = {
            "task" : "Delete Live Classes", 
            "loggedInUser" : document.getElementById("userId").textContent, 
            "authority" : document.getElementById("authority").textContent,
            "instituteId" : document.getElementById("instituteId").textContent,   
            "sessionId" : document.getElementById("sessionId").textContent,
            "selectedLiveClasses" : selectedLiveClasses
        };
    
        let onLoadFunction = function(){
    
            if( this.status != 200 ){
                alert("Something Went Wrong!");
            }
            else{
                let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
                if( responseText.includes("Success") || responseText.includes("Failed") ){
                    let response = JSON.parse(responseText);
                    alert(response.message);
                    showLiveClasses("hosted");
                }
                else{
                    alert(responseText);
                }
            }
        }
    
        // Making the AJAX Request //
        makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction)

    }
    else{
        alert("Please select atleast one Live Class !!!");
    }
}





// Binding the Live Class's Buttons to their Respective Handlers //
document.getElementById("launchClass").addEventListener("click", openLaunchClassForm);
document.getElementById("showHostedClasses").addEventListener("click", ()=>{showLiveClasses("hosted");});
document.getElementById("deleteHostedClasses").addEventListener("click", deleteHostedClasses);
document.getElementById("upcomingLiveClasses").addEventListener("click", ()=>{showLiveClasses();});










// ---------------------------------- Update Personal Details ---------------------------------- //


// Function to get the Isntitute Details From the Database //
function getTeacherDetails(){

    let teacherData = {};

    let data = {
        "task" : "Get Teacher Data", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent, 
        "userId" : document.getElementById("userId").textContent, 
        "sessionId" : document.getElementById("sessionId").textContent,
        "authority" : document.getElementById("authority").textContent,
    };

    let onLoadFunction = function(){
        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                teacherData = response.teacherData[0];
            }
            else{
                alert(responseText);
            }
        }
    }

    // Making the AJAX Request //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction, false);
    return teacherData;
}


// Function to fill up the Self Profile Section //
function fillUpPersonalDetails(){
    
    // Getting the Institute Data //
    let teacherData = getTeacherDetails();

    // Getting the tag Elements //
    let rootProfileDiv = document.getElementById("teacherProfileDiv");
    let teacherId = document.getElementById("personalPersonId");
    let teacherName = document.getElementById("personalName");
    let teacherEmail = document.getElementById("personalEmail");
    let teacherPhoneNo = document.getElementById("personalPhoneNo");
    let teacherAddress = document.getElementById("personalAddress");
    let teacherCity = document.getElementById("personalCity");
    let teacherState = document.getElementById("personalState");
    let teacherPinCode = document.getElementById("personalPinCode");


    // Assigining Values to their attributes //
    rootProfileDiv.style.display = "block";
    teacherId.value = teacherData.userId;
    teacherId.disabled = true;
    teacherName.value = teacherData.name;
    teacherEmail.value = teacherData.email;
    teacherPhoneNo.value = teacherData.phoneNo;
    teacherAddress.value = teacherData.address;
    teacherCity.value = teacherData.city;
    teacherState.value = teacherData.state;
    teacherPinCode.value = teacherData.pinCode;
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
        "updatedTeacherName" : document.getElementById("personalName").value,
        "updatedTeacherEmail" : document.getElementById("personalEmail").value,
        "updatedTeacherPhoneNumber" : document.getElementById("personalPhoneNo").value,
        "updatedTeacherAddress" : document.getElementById("personalAddress").value,
        "updatedTeacherCity" : document.getElementById("personalCity").value,
        "updatedTeacherState" : document.getElementById("personalState").value,
        "updatedTeacherPinCode" : document.getElementById("personalPinCode").value,
    };

    
    let xhr = new XMLHttpRequest();
    let formData = new FormData();
    let profileImg = document.getElementById("teacherProfileImg").src;  
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











function setAttendance(){

    let data = {
        "task" : "Set Attendance", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent, 
        "userId" : document.getElementById("userId").textContent, 
        "sessionId" : document.getElementById("sessionId").textContent,
        "authority" : document.getElementById("authority").textContent,
        "year" : "2022",
        "date" : "06/01/2022",
        "month" : "Jan",
        "class" : "BCA Final Year",
        "persons" : [
            {"userId":"student1", "name":"student1", "status":"absent"}
        ]
    };

    let onLoadFunction = function(){
        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                alert(response.message);
            }
            else{
                alert(responseText);
            }
        }
    }

    // Making the AJAX Request //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction);

}



document.getElementById("markAttendance").addEventListener("click", setAttendance);