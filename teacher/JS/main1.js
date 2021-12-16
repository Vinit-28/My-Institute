

// Declaring Some Global Varibales //
let relatedPersons = {};
let instituteClasses = {};
let uploadedAssignments = {};


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


// Function to get the Classes from the Institute's Database //
function getClassesOfTheInstitute(){

    let data = {
        "task" : "Update Classes", 
        "subtask" : "Show Classes", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent,  
        "sessionId" : document.getElementById("sessionId").textContent,
        "authority" : document.getElementById("authority").textContent,
    };

    let onLoadFunction = function(){

        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") || responseText.includes("Failed") ){
                let response = JSON.parse(responseText);
                instituteClasses = response.classes;
            }
            else{
                alert(responseText);
            }
        }
    }

    // Making the AJAX Request //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction, false);
}


// ------------------------------- Search Person ------------------------------- // 


// Function that will make and return a card conatining Searched Person Information //
function getPersonCard(personDetails, key){


    // Creating Tags //
    let suggestedPerson = document.createElement("div");
    let suggestedPersonProfile = document.createElement("div");
    let profile = document.createElement("img");
    let suggestedPersonDetails = document.createElement("div");
    let suggestedPersonID = document.createElement("span");
    let suggestedPersonName = document.createElement("span");
    let suggestedPersonDept = document.createElement("span");

    // Adding Classes //
    suggestedPerson.classList.add("suggestedPerson");
    suggestedPersonProfile.classList.add("suggestedPersonProfile");
    suggestedPersonDetails.classList.add("suggestedPersonDetails");
    suggestedPersonDetails.classList.add("suggestedPersonDetails");
    suggestedPersonID.classList.add("suggestedPersonID");
    suggestedPersonName.classList.add("suggestedPersonName");
    suggestedPersonDept.classList.add("suggestedPersonDept");


    // Adding content to the tags //
    profile.src = personDetails['profilePath'];
    suggestedPersonID.innerHTML = personDetails['userId'];
    suggestedPersonName.innerHTML = personDetails['name'];
    suggestedPersonDept.innerHTML = personDetails['designation'];

    if( personDetails['designation'].toLowerCase() == "student" ){
        suggestedPersonDept.innerHTML += " ( " + personDetails['class'] +" )";
    }


    // Wrapping the tags of the Person's Card //
    suggestedPersonProfile.appendChild(profile);
    suggestedPersonDetails.appendChild(suggestedPersonID);
    suggestedPersonDetails.appendChild(suggestedPersonName);
    suggestedPersonDetails.appendChild(suggestedPersonDept);
    suggestedPerson.appendChild(suggestedPersonProfile);
    suggestedPerson.appendChild(suggestedPersonDetails);
    
    suggestedPerson.id = key;
    suggestedPerson.onclick = openModalForSelectedPerson;
    return suggestedPerson;
}


// Function to search a person in the database //
function searchPersonInTheDatabase(e){
   
    if( e!=undefined && e!=null ) e.preventDefault();
    
    // Creating Some Variables //
    let personData = {
        "task" : "Search Person", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent, 
        "authority" : document.getElementById("authority").textContent,
        "sessionId" : document.getElementById("sessionId").textContent,
        "searchKey" : document.getElementById("searchKey").value,
    };

    let onLoadFunction = function(){
        
        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");

            if( responseText.includes("Success") || responseText.includes("Failed") ){
                let response = JSON.parse(responseText);
                if(response.result.includes("Failed")){
                    alert(response.message);
                }
                else{
                    let totalPersons = 0;
                    let searchResult = document.getElementById("searchResults");
                    searchResult.innerHTML = "";
                    searchResult.style.display = "block";
                    relatedPersons = response.relatedPersons;
                    for(let key in response.relatedPersons){
                
                        // Make a Person Card //
                        searchResult.appendChild(getPersonCard(response.relatedPersons[key], key));
                        totalPersons+=1;
                    }
                    
                    // If No One Found //
                    if( totalPersons <= 0 ){
                        let notFound = document.createElement("div");
                        notFound.classList.add("notFound");
                        notFound.innerText = "Couldn't find anything ! (Try again using relevant keywords.)";
                        notFound.style.color = "red";
                        searchResult.appendChild(notFound);
                    }
                }
            }
            else{
                alert(responseText);
            }
        }
    };

    // Making the Request to the Server //
    makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", personData, onLoadFunction);
}


// Binding the Function searchPersonInTheDatabase to the button searchPerson //
document.getElementById("searchPerson").addEventListener("click", searchPersonInTheDatabase);




// ------------------------------- Update Person Details ------------------------------- // 

// Function to return the index value of search key found in the Listitems //
function getIndexOfValue(listItems, find){
    for(let i=0;i<listItems.length;i++){
        if( find == listItems[i].value ) return i;   
    } 
    return 0;
}  


// Function to get the total fees of a Class //
function getTotalFees(className){
    for(let key in instituteClasses){
        if( instituteClasses[key].className == className ) return instituteClasses[key].fees;
    }
}


// Function to check whether the details of the selected person is Modified or not //
function isDataModified(updatedData, selectedPerson){

    if( selectedPerson.designation.toLowerCase() == "student" && updatedData.class != selectedPerson.class) return true;
    
    return (updatedData.name != selectedPerson.name || updatedData.phoneNo != selectedPerson.phoneNo || updatedData.address != selectedPerson.address || updatedData.city != selectedPerson.city || updatedData.pinCode != selectedPerson.pinCode || updatedData.state != selectedPerson.state || updatedData.adharCardNo != selectedPerson.adharCardNo || updatedData.gender != selectedPerson.gender || updatedData.designation != selectedPerson.designation);
}


// Function to close the modal //
function closeModal(){

    let modal = document.getElementById("selectedPersonProfileContainer");
    let updateDetailsButton = document.getElementById("updateDetailsButton");
    let closeModalButton = document.getElementById("closeModalButton");

    updateDetailsButton.remove();
    closeModalButton.remove();
    modal.style.display = "none";
}


// Function to open the Modal to display the Selected Person's Details and Root/Teacher can modify it too //
function openModalForSelectedPerson(selectedPerson){
    // Getting the information of the Selected Person //
    let selectedPersonProfile =  relatedPersons[this.id];
    // Getting Tags //
    let modal = document.getElementById("selectedPersonProfileContainer");
    let modalForm = document.getElementById("modalForm");
    let selectedImg = document.getElementById("selectedImg");
    let updatePersonId = document.getElementById("update-personId");
    let updateName = document.getElementById("update-name");
    let updateEmail = document.getElementById("update-email");
    let updateGender = document.getElementById("update-gender");
    let updateDesignation = document.getElementById("update-designation");
    let updateClass = document.getElementById("update-class");
    let updatePhoneNo = document.getElementById("update-phoneNo");
    let updateAdharCardNo = document.getElementById("update-adharCardNo");
    let updateAddress = document.getElementById("update-address");
    let updateCity = document.getElementById("update-city");
    let updateState = document.getElementById("update-state");
    let updatePinCode = document.getElementById("update-pinCode");
    let depositedFees = document.getElementById("depositedFees");
    let remainingFees = document.getElementById("remainingFees");
    let updateDetailsButton = document.createElement("button");
    let closeModalButton = document.createElement("button");


    
    // Assigning data to their Attributes //
    updateDetailsButton.type = closeModalButton.type =  "button";
    updateDetailsButton.innerText = "Update Details";
    updateDetailsButton.id = "updateDetailsButton";
    closeModalButton.id = "closeModalButton";
    closeModalButton.innerText = "Go Back";
    appendClassDropdownMenu("update-class", ()=>{
        updateClass.selectedIndex =  getIndexOfValue(updateClass.options, selectedPersonProfile.class);
    });
    modal.style.display = "flex";
    selectedImg.src = selectedPersonProfile.profilePath;
    updatePersonId.value = selectedPersonProfile.userId;
    updatePersonId.disabled = true;
    updateEmail.disabled = true;
    updateName.value = selectedPersonProfile.name;
    updateEmail.value = selectedPersonProfile.email;
    updateName.value = selectedPersonProfile.name;
    updateGender.selectedIndex = getIndexOfValue(updateGender.options, selectedPersonProfile.gender);
    updateDesignation.selectedIndex = getIndexOfValue(updateDesignation.options, selectedPersonProfile.designation);
    updateDesignation.disabled =  true;
    updatePhoneNo.value = selectedPersonProfile.phoneNo;
    updateAdharCardNo.value = selectedPersonProfile.adharCardNo;
    updateAddress.value = selectedPersonProfile.address;
    updateCity.value = selectedPersonProfile.city;
    updateState.value = selectedPersonProfile.state;
    updatePinCode.value = selectedPersonProfile.pinCode;
    modalForm.appendChild(closeModalButton);
    modalForm.appendChild(updateDetailsButton);


    // If the Selected Person is a student //
    if( selectedPersonProfile.designation.toLowerCase() == "student" ){
        depositedFees.style.display = remainingFees.style.display = "block";
        depositedFees.value = (selectedPersonProfile.feeSubmitted + " ( Deposited Fees ) " );
        let totalFee = getTotalFees(selectedPersonProfile.class);
        remainingFees.value = (totalFee-selectedPersonProfile.feeSubmitted + " ( Remaining Fees ) " );
    }
    // Otherwise class dropdown menu will be disabled //
    else{
        depositedFees.style.display = "none";
        remainingFees.style.display = "none";
        updateClass.disabled = true;
    }

    // Function to update details of the selected person //
    function updateDetailsOfSelectedPerson(){
        
        // Creating the Updated data variable //
        let updatedData = {
            "task" : "Update Person Details", 
            "loggedInUser" : document.getElementById("userId").textContent, 
            "instituteId" : document.getElementById("instituteId").textContent,  
            "sessionId" : document.getElementById("sessionId").textContent,
            "authority" : document.getElementById("authority").textContent,
            "userId" : updatePersonId.value,
            "name" : updateName.value,
            "email" : updateEmail.value,
            "gender" : updateGender.options[updateGender.selectedIndex].value,
            "designation" : updateDesignation.options[updateDesignation.selectedIndex].value,
            "class" : updateClass.options[updateClass.selectedIndex].value,
            "phoneNo" : updatePhoneNo.value,
            "adharCardNo" : updateAdharCardNo.value,
            "address" : updateAddress.value,
            "state" : updateState.value,
            "city" : updateCity.value,
            "pinCode" : updatePinCode.value,
        };
        
        // If data is modified //
        if( isDataModified(updatedData, selectedPersonProfile) ){
            
            // Onload Function to be executed When request is made and got the response //
            let onLoadFunction = function(){
                
                if( this.status != 200 ){
                    alert("Something Went Wrong!");
                }
                else{
                    let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");           
                    if( responseText.includes("Success") ){
                        let response = JSON.parse(responseText);
                        alert(response.message);
                        document.getElementById("selectedPersonProfileContainer").style.display = "none";
                        updateDetailsButton.remove();
                        closeModalButton.remove();
                        searchPersonInTheDatabase();
                    }
                    else{
                        alert(responseText);
                    }
                }
            }

            // Making the Request to the Server //
            makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", updatedData, onLoadFunction);
        }
        // If Data is not Modified //
        else{
            alert("Nothing to update !!!");
        }
    }

        
    updateDetailsButton.addEventListener("click", updateDetailsOfSelectedPerson);
    closeModalButton.addEventListener("click", closeModal);
}





// Function to Create or Append or Edit the Class Dropdown Menu int the Add Person Pannel //
function appendClassDropdownMenu(classDropdownMenuId, callback=null){
    
    // Getting the ClassContainer //
    let classDropdownMenu = document.getElementById(classDropdownMenuId)
    classDropdownMenu.innerHTML = "";

    // Default Menu Option //
    {
        // Creating an option tag With the value of ClassName //
        let option = document.createElement("option");
        option.value = option.innerText = "Class";
        option.classList.add("options");
        // Appending the option in the Class Dropdown Menu //
        classDropdownMenu.appendChild(option);
    }

    getClassesOfTheInstitute();
    
    for(let key in instituteClasses){    
        // Creating an option tag With the value of ClassName //
        let option = document.createElement("option");
        option.value = option.innerText = instituteClasses[key].className;
        option.classList.add("options");
        // Appending the option in the Class Dropdown Menu //
        classDropdownMenu.appendChild(option);
    }
    
    // Calling the callback function if any //
    if( callback != null )
        callback();
}





// ------------------------------- Assignments ------------------------------- // 




// Function to make and return the Form of Upload New Assignment //
function getUploadNewAssignmentForm(){


    let uploadAssignmentContainer = document.getElementById("uploadAssignmentContainer");
    uploadAssignmentContainer.innerHTML = "";

    // Creating Tags //
    let divForm = document.createElement("div");
    let form = document.createElement("form");
    let uploadedBy = document.createElement("input");
    let subjectName = document.createElement("input");
    let assignmentTitle = document.createElement("input");
    let assignmentDescription = document.createElement("input");
    let timeDivFileInput = document.createElement("div");
    let timeDivDeadline = document.createElement("div");
    let fileInput = document.createElement("input");
    let labelFile = document.createElement("label");
    let deadline = document.createElement("input");
    let labelTime = document.createElement("label");
    let selectVisibility = document.createElement("select");
    let defaultOption = document.createElement("option");
    let uploadAssignmentButton = document.createElement("button");


    // Adding Classes and Assigning Values to their Attributes //
    form.classList.add("forms");
    timeDivFileInput.classList.add("timeDiv");
    timeDivDeadline.classList.add("timeDiv");

    uploadedBy.placeholder = "Uploaded By";
    subjectName.placeholder = "Subject Name";
    assignmentTitle.placeholder = "Assignment Title";
    assignmentDescription.placeholder = "Assignment Description";
    labelFile.innerText = "Upload File ";
    labelTime.innerText = "Deadline ";
    fileInput.type = "file";
    deadline.type = "datetime-local";
    defaultOption.value = defaultOption.innerText = "Visible To";
    defaultOption.selected = true;
    uploadAssignmentButton.innerText = "Upload Assignment";
    uploadAssignmentButton.type = "submit";

    subjectName.id = "subjectName";
    assignmentTitle.id = "assignmentTitle";
    assignmentDescription.id = "assignmentDescription";
    fileInput.id = "fileInput";
    deadline.id = "deadline";
    selectVisibility.id = "assignmentVisibility"

    uploadedBy.value = document.getElementById("userId").innerText + " (Creator)";
    uploadedBy.disabled = true;

    // Wrapping up the Tags //
    timeDivFileInput.appendChild(labelFile);
    timeDivFileInput.appendChild(fileInput);
    timeDivDeadline.appendChild(labelTime);
    timeDivDeadline.appendChild(deadline);
    selectVisibility.appendChild(defaultOption);

    form.appendChild(uploadedBy);
    form.appendChild(subjectName);
    form.appendChild(assignmentTitle);
    form.appendChild(assignmentDescription);
    form.appendChild(timeDivFileInput);
    form.appendChild(timeDivDeadline);
    form.appendChild(selectVisibility);
    form.appendChild(uploadAssignmentButton);
    uploadAssignmentContainer.appendChild(form);

    for(let key in instituteClasses){
        let option = document.createElement("option");
        option.value = option.innerText = instituteClasses[key].className;
        selectVisibility.appendChild(option);
    }


    return [form, subjectName, assignmentTitle, assignmentDescription, fileInput, deadline, selectVisibility, uploadAssignmentButton, uploadedBy]
}



// Function to open the New From to upload the Assignment //
function openUploadNewAssignmentForm(){

    let elemenList = getUploadNewAssignmentForm();

    let form = elemenList[0], subjectName = elemenList[1], assignmentTitle = elemenList[2], assignmentDescription = elemenList[3], fileInput = elemenList[4], deadline = elemenList[5], selectVisibility = elemenList[6], uploadAssignmentButton = elemenList[7], uploadedBy=elemenList[8];


    // Function to make request to the server to upload a new Assignment //
    function uploadNewAssignment(e){

        e.preventDefault();

        if( selectVisibility.selectedIndex == 0 ){
            alert("Please Select the Visibility of Assignment !!!");
            return;
        }

        // Creating Some Variables //
        let data = {
            "task" : "Upload New Assignment", 
            "loggedInUser" : document.getElementById("userId").textContent, 
            "instituteId" : document.getElementById("instituteId").textContent, 
            "authority" : document.getElementById("authority").textContent,
            "sessionId" : document.getElementById("sessionId").textContent,
            "uploadedBy": document.getElementById("userId").textContent,
            "subjectName": subjectName.value,
            "assignmentTitle": assignmentTitle.value,
            "assignmentDescription": assignmentDescription.value,
            "assignmentDeadline": deadline.value,
            "uploadedDateTime": Date(),
            "assignmentVisibility": selectVisibility.options[selectVisibility.selectedIndex].value,
        };

        let xhr = new XMLHttpRequest();
        let formData = new FormData();
        
        formData.append("request", JSON.stringify(data));   
        formData.append("assignmentFile", fileInput.files[0]);

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
                    showUploadedAssignments();
                }
                else{
                    alert(responseText);
                }
            }
        }
        xhr.send(formData);

    }
    uploadAssignmentButton.addEventListener("click", uploadNewAssignment);
}



// Function to get the Uploaded assignments from the Institute's Database //
function getUploadedAssignments(asyncRequest=true){

    // Creating Some Variables //
    let data = {
        "task" : "Get Uploaded Assignments", 
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




// Function to Make a Uploaded File Card //
function getUploadedAssignmentCard(assignmentDetails){

    // Creating Tags //
    let form = document.createElement("form");
    let classSelectorDiv = document.createElement("div");
    let classDescriptionDiv = document.createElement("div");
    let buttonsDiv = document.createElement("div");
    let liveClassCardCheckbox = document.createElement("input");
    let classHeadingDiv = document.createElement("div");
    let hostNameDiv = document.createElement("div");
    let classTitleDiv = document.createElement("div");
    let classSubtopicsUL = document.createElement("ul");
    let pTopicDescription = document.createElement("p");
    let classDateDiv = document.createElement("div");
    let classTimeDiv = document.createElement("div");
    let assignmentFileLink = document.createElement("a");
    let submissionLink = document.createElement("a");

    
    // Assigning values to their Attributes //
    form.classList.add("classItem");
    classSelectorDiv.classList.add("classSelector");
    classDescriptionDiv.classList.add("classDescription");
    buttonsDiv.classList.add("joinClassButton");
    
    liveClassCardCheckbox.type = "checkbox";
    liveClassCardCheckbox.name = "uploadedAssignmentCard";
    liveClassCardCheckbox.value = assignmentDetails.assignmentId;
    classHeadingDiv.classList.add("classHeading");
    classHeadingDiv.innerText = assignmentDetails.subjectName; 
    hostNameDiv.classList.add("hostName");
    hostNameDiv.innerHTML = "( " + assignmentDetails.uploadedBy + " )"; 

    classDescriptionDiv.classList.add("classDescription");
    classTitleDiv.classList.add("classTitle");
    classTitleDiv.innerText = assignmentDetails.assignmentTitle;
    classSubtopicsUL.classList.add("classSubtopics");
    pTopicDescription.innerText = assignmentDetails.assignmentDescription;
    classDateDiv.classList.add("classDate");
    classDateDiv.innerText = assignmentDetails.assignmentDeadline;
    classTimeDiv.classList.add("classTime");
    classTimeDiv.innerText = "Timing :- " + assignmentDetails.assignmentDeadline + " to " + assignmentDetails.assignmentDeadline;

    buttonsDiv.classList.add("joinClassButton");
    assignmentFileLink.classList.add("classJoinButton");
    assignmentFileLink.target = "_blank";
    assignmentFileLink.href = assignmentDetails.assignmentFileLinkHref;
    assignmentFileLink.innerText = "Assignment File";

    submissionLink.classList.add("classJoinButton");
    submissionLink.innerText = "View Submissions";
    submissionLink.addEventListener("click", ()=>{console.log(assignmentDetails.assignmentId)});


    // Wrapping up the tags //
    classSelectorDiv.appendChild(liveClassCardCheckbox);
    classSelectorDiv.appendChild(classHeadingDiv);
    classSelectorDiv.appendChild(hostNameDiv);

    classSubtopicsUL.appendChild(pTopicDescription);
    classSubtopicsUL.appendChild(classDateDiv);
    classSubtopicsUL.appendChild(classTimeDiv);

    classDescriptionDiv.appendChild(classTitleDiv);
    classDescriptionDiv.appendChild(classSubtopicsUL);

    buttonsDiv.appendChild(assignmentFileLink);
    buttonsDiv.appendChild(submissionLink);

    form.appendChild(classSelectorDiv);
    form.appendChild(classDescriptionDiv);
    form.appendChild(buttonsDiv);

    return form;
}



// Function to Show all the Uploaded Assignments to the User(Teacher) //
function showUploadedAssignments(){

    let uploadAssignmentContainer = document.getElementById("uploadAssignmentContainer");
    uploadAssignmentContainer.innerHTML = "";
    getUploadedAssignments(false);
    let userId = document.getElementById("userId").textContent;

    for(let key in uploadedAssignments){
        if( userId == uploadedAssignments[key].uploadedBy ){
            uploadAssignmentContainer.appendChild(getUploadedAssignmentCard(uploadedAssignments[key]));
        }
    }

    if( !uploadAssignmentContainer.children.length ){
        alert("No Uploaded Assignments to Show !!!");
    }
}




// Function to get the Selected Items from the Form Menu (Checkboxes) //
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



// Function to the index of the Selected Institute Class // 
function getIndexOfClasses(classList, className){

    for(let index=0;index<classList.length;index++){
        if( classList[index] == className ) return index;
    }
    return 0;
}



// Function to get the Selected Assignment //
function getSelectedAssignment(selectedId){

    for(let key in uploadedAssignments){
        if( uploadedAssignments[key].assignmentId == selectedId ) return uploadedAssignments[key];
    }
    return null;
}




// Function to provide the functionality to update the Uploaded Assignment //
function updateUploadedAssignment(){

    let selectedItems = getSelectedItems("uploadedAssignmentCard");

    if( selectedItems.length == 1 ){

        let elemenList = getUploadNewAssignmentForm();
        let selectedAssignment = getSelectedAssignment(selectedItems[0]); 
        let form = elemenList[0], subjectName = elemenList[1], assignmentTitle = elemenList[2], assignmentDescription = elemenList[3], fileInput = elemenList[4], deadline = elemenList[5], selectVisibility = elemenList[6], uploadAssignmentButton = elemenList[7], uploadedBy = elemenList[8];

        uploadAssignmentButton.innerText = "Update Assignment";
        // Setting the Pre-Selected/Entered Values //
        subjectName.value = selectedAssignment.subjectName;
        assignmentTitle.value = selectedAssignment.assignmentTitle;
        assignmentDescription.value = selectedAssignment.assignmentDescription;
        deadline.value = selectedAssignment.assignmentDeadline;
        selectVisibility.selectedIndex = getIndexOfValue(selectVisibility.options, selectedAssignment.assignmentVisibility);


        // Function to make request the server to update Uploaded Assignment //
        function makeRequestAndUpdateAssignment(e){

            e.preventDefault();
    
            // Creating Some Variables //
            let data = {
                "task" : "Update Uploaded Assignment", 
                "loggedInUser" : document.getElementById("userId").textContent, 
                "instituteId" : document.getElementById("instituteId").textContent, 
                "authority" : document.getElementById("authority").textContent,
                "sessionId" : document.getElementById("sessionId").textContent,
                "assignmentId" : selectedAssignment.assignmentId,
                "uploadedBy": document.getElementById("userId").textContent,
                "subjectName": subjectName.value,
                "assignmentTitle": assignmentTitle.value,
                "assignmentDescription": assignmentDescription.value,
                "assignmentDeadline": deadline.value,
                "uploadedDateTime": Date(),
                "assignmentVisibility": selectVisibility.options[selectVisibility.selectedIndex].value,
            };
    
            let xhr = new XMLHttpRequest();
            let formData = new FormData();
            
            formData.append("request", JSON.stringify(data));   
            formData.append("updatedAssignmentFile", fileInput.files[0]);
    
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
                        showUploadedAssignments();
                    }
                    else{
                        alert(responseText);
                    }
                }
            }

            xhr.send(formData);
        }

        uploadAssignmentButton.addEventListener("click", makeRequestAndUpdateAssignment);

    }
    else if( selectedItems.length > 1 ){
        alert("Select Only One Assignment !!!");
    }
    else{
        alert("Select atleast One Aassignment !!!");
    }
}




// Function to delete the selected Uploaded Assignments //
function deleteUploadedAssignments(){

    let selectedItems = getSelectedItems("uploadedAssignmentCard");
    console.log("wkegm");
    if( selectedItems.length ){

        // Creating Some Variables //
        let data = {
            "task" : "Delete Uploaded Assignments", 
            "loggedInUser" : document.getElementById("userId").textContent, 
            "instituteId" : document.getElementById("instituteId").textContent, 
            "authority" : document.getElementById("authority").textContent,
            "sessionId" : document.getElementById("sessionId").textContent,
            "selectedAssignments":selectedItems
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
                    showUploadedAssignments();
                }
                else{
                    alert(responseText);
                }
            }
        }

        // Making the AJAX Request //
        makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction);
    }
    else{
        alert("Select atleast One Aassignment !!!");
    }
}



// Binding the Upload Assignments Buttons to their respective handlers //
document.getElementById("uploadAssignment").addEventListener("click", openUploadNewAssignmentForm);
document.getElementById("showAssignments").addEventListener("click", showUploadedAssignments);
document.getElementById("deleteAssignments").addEventListener("click", deleteUploadedAssignments);
document.getElementById("updateAssignment").addEventListener("click", updateUploadedAssignment);



















// -------------------------- Calling the Required Functions -------------------------- //

// Getting the Classes of the Institute //
getClassesOfTheInstitute();
