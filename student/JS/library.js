
var link = 'https://www.dbooks.org/api/search/';
var subject = 'Linux'
var x = new XMLHttpRequest();

if (window.screen.width >= 701) {
    document.getElementById('mainLibraryContainer').style.height = (window.screen.height * 85 / 100);
    document.getElementById('libraryContainer').style.height = document.getElementById('mainLibraryContainer').style.height * 80/100
    // document.getElementById('libraryContainer').style.height = (window.screen.height * 6.8 / 10) + "px";
} 
else
{
    document.getElementById('mainLibraryContainer').style.height = (window.screen.height * 85 / 100) +"px";
    document.getElementById('libraryContainer').style.height = (document.getElementById('mainLibraryContainer').style.height * 70/100)+"px";
    alert('yes')
}

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


function showBooks(event)
{
    var subject = document.getElementById('bookName').value
    if(subject != "")
    {
        event.preventDefault();
        x.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) 
            {
                object = JSON.parse(this.responseText);
                if (object['total'] > 0)
                {
                    counter = (object['total'] > 10) ? 10 : (object['total']);
        
                    for (i = 0; i < counter; i++) {
                        document.getElementById('libraryContainer').appendChild(getCard(
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
        }
        x.open('GET', link + subject, true);
        x.send();
    }
    else
    {
        alert('Enter some book name !');
    }
}



// x.open('GET', link + subject, true)
