
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
            "instituteId" : document.getElementById("userId").textContent, 
            "authority" : document.getElementById("authority").textContent,
            "sessionId" : document.getElementById("sessionId").textContent,
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
        "instituteId" : document.getElementById("userId").textContent, 
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

                for(let key in response.uploadedFiles){
                    // Appending the Input Tags (Checkboxes) in the form //
                    form.appendChild(getUploadFileCard(response.uploadedFiles[key]));
                }
                
                // If no files to show !
                if(response.uploadedFiles.length == 0){
                    form.innerHTML = `<div style="color: red; text-align:center; margin-top:10px">No files to show !</div>`;
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
            "instituteId" : document.getElementById("userId").textContent, 
            "sessionId" : document.getElementById("sessionId").textContent,
            "authority" : document.getElementById("authority").textContent,
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

    window.open("https:/www.meet.new");

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
            "instituteId" : document.getElementById("userId").textContent, 
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

        // console.log(data);
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
                        showHostedClasses();
                    }
                }
                else{
                    alert(responseText);
                }
            }
            // console.log(this.responseText);
        }

        // Making AJAX Request //
        makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction);
    }

    // Binding the createLiveClassButton to its Handler //
    document.getElementById("createLiveClassButton").addEventListener("click", createLiveClass);
}


// Function to Make a Live Class Card //
function getLiveClassCard(liveClassDetails, disabled){

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
    joinClassButtonDiv.classList.add("classJoinButton");
    
    liveClassCardCheckbox.type = "checkbox";
    liveClassCardCheckbox.name = "liveClassCard";
    liveClassCardCheckbox.disabled = disabled;
    liveClassCardCheckbox.value = liveClassDetails.liveClassId;
    classHeadingDiv.classList.add("classHeading");
    classHeadingDiv.innerText = liveClassDetails.subjectName; 
    hostNameDiv.classList.add("hostName");
    hostNameDiv.innerText = "( " + liveClassDetails.teacherName + " )"; 

    classDescriptionDiv.classList.add("classDescription");
    classTitleDiv.classList.add("classTitle");
    classTitleDiv.innerText = liveClassDetails.topicName;
    classSubtopicsUL.classList.add("classSubtopics");
    pTopicDescription.innerText = liveClassDetails.topicDescription;
    classDateDiv.classList.add("classDate");
    classDateDiv.innerText = liveClassDetails.classDate;
    classTimeDiv.classList.add("classTime");
    classTimeDiv.innerText = "Timing :- " + liveClassDetails.startingTime + " to " + liveClassDetails.endingTime;


    // aClassLink.classList.add("classJoinButton");

    aClassLink.target = "_blank";
    aClassLink.href = liveClassDetails.joiningLink;
    aClassLink.innerText = "Join Class";


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

    return form;
}


// Function to Show all the Hsoted Classes //
function showHostedClasses(classFilter){

    // Getting The Live Class Section Tag //
    let liveClassSection = document.getElementById("liveClassSection");
    let liveClassContainer = document.createElement("div");
    liveClassSection.innerHTML = "";
    liveClassContainer.id = "liveClassContainer";

    // Creating Some Variables //
    let data = {
        "task" : "Get Live Classes", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("userId").textContent, 
        "sessionId" : document.getElementById("sessionId").textContent,
        "authority" : document.getElementById("authority").textContent,
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
                        liveClassContainer.appendChild(getLiveClassCard(response.liveClasses[key], true));
                    }
                }
                // If No Live Classes are scheduled //
                if( response.liveClasses.length < 1 ){
                    // alert("No Live Classes Scheduled !!!");
                    
                    liveClassSection.innerHTML = `<div style="color: red; text-align:center; margin-top:10px">No live class is sheduled !</div>`;
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
            "instituteId" : document.getElementById("userId").textContent, 
            "sessionId" : document.getElementById("sessionId").textContent,
            "authority" : document.getElementById("authority").textContent,
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
                    showHostedClasses();
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
document.getElementById("showHostedClasses").addEventListener("click", ()=>{showHostedClasses("hosted");});
document.getElementById("deleteHostedClasses").addEventListener("click", deleteHostedClasses);
document.getElementById("allLiveClasses").addEventListener("click", ()=>{showHostedClasses("all");});