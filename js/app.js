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
}

loginButton.addEventListener("click", function(e){
    login();
    window.location.assign('contacts.html');
});
