

// Function to make a XmlHttpRequest to the Server //
function makeXMLHTTPREQUEST(serverPath, data, onLoadFunction){
    // Creating the XHR Object //
    let xhrObject = new XMLHttpRequest();
    xhrObject.open("POST", serverPath);
    xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    // After getting the Response from the Server this Function will be executed //
    xhrObject.onload = onLoadFunction;

    // Making the Request //
    xhrObject.send("request="+JSON.stringify(data));
}