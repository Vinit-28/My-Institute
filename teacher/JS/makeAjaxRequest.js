


// Function to encode an object recursively //
function encodeObjectRecursively(dataObject){

    for(let key in dataObject){

        if( typeof(dataObject[key]) == 'string' ){
            dataObject[key] = encodeURIComponent(dataObject[key]);
        }
        else if( typeof(dataObject[key]) == 'object' ){
            encodeObjectRecursively(dataObject[key]);
        }
    }
}



// Function to make a AJAX request to the Server //
function makeAJAXRequest(requesType, serverUrl, data, onLoadFunction, async=true){

    // Encoding the Data //
    encodeObjectRecursively(data);

    // Creating the XHR Object //
    let xhrObject = new XMLHttpRequest();
    xhrObject.open(requesType, serverUrl, async);
    xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    // After getting the Response from the Server this Function will be executed //
    xhrObject.onload = onLoadFunction;

    // Making the Request //
    xhrObject.send("request="+JSON.stringify(data));
}



// Function to make a AJAX request to the Server along with file uploading //
function makeAJAXRequest_FileUpload(requesType, serverUrl, requestData, formData, onLoadFunction, async=true){

    // Encoding the Data //
    encodeObjectRecursively(requestData);

    // Creating the XHR Object //
    let xhr = new XMLHttpRequest();
        
    // Setting some request variables //
    formData.append("request", JSON.stringify(requestData));   
    xhr.timeout = 10000;
    xhr.open(requesType, serverUrl, async); 

    
    // After getting the Response from the Server this Function will be executed //
    xhr.onload = onLoadFunction;

    // Making the Request //
    xhr.send(formData);
}