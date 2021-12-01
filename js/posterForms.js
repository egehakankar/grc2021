// object to store session data
let session = {
    email: undefined,
    password: undefined,
    firstname: undefined,
    lastname: undefined,
    poster: undefined
};

// POST HTTP request
function post(url, payload) {
    return new Promise((resolve, reject) => {
        let request = new XMLHttpRequest();
        request.open('POST', url, true);
        request.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

        request.onload = () => {
            if (request.status === 200) {
                resolve(request.response);
            } else {
                reject(Error(request.statusText));
            }
        };

        request.onerror = () => {
            reject(Error("request failed"));
        };
        request.send(JSON.stringify(payload));
    });
}

function clearInputs() {
    let inputs = document.getElementsByTagName('input');
    for (let i of inputs) {
        if (i.type === "text") {
            i.value = "";
        }
    }
}

let handlers = {
    registerFormHandler: function() {
        let payload = {
            id: document.getElementById("register-id").value,
            firstname: document.getElementById("register-firstname").value,
            lastname: document.getElementById("register-lastname").value,
            email: document.getElementById("register-email").value
        };

        if (payload.id === "") {
            alert("Please enter your ID number.")
        } else if (!/^\d+$/.test(payload.id)) {
            // check if id number consists of only digits
            alert("Please enter a valid ID number.")
        } else if (payload.firstname === "") {
            alert("Please enter your first name.")
        } else if (payload.lastname === "") {
            alert("Please enter your last name.")
        } else if (payload.email === "") {
            alert("Please enter your e-mail.")
        } else {
            post("endpoint/poster_register.php", payload).then(response => {
                let data = JSON.parse(response);
                if (data["status"] === "OK") {
                    clearInputs();
                    alert("Your password has been sent to your e-mail.");
                } else if (data["error"] === "invalid email") {
                    alert("Please enter a valid Bilkent e-mail.");
                }
            });
        }
    },

    loginFormHandler: function() {
        let payload = {
            email: document.getElementById("login-email").value,
            password: document.getElementById("login-password").value
        };

        post("endpoint/poster_login.php", payload).then(response => {
            let data = JSON.parse(response);
            if (data["status"] === "OK") {
                clearInputs();

                session["email"] = payload["email"];
                session["password"] = payload["password"];
                session["firstname"] = data["firstname"];
                session["lastname"] = data["lastname"];
                session["poster"] = data["poster"];

                toggleShowElement(document.getElementById("register-form"), false);
                toggleShowElement(document.getElementById("login-form"), false);

                let uploadForm = document.getElementById("upload-form");
                toggleShowElement(uploadForm, true);
                let logoutBtn = document.getElementById("logout-btn");
                toggleShowElement(logoutBtn, true);

                let welcome = "Welcome, " + session["firstname"] + " " + session["lastname"] + ".";
                document.getElementById("upload-welcome").innerText = welcome;

                if (data["poster"] === null) {
                    document.getElementById("upload-info").innerText = "You have not made a submission."
                } else {
                    let link = "<a target='_blank' href='" + data["poster"] + "'>Link to your latest submission</a>";
                    document.getElementById("upload-info").innerHTML = link;
                }
            } else {
                alert("Login failed.");
            }
        });
    },

    uploadFormHandler: function(e) {
        e.preventDefault();

        const files = document.querySelector('[type=file]').files;
        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            let file = files[i];

            formData.append('files[]', file);
            formData.append("email", session["email"]);
            formData.append("password", session["password"]);
        }

        fetch("endpoint/poster_submit.php", {
            method: 'POST',
            body: formData
        }).then(response => {
            return response.json()
        }).then(body => {
            console.log(body);
            if (body["status"] === "OK") {
                let link = "<a target='_blank' href='" + body["poster"] + "'>Link to your latest submission</a>";
                document.getElementById("upload-info").innerHTML = link;
                alert("Upload successful.");
            } else if (body["error"] === "not pdf") {
                alert("Upload failed. Only pdf files are allowed.");
            } else if (body["error"] === "oversize") {
                alert("Upload failed. Max file size is " + body["limit"] + "bytes.");
            }
        });
    },

    logoutButtonHandler: function() {
        session = {
            email: undefined,
            password: undefined,
            firstname: undefined,
            lastname: undefined
        };

        let uploadForm = document.getElementById("upload-form");
        toggleShowElement(uploadForm, false);
        let logoutBtn = document.getElementById("logout-btn");
        toggleShowElement(logoutBtn, false);

        toggleShowElement(document.getElementById("register-form"), true);
        toggleShowElement(document.getElementById("login-form"), true);
    }
};

function toggleShowElement(elem, flag) {
    if (flag) {
        elem.style["display"] = "block";
        elem.style["visibility"] = "visible";
    } else {
        elem.style["display"] = "none";
    }
}

document.getElementById("register-btn").addEventListener("click", () => handlers.registerFormHandler());
toggleShowElement(document.getElementById("register-form"), true);

document.getElementById("login-btn").addEventListener("click", () => handlers.loginFormHandler());
toggleShowElement(document.getElementById("login-form"), true);

let logoutBtn = document.getElementById("logout-btn");
logoutBtn.addEventListener("click", () => handlers.logoutButtonHandler());
toggleShowElement(logoutBtn, false);

let uploadForm = document.getElementById("upload-form");
uploadForm.addEventListener('submit', e => handlers.uploadFormHandler(e));
toggleShowElement(uploadForm, false);
