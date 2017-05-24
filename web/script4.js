window.onload = function () {
    var modalElem = document.getElementById('myModal');
    var modalCloseElem = document.getElementsByClassName("close")[0];
    var btn = document.getElementById("myBtn");

    var buttonRename = document.getElementsByClassName('rename-file');
    var renameBlock = document.getElementsByClassName('rename-block');
    var buttonReplace = document.getElementsByClassName('replace-file');
    var replaceBlock = document.getElementsByClassName('replace-block');
    var fileName = document.getElementsByClassName('file-name');
    var directoryUrl = document.getElementsByClassName('directory-url');
    var createBlockDir = document.getElementsByClassName('create-block-dir');
    var buttonCreateDirectory = document.getElementsByClassName('create-directory');
    var renameBlockDir = document.getElementsByClassName('rename-block-dir');
    var buttonRenameDirectory = document.getElementsByClassName('rename-directory');
    var directoryParentUrl = document.getElementsByClassName('url-parent');

    var moveFileBlock = document.getElementsByClassName('move-file-block');
    var buttonMoveFile = document.getElementsByClassName('move-file');
    var listDirectories = document.getElementsByClassName('list-directories');
    var fileUrl = document.getElementsByClassName('file-url');

    var moveDirectoryBlock = document.getElementsByClassName('move-directory-block');
    var buttonMoveDirectory = document.getElementsByClassName('move-directory');
    var listDirectoriesDir = document.getElementsByClassName('list-directories-dir');

    var uploadElem = document.getElementById("form-upload");
    var myFileElem = document.getElementById("myFile");
    var newFileElem = document.getElementById('new-file-upload');


// When the user clicks on <span> (x), close the modal
if(modalCloseElem != undefined){
modalCloseElem.onclick = function() {
    modalElem.style.display = "none";
}
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modalElem) {
        modalElem.style.display = "none";
    }
}
    myFileElem.onchange = function () {
        if (newFileElem != undefined && newFileElem != null) {
            newFileElem.parentNode.removeChild(newFileElem);
        }
        var urlFile = this.value;
        var nameFile = urlFile.split("\\");
        size = nameFile.length;
        var newElem = document.createElement("input");
        newElem.setAttribute("type", "text");
        newElem.setAttribute("id", "new-file-upload");
        newElem.setAttribute("name", "new-file-upload");
        newElem.setAttribute("value", nameFile[size - 1]);
        uploadElem.appendChild(newElem);
        newFileElem = document.getElementById('new-file-upload');
    }

    for (var i = 0; i < buttonRename.length; i++) {
        buttonRename[i].onclick = function () {
            var ind = parseInt(this.toString());
            renameBlock[ind].innerHTML = "<form method='post' action='?action=rename'><input type='hidden' name='url-file' value='" + fileUrl[ind].textContent + "'><input type='hidden' name='name-file' value='" + fileName[ind].textContent + "'><input type='text' name='rename'><input type='submit' id='rename-ok' value='ok rename'></form>";
        }.bind(i);
    }

    for (var i = 0; i < buttonReplace.length; i++) {
        buttonReplace[i].onclick = function () {
            var ind = parseInt(this.toString());
            formReplace = "<form method='post' enctype='multipart/form-data' action='?action=replace'>";
            formReplace += "<input type='hidden' name='MAX_FILE_SIZE' value='10000000' />";
            formReplace += "Replace this file by: <input type='file' name='monfichier' />";
            formReplace += "<input type='hidden' name='what-function' value='3'>";
            formReplace += "<input type='hidden' name='url-file' value='" + fileUrl[ind].textContent + "'>";
            formReplace += "<input type='submit' class='bof' value='Replace'/></form>";
            replaceBlock[ind].innerHTML = formReplace;
        }.bind(i);
    }

    //EVENT FOR CREATE DIRECTORY INSIDE A DIRECTORY
    for (var i = 0; i < buttonCreateDirectory.length; i++) {
        buttonCreateDirectory[i].onclick = function () {
            var ind = parseInt(this.toString());
            createBlockDir[ind].innerHTML = "<form method='post' action='?action=createDirectory'><input type='hidden' name='url-directory' value='" + directoryUrl[ind].textContent + "'><input type='text' name='directory_name'><input type='submit' id='create-dir-ok' value='ok create'></form>";
        }.bind(i);
    }

    //EVENT FOR RENAME A DIRECTORY
    for (var i = 0; i < buttonRenameDirectory.length; i++) {
        buttonRenameDirectory[i].onclick = function () {
            var ind = parseInt(this.toString());
            renameBlockDir[ind].innerHTML = "<form method='post' action='?action=renameDirectory'><input type='hidden' name='url-directory' value='" + directoryUrl[ind].textContent + "'><input type='hidden' name='url-parent' value='" + directoryParentUrl[ind].textContent + "'><input type='text' name='directory-rename'><input type='submit' id='rename-ok' value='ok rename'></form>";
        }.bind(i);
    }

    //EVENT FOR MOVE A FILE
    for (var i = 0; i < buttonMoveFile.length; i++) {
        buttonMoveFile[i].onclick = function () {
            var ind = parseInt(this.toString());
            formReplace = "<form method='post'  action='?action=moveFile'><input type='hidden' name='name-file' value='" + fileName[ind].textContent + "'><input type='hidden' name='url-file' value='" + fileUrl[ind].textContent + "'>";
            formReplace += listDirectories[ind].innerHTML;
            formReplace += '<input type="submit" id="move-ok" value="ok move"></form>';
            moveFileBlock[ind].innerHTML = formReplace;
        }.bind(i);
    }

    //EVENT FOR MOVE A DIRECTORY
    for (var j = 0; j < buttonMoveDirectory.length; j++) {
        buttonMoveDirectory[j].onclick = function () {
            var ind = parseInt(this.toString());
            formReplace = "<form method='post'  action='?action=moveDirectory'><input type='hidden' name='url-directory' value='" + directoryUrl[ind].textContent + "'>";
            formReplace += listDirectoriesDir[ind].innerHTML;
            formReplace += '<input type="submit" id="move-ok" value="ok move"></form>';
            moveDirectoryBlock[ind].innerHTML = formReplace;
        }.bind(j);
    }

};

