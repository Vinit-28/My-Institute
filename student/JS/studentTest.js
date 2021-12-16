

// // Function to make a AJAX request to the Server //
// function makeAJAXRequest(requesType, serverUrl, data, onLoadFunction, async=true){

//     // Creating the XHR Object //
//     let xhrObject = new XMLHttpRequest();
//     xhrObject.open(requesType, serverUrl, async);
//     xhrObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
//     // After getting the Response from the Server this Function will be executed //
//     xhrObject.onload = onLoadFunction;

//     // Making the Request //
//     xhrObject.send("request="+JSON.stringify(data));
// }


function executeText() {
    
    if(document.getElementById('testScript') == undefined)
    {
        alert('Unable to launch test, Try reloading the page !');
        setTimeout(function(){
            window.close()
        } , 2000)
    }
    else
    {
        
        // document.getElementById('testScript').style.display = "none";

        let onLoadFunction = function () {

            var string = this.responseText.split('[{')[1];
            string = "[{" + string;
            var a = JSON.parse(string);
            a  = JSON.stringify(a);
            localStorage.setItem('questions' , a);
            localStorage.setItem('started_by_user' , true);
            window.open('http://localhost/student/PHP/testPaper.html' , '_blank');
        }

        // Making the AJAX Request //
        makeAJAXRequest("POST", "http://localhost/student/JS/makeRequest.php", "", onLoadFunction, false);

    }

}