


// ----------------------------- Uploading Test ----------------------------- //



// Function to upload test for the students //
function uploadTest(e){

    e.preventDefault();

    // Creating Some Request Variables //
    let data = {
        "task" : "Upload Test", 
        "loggedInUser" : document.getElementById("userId").textContent, 
        "instituteId" : document.getElementById("instituteId").textContent,  
        "sessionId" : document.getElementById("sessionId").textContent,
        "authority" : document.getElementById("authority").textContent,
        "uploadedDateTime" : Date(),
        "subjectName" : "subjectName",
        "topicName" : "topicName",
        "testDate" : "2022-01-02",
        "fromTime" : "15:12",
        "toTime" : "15:50",
        "forClass" : "BCA Final Year",
        "questionGapSec" : "15",
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
            }
            else{
                alert(responseText);
            }
            console.log(this.responseText);
        }
    };

    // Making the Request //
    makeAJAXRequest_FileUpload(requesType, serverUrl, data, formData, onLoadFunction);
    // makeAJAXRequest("POST", "../../Server/Utilities/InstituteSpecificUtilities.php", data, onLoadFunction);
}


// uploadTest();


document.getElementById("uploadTestFile").addEventListener("click", uploadTest);