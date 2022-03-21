

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