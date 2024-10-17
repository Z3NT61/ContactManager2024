const urlBase = 'http://www.knightsthegerbxyz.online/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = '';
let lastName = '';


// index.html
document.addEventListener('DOMContentLoaded', () => {
  // Attach event listener to the form's submit event
  const loginForm = document.getElementById('loginForm');
  loginForm.addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission
    doLogin(); // Call the doLogin function
  });
});

function doLogin() {
  let userId = 0;
  let firstName = '';
  let lastName = '';

  let login = document.getElementById('username').value;
  let password = document.getElementById('password').value;

  // Clear any previous login result
  document.getElementById('loginResult').innerHTML = '';

  // Prepare the data to be sent in the request
  let tmp = { login: login, password: password };
  let jsonPayload = JSON.stringify(tmp);

  // Define the URL for the login request
  let url = urlBase + '/Login.' + extension;

  let xhr = new XMLHttpRequest();
  xhr.open('POST', url, true);
  xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8');

  try {
    xhr.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        let jsonObject = JSON.parse(xhr.responseText);
        userId = jsonObject.id;

        if (userId < 1) {
          document.getElementById('loginResult').innerHTML =
            'User/Password combination incorrect';
          return;
        }

        firstName = jsonObject.firstName;
        lastName = jsonObject.lastName;

        saveCookie(firstName, lastName, userId); // Save the user's information in a cookie

        // Redirect to the desired page upon successful login
        window.location.href = 'contacts.html';
      }
    };
    xhr.send(jsonPayload);
  } catch (err) {
    document.getElementById('loginResult').innerHTML = err.message;
  }
}

function saveCookie(firstName, lastName, userId) {
  let minutes = 20;
  let date = new Date();
  date.setTime(date.getTime() + minutes * 60 * 1000);
  document.cookie =
    'credentials=firstName=' +
    firstName +
    ',lastName=' +
    lastName +
    ',userId=' +
    userId +
    ';expires=' +
    date.toGMTString();
}

function readCookie() {
  userId = -1;
  let data = document.cookie;
  let splits = data.split(',');
  for (var i = 0; i < splits.length; i++) {
    let thisOne = splits[i].trim();
    let tokens = thisOne.split('=');
    if (tokens[0] == 'firstName') {
      firstName = tokens[1];
    } else if (tokens[0] == 'lastName') {
      lastName = tokens[1];
    } else if (tokens[0] == 'userId') {
      userId = parseInt(tokens[1].trim());
    }
  }

  if (userId < 0) {
    window.location.href = 'index.html';
  } else {
    document.getElementById('heading').innerHTML =
      'Logged in as ' + firstName + ' ' + lastName;
      console.log(userId);
  }
}
// signup.html
document.addEventListener('DOMContentLoaded', () => {
  // Select the form by its ID
  const registerForm = document.getElementById('registerForm');

  // Check if the form element is not null
  if (registerForm) {
    // Attach the event listener to the form
    registerForm.addEventListener('submit', signup);
  } else {
    console.error('Form element not found');
  }
});

function signup(event) {
  event.preventDefault();

  const data = new FormData(event.target);
  let firstName = data.get('firstName');
  let lastName = data.get('lastName');
  let username = data.get('signupName');
  let password = data.get('signupPassword');
  console.log(firstName);

  if (!firstName || !lastName || !username || !password) {
    console.log('Invalid Fields');
    return;
  }

  var signupdata = {
    firstName: firstName,
    lastName: lastName,
    login: username,
    password: password,
  };

  // Send data to PHP
  let url = urlBase + '/register.' + extension;
  let payload = JSON.stringify(signupdata);
  let xhr = new XMLHttpRequest();
  xhr.open('POST', url, true);
  xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8');

  try {
    xhr.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        console.log('added to database');
        // Go back to login page or index.html
        window.location.href = 'index.html';
      }
    };
    xhr.send(payload);
  } catch (err) {
    console.log(err.message);
  }
}

//contacts.html
document.addEventListener('DOMContentLoaded', () => {
  // Open popup when "Add New Contact" button is clicked
  document.getElementById('add').addEventListener('click', openPopup);
  readCookie();
  // Close popup when the close button is clicked
  document.querySelector('.close-btn').addEventListener('click', closePopup);
});

// Function to show the popup
function openPopup() {
  document.getElementById('popup').style.display = 'block';
  document.getElementById('popup-overlay').style.display = 'block'; // Show overlay
}

// Function to close the popup
function closePopup() {
  document.getElementById('popup').style.display = 'none';
  document.getElementById('popup-overlay').style.display = 'none'; // Hide overlay
}

// Function to add a contact
function addContact(event) {
  event.preventDefault(); // Prevent default form submission behavior

  // Collect form data
  const firstName = document.getElementById('firstName').value;
  const lastName = document.getElementById('lastName').value;
  const email = document.getElementById('email').value;

  // Ensure all fields are filled out
  if (!firstName || !lastName || !email) {
    console.log('All fields are required.');
    return;
  }

  // Prepare the data to be sent
  let contactData = {
    firstName: firstName,
    lastName: lastName,
    email: email,
  };
  let jsonPayload = JSON.stringify(contactData);

  // Create a new XMLHttpRequest to send the data to the PHP server
  let xhr = new XMLHttpRequest();
  let url = urlBase + '/addContacts.' + extension;
  xhr.open('POST', url, true);
  xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8');

  // Handle the server's response
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      console.log('Server response:', xhr.responseText);

      // Close the popup after successfully adding the contact
      closePopup();

      // Optionally clear the form fields after submission
      document.getElementById('newContactForm').reset();

      //viewData(); have to update to where when we add a new data it also updates the grid dynamically.
      viewData();
    } else if (xhr.readyState === 4) {
      console.log('Error:', xhr.responseText);
    }
  };

  // Send the JSON payload to the server
  try {
    xhr.send(jsonPayload);
  } catch (err) {
    console.log('Error sending the request:', err.message);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  // Attach the click event listener to the delete button
  document
    .getElementById('delete')
    .addEventListener('click', toggleDeleteContact);
});

let deleteMode = false;

// Function to toggle delete mode and show/hide delete buttons
function toggleDeleteContact() {
  console.log('toggleDeleteContact function called');

  // Toggle delete mode
  deleteMode = true;
  console.log('Delete mode is now:', deleteMode);

  // Re-render the contact list with delete buttons
  viewData();
}

function viewData(event) {
  //event.preventDefault();  // Prevent default behavior (if inside a form, for example)

  const contactsTable = document.getElementById('contactsTable');
  const contactsTableBody = document.querySelector('#contactsTable tbody');
  contactsTable.style.display = 'table';

  // Clear any existing rows in the table
  contactsTableBody.innerHTML = '';

  // Send a request to the server to fetch the user's contacts
  let xhr = new XMLHttpRequest();
  let url = urlBase + '/getContacts.' + extension;
  xhr.open('GET', url, true);
  xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8');

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      const response = JSON.parse(xhr.responseText);

      // dont really need this if statement
      if (response.error) {
        console.log(response.error);
      } else {
        // Populate the table with contacts
        response.contacts.forEach((contact, index) => {
          const row = document.createElement('tr');
          row.setAttribute('data-id', contact.contactId); // Add a data-id for easy row selection

          row.innerHTML = `
                        <td>${contact.firstName}</td>
                        <td>${contact.lastName}</td>
                        <td>${contact.email}</td>

						<td>
                            <div class="edit_buttons">
                                <button class="delete-btn" onclick="deleteContact(${contact.contactId})">Delete</button>
                                <button class="edit-btn" onclick="editToggle(${contact.contactId}, '${contact.firstName}', '${contact.lastName}', '${contact.email}')">Edit</button>
                            </div>
                        </td>

                    `;
          contactsTableBody.appendChild(row);
        });

        // Show the table after fetching contacts
        contactsTable.style.display = 'table';
      }
    } else if (xhr.readyState === 4) {
      console.log('Error fetching contacts:', xhr.responseText);
    }
  };

  xhr.send();
}

function deleteContact(index) {
  // Assuming you have a way to identify which contact to delete (e.g., by ID, not just index)

  // Send a request to delete the contact from the server
  let xhr = new XMLHttpRequest();
  let url = urlBase + '/deleteContacts.' + extension; // Replace with the correct endpoint
  xhr.open('POST', url, true);
  xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8');

  console.log(index);

  let jsonPayload = JSON.stringify({ contactId: index });

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      console.log('Contact deleted successfully.');
      viewData(); // Refresh contact list after deletion
    } else if (xhr.readyState === 4) {
      console.log('Error deleting contact:', xhr.responseText);
    }
  };

  try {
    xhr.send(jsonPayload);
  } catch (err) {
    console.log('Error sending the delete request:', err.message);
  }
}

function editToggle(contactId, firstName, lastName, email) {
  // Get the table row that contains the buttons and other fields.
  const row = document.querySelector(`tr[data-id="${contactId}"]`);

  // Change the row's content to input fields
  row.innerHTML = `
        <td><input type="text" id="firstName_${contactId}" value="${firstName}" /></td>
        <td><input type="text" id="lastName_${contactId}" value="${lastName}" /></td>
        <td><input type="email" id="email_${contactId}" value="${email}" /></td>
        <td>
            <div class="edit_buttons">
                <button class="save-btn" onclick="saveEdit(${contactId})">Save</button>
                <button class="cancel-btn" onclick="cancelEdit(${contactId}, '${firstName}', '${lastName}', '${email}')">Cancel</button>
            </div>
        </td>
    `;
}

function saveEdit(contactId) {
  // Get the updated values from the input fields
  const updatedFirstName = document.getElementById(
    `firstName_${contactId}`
  ).value;
  const updatedLastName = document.getElementById(
    `lastName_${contactId}`
  ).value;
  const updatedEmail = document.getElementById(`email_${contactId}`).value;

  console.log(
    'Updated values:',
    contactId,
    updatedFirstName,
    updatedLastName,
    updatedEmail
  );
  // Now, you would send the updated data to your server (use editContact or similar)
  editContact(contactId, updatedFirstName, updatedLastName, updatedEmail);
}

function cancelEdit(contactId, firstName, lastName, email) {
  // Restore the original row content
  const row = document.querySelector(`tr[data-id="${contactId}"]`);
  row.innerHTML = `
        <td>${firstName}</td>
        <td>${lastName}</td>
        <td>${email}</td>
        <td>
            <div class="edit_buttons">
                <button class="delete-btn" onclick="deleteContact(${contactId})">Delete</button>
                <button class="edit-btn" onclick="editToggle(${contactId}, '${firstName}', '${lastName}', '${email}')">Edit</button>
            </div>
        </td>
    `;
}

function editContact(contactId, firstName, lastName, email) {
  // console.log(contactId, firstName, lastName, email);

  let tmp = {
    contactId: contactId,
    firstName: firstName,
    lastName: lastName,
    email: email,
  };
  let jsonPayload = JSON.stringify(tmp);

  let url = urlBase + '/updateContacts.' + extension;
  let xhr = new XMLHttpRequest();
  xhr.open('POST', url, true);
  xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8');

  try {
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        console.log('Contact updated successfully.');
        viewData(); // Refresh the data after successful update
      } else if (xhr.readyState === 4) {
        console.log('Error fetching contacts:', xhr.responseText);
      }
    };

		xhr.send(jsonPayload);  // Send the request with the payload
	} catch (err) {
		console.log("Error during the request:", err.message);
	}



}


document.getElementById("search").addEventListener("keyup", function () {
    const searchContactItem = this.value.toLowerCase(); // Get the search input value
    readCookie();


    let tmp = {
        userId: userId,
        searchContactItem:searchContactItem,
      };

    let jsonPayload = JSON.stringify(tmp);


    console.log(searchContactItem);
    console.log(userId);

    if (searchContactItem.length > 0) {
        let xhr = new XMLHttpRequest();

        let url = urlBase + '/contactList.' + extension;

        xhr.open("POST", url, true); // Open a POST request
        xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8"); // Set content type

        // Handle the response from the PHP backend
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Parse the JSON response
                console.log('Server response:', xhr.responseText);

                const result = JSON.parse(xhr.responseText);

                if (result.error) {
                    console.error(result.error);
                } else {
                    // Clear the existing table rows
                    const tbody = document.querySelector("#contactsTable tbody");
                    tbody.innerHTML = '';

                    // Populate the table with the new results
                    result.forEach(contact => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${contact.firstName}</td>
                            <td>${contact.lastName}</td>
                            <td>${contact.email}</td>
                            <td><button class="delete-btn" onclick="deleteContact(${contact.ID})">Delete</button></td>
                        `;
                        tbody.appendChild(row);
                    });
                }
            }
        };



        // Send the request
        xhr.send(jsonPayload);
    }
});

