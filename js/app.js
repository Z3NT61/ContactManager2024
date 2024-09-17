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

// Register a new user
function register() {
    // Get the form data
    const firstName = document.getElementById('firstname').value;
    const lastName = document.getElementById('lastname').value;
    const email = document.getElementById('email').value;
    const loginName = document.getElementById('user').value;
    const password = document.getElementById('password').value;

    // Create a data object
    const formData = new FormData();
    formData.append('firstName', firstName);
    formData.append('lastName', lastName);
    formData.append('email', email);
    formData.append('loginName', loginName);
    formData.append('loginPassword', password);

    // Send the form data to createUser.php using fetch
    fetch(`${urlBase}/createUser.${extension}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) // Parse response as text
    .then(data => {
        // Handle the response from createUser.php
        if (data.includes("Error") || data.includes("exists")) {
            // Show error messages
            document.querySelector('.error-message').innerHTML = data;
        } else {
            // Redirect to contacts.html if registration is successful
            window.location.href = 'contacts.html';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.querySelector('.error-message').innerHTML = "An error occurred during registration.";
    });
}

registerButton.addEventListener("click", function(e) {
    e.preventDefault(); // Prevent the default button click behavior
    register(); // Call the register function
});