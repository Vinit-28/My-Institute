
function getCard(title, author, imageSrc, url) {
    let book = document.createElement('div');
    book.classList.add('book');

    let bookContainer = document.createElement('div');
    bookContainer.classList.add('bookContainer');

    let details = document.createElement('div');
    details.classList.add('details');

    let img = document.createElement('img');
    img.classList.add('bookThumbnail');
    img.src = imageSrc;

    let anchor = document.createElement('a');
    anchor.classList.add('link');
    anchor.href = url;
    anchor.target = "_blank";

    let h3 = document.createElement('h3');
    h3.classList.add('title');
    h3.innerText = title;

    let authorDiv = document.createElement('div');
    authorDiv.classList.add('author');
    authorDiv.innerText = author;

    // parts merging
    bookContainer.appendChild(img);
    anchor.appendChild(h3);

    // main merging !
    book.appendChild(bookContainer);
    details.appendChild(anchor);
    details.appendChild(authorDiv);
    book.appendChild(details);

    return book;


}



document.getElementById("searchbookbutton").onclick = function(event){
    document.getElementById('booksContainer').innerHTML = "";
    event.preventDefault();
    var subject = document.getElementById('bookName').value
    var link = 'https://www.dbooks.org/api/search/';
    var x = new XMLHttpRequest();

    if(subject != "")
    {
        x.onload = function () {
            if (this.status == 200) 
            {
                object = JSON.parse(this.responseText);
                console.log(object);
                if (object['total'] > 0)
                {
                    counter = (object['total'] > 50) ? 50 : (object['total']);
            
                    for (i = 0; i < counter; i++) {
                        document.getElementById('booksContainer').appendChild(getCard(
                            object['books'][i]['title'],
                            object['books'][i]['authors'],
                            object['books'][i]['image'],
                            object['books'][i]['url']
                        ));
                    }
                }
                else
                    alert('Couldn\'t found any book with this tag !');
            }
            else{
                alert("Something Went Wrong !!!");
            }
        }
        x.open('GET' , link+subject , true);
        x.send();
    }
    
    else
    {
        alert('Enter some book name !');
    }

}


