const urlBase = 'http://www.knightsthegerbxyz.online/LAMPAPI';
const extension = 'php';

let loginButton = document.getElementById('login');
let registerButton = document.getElementById('register');

// login existing users
function login() {
    let login = document.getElementById('user').value;
    let password = document.getElementById('password').value;
    let loginString = {login:login, password:password};
    let payload = JSON.stringify(loginString);

    // prints login info
    console.log(payload);
    // ...
    // implement retrieval of user's contacts info from database
    let url = urlBase + "/login." + extension;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try{
        xhr.onreadystatechange = function(){
            if(this.readyState == 4 && this.status == 200){
                let jsonObject = JSON.parse(xhr.responseText);
                userID = jsonObject.id;

                if(userID < 1){ //this user does not exist
                    //to do
                    return;
                }
		    window.location.href = "contacts.html";
            }

        };
        xhr.send(payload);
    }
    catch(err){
        //error message
    }

}

function register() {
    let user = document.getElementById('user').value;
    let email = document.getElementById('email').value;
    let firstname = document.getElementById('firstname').value;
    let lastname = document.getElementById('lastname').value;
    let password = document.getElementById('password').value;

    let string = {firstname:firstname, lastname:lastname, email:email, user:user, password:password};
    let payload = JSON.stringify(string);

    let url = urlBase + "/createUser." + extension;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);

    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try{
        xhr.onreadystatechange = function(){
            if(this.readyState == 4 && this.status == 200){
                let jsonObject = JSON.parse(xhr.responseText);
                console(jsonObject);
                window.location.href = "index.html";
            }

        };
        xhr.send(payload);
    }
    catch(err){
        //error message
        console.log(err.message);
        return;
    }

}
