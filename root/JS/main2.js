



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
    downloadContainer.appendChild(form);


    // Function that will make ajax request to the Server to Upload a File //
    function uploadFileToTheDatabase(e){

        e.preventDefault();

        // Creating Some Variables //
        let data = {
            "task" : "Upload File", 
            "instituteId" : document.getElementById("userId").textContent, 
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
                        // showUploadedFiles();
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





// Binding the Upload File Buttons to their Respective Handlers //
document.getElementById("addFileButton").addEventListener("click", openUploadFileForm);