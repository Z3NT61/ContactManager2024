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
            }
        };
        xhr.send(payload);
    }
    catch(err){
        //error message
    }

}

loginButton.addEventListener("click", function(e){
    login();
    window.location.assign('contacts.html');
});
