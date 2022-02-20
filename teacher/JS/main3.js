


// ----------------------------- Uploading Test ----------------------------- //



function getUploadedTests(){

    // Creating Some Request Variables //
    let data = {
        "task" : "Get Uploaded Tests", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent,  
        "sessionId" : document.getElementById("sessionId").textContent,
        "authority" : document.getElementById("authority").textContent
    };

    let uploadedTests = null;
    let serverUrl = "../../Server/Utilities/InstituteSpecificUtilities.php";
    let requesType = "POST";
    let onLoadFunction = function(){

        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                uploadedTests = (response.uploadedTests.length == 0)? null : response.uploadedTests;
                // console.log(response.uploadedTests.length);
            }
            else{
                alert(responseText);
            }
        }
    };

    // Making the Request //
    makeAJAXRequest(requesType, serverUrl, data, onLoadFunction, false);
    return uploadedTests;
}





// Function to upload test for the students //
function uploadTest(e){

    e.preventDefault();

    // Creating Some Request Variables //
    let classForTest = document.getElementById("classForTest");
    let questionGap = document.getElementById("questionGap");

    // If the class is not selected //
    if( classForTest.selectedIndex == 0 ){
        alert("Please Select a Class !!!");
        return;
    }
    // If the question gap is not selected //
    else if( questionGap.selectedIndex == 0 ){
        alert("Please Select Question Gap !!!");
        return;
    }

    let data = {
        "task" : "Upload Test", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent,  
        "sessionId" : document.getElementById("sessionId").textContent,
        "authority" : document.getElementById("authority").textContent,
        "subjectName" : document.getElementById("subjectName").value,
        "topicName" : document.getElementById("topicName").value,
        "testDate" : document.getElementById("testDate").value,
        "fromTime" : document.getElementById("fromTime").value,
        "toTime" : document.getElementById("toTime").value,
        "forClass" : classForTest.options[classForTest.selectedIndex].value,
        "questionGapSec" : questionGap.options[questionGap.selectedIndex].value,
    };

    let formData = new FormData();
    let studentTestFile = document.getElementById("studentTestFile").files[0];      
    formData.append("studentTestFile", studentTestFile);
    let serverUrl = "../../Server/Utilities/InstituteSpecificUtilities.php";
    let requesType = "POST";
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
                    showUploadedTests();
                }
            }
            else{
                alert(responseText);
            }
        }
    };


    // Making the Request //
    makeAJAXRequest_FileUpload(requesType, serverUrl, data, formData, onLoadFunction);
}




// Function to make and return the uploaded test card //
function getUploadedTestCard(testDetails){
    
    // Creating Tag Elements //
    let classItem = document.createElement("div");
    let classSelector = document.createElement("div");
    let classDescription = document.createElement("div");
    let buttonsDiv = document.createElement("div");
    let classHeading = document.createElement("div");
    let hostName = document.createElement("div");
    let classTitle = document.createElement("div");
    let classSubtopics = document.createElement("ul");
    let testForClass = document.createElement("div");
    let uploadedDate = document.createElement("div");
    let classDate = document.createElement("div");
    let classTime = document.createElement("div");
    let viewResultButton = document.createElement("button");
    let deleteTestButton = document.createElement("button");


    // Adding Classes //
    classItem.classList.add("classItem");
    classSelector.classList.add("classSelector");
    classHeading.classList.add("classHeading");
    hostName.classList.add("hostName");
    classDescription.classList.add("classDescription");
    classTitle.classList.add("classTitle");
    classSubtopics.classList.add("classSubtopics");
    classDate.classList.add("classDate");
    classTime.classList.add("classTime");
    classSubtopics.classList.add("classSubtopics");
    classSubtopics.classList.add("classSubtopics");
    
    
    // Assigning values to their attributes //
    classHeading.innerText = testDetails.subjectName;
    hostName.innerText = " ( " + testDetails.uploadedBy + " )";
    classTitle.innerText = testDetails.topicName;
    testForClass.innerText = "Class : " + testDetails.forClass;
    uploadedDate.innerText = "Uploaded Date : " + testDetails.uploadedDateTime;
    classDate.innerText = "Test Date : " + testDetails.testDate;
    classTime.innerText = "Test Timing : " + testDetails.fromTime + " to " + testDetails.toTime;
    viewResultButton.innerText = "View Result";
    deleteTestButton.innerText = "Delete Test";
    deleteTestButton.addEventListener("click", ()=>{deleteUpladedTest(testDetails.testId);});
    classSelector.addEventListener("click", ()=>{window.open(testDetails.testFileLinkHref, "_blank");});


    // Wrapping up the tags //
    buttonsDiv.appendChild(viewResultButton);
    buttonsDiv.appendChild(deleteTestButton);

    classSubtopics.appendChild(testForClass);
    classSubtopics.appendChild(uploadedDate);
    classSubtopics.appendChild(classDate);
    classSubtopics.appendChild(classTime);

    classDescription.appendChild(classTitle);
    classDescription.appendChild(classSubtopics);

    classSelector.appendChild(classHeading);
    classSelector.appendChild(hostName);

    classItem.appendChild(classSelector);
    classItem.appendChild(classDescription);
    classItem.appendChild(buttonsDiv);


    return classItem;
}




// Function to show uploaded test on the screen //
function showUploadedTests(){

    // Getting the uploaded tests //
    let uploadedTests = getUploadedTests();

    // Getting the test container elements // 
    let uploadTestContainer = document.getElementById("uploadTestContainer");
    let uploadedTestContainer = document.getElementById("uploadedTestContainer");

    uploadTestContainer.style.display = "none";
    uploadedTestContainer.style.display = "flex";
    uploadedTestContainer.innerHTML = "";
    uploadedTestContainer.style.color = "black";
    uploadedTestContainer.style.justifyContent = "flex-start";

    // If there is no uploaded test //
    if( uploadedTests == null ){
        uploadedTestContainer.style.color = "red";
        uploadedTestContainer.style.display = "flex";
        uploadedTestContainer.style.justifyContent = "center";
        uploadedTestContainer.innerHTML = "No Test is Scheduled !";
    }
    else{

        // Iterating through all the uploaded tests //
        for(let key in uploadedTests){
            uploadedTestContainer.appendChild( getUploadedTestCard(uploadedTests[key]) );
        }
    }
}


// Function to delete the uploaded tests // 
function deleteUpladedTest(testId){

    // Craeting some request variables and handlers //
    let data = {
        "task" : "Delete Tests", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent,  
        "sessionId" : document.getElementById("sessionId").textContent,
        "authority" : document.getElementById("authority").textContent,
        "testId" : testId
    };

    let serverUrl = "../../Server/Utilities/InstituteSpecificUtilities.php";
    let requesType = "POST";
    let onLoadFunction = function(){

        if( this.status != 200 ){
            alert("Something Went Wrong!");
        }
        else{
            let responseText = this.responseText.replace(/(\r\n|\n|\r)/gm, "");
            if( responseText.includes("Success") ){
                let response = JSON.parse(responseText);
                alert(response.message);
                showUploadedTests();
            }
            else{
                alert(responseText);
            }
        }
    };

    // Making the Request //
    makeAJAXRequest(requesType, serverUrl, data, onLoadFunction, false);
}



// Function to open the form to upload a new test //
function openFormToUploadTest(){
    
    // Getting the test container elements // 
    let uploadTestContainer = document.getElementById("uploadTestContainer");
    let uploadedTestContainer = document.getElementById("uploadedTestContainer");
    let classForTest = document.getElementById("classForTest");
    
    uploadedTestContainer.style.display = "none";
    uploadTestContainer.style.display = "block";
    classForTest.innerHTML = "";
    
    // Getting Institute Classes //
    getClassesOfTheInstitute();

    // Adding available institute classes to the class menu //
    let defaultOption = document.createElement("option");
    defaultOption.selected = true;
    defaultOption.innerText = "Select Class";
    classForTest.appendChild(defaultOption);

    // Iterating through all the classes of the institutes //
    for(let key in instituteClasses){

        // Creating and Appending a new class option to the class menu //
        let option = document.createElement("option");
        option.innerText = instituteClasses[key].className;
        classForTest.appendChild(option);
    }
}





// Binding the buttons with their respective handlers // 
document.getElementById("uploadNewTest").addEventListener("click", uploadTest);
document.getElementById("uploadTestButton").addEventListener("click", openFormToUploadTest);
document.getElementById("uploadedTestButton").addEventListener("click", showUploadedTests);