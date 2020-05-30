const createIcon=(text,prefix=true)=>{
    let i=document.createElement('i');
    i.classList.add('material-icons');
    
    if (prefix) {
      i.classList.add('prefix');
    }
   
    i.innerText=text;
    return i;
  }

const createMessage = (element, message) => {
    element.innerText = message;
   
}
const createElement = (el, parent) => {
    if (parent.querySelector(el)) {
        return parent.querySelector(el);
    }
    let element = document.createElement(el);
    parent.appendChild(element);
    return parent.querySelector(el);
};
const isEmpty = (value) => {
    if (value.trim() === '') return true;
    return false;
}

function triggerEvent(el, type) {
    if ('createEvent' in document) {
        // modern browsers, IE9+
        var e = document.createEvent('HTMLEvents');
        e.initEvent(type, false, true);
        el.dispatchEvent(e);
    } else {
        // IE 8
        var e = document.createEventObject();
        e.eventType = type;
        el.fireEvent('on' + e.eventType, e);
    }
}

const isValidForm = () => {

    for (const key in isValid) {
        if (!isValid[key]) {
            submit.disabled = true;
            return;

        }
    }
   
    submit.disabled = false;
}




function triggerForm() {
   
    if (title.value !== "") {
        triggerEvent(title, 'input');
    }
    if (content.value !== "") {
        triggerEvent(content, 'input');
    }
    if ($eventSelect.value !== "") {
        triggerEvent(category, 'change');
    }
    
   
}

const chechLength = (value, min = 1, max = 20) => {
  
    
    if (value.length >= min && value.length <= max) return true;
     return false;
}
let addStyleElement = (messageElement, element) => {
   // messageElement.style.marginLeft = "3rem";
   messageElement.style.width="100%";
  
    messageElement.style.color = "#F44336";
    element.classList.add("invalid");
    isValidForm();
    

};
let removeStyleElements = element => {
    element.classList.remove("invalid");
    isValidForm()
   
};

function isValidEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}
const chechPasswordMatch = (pass1, pass2) => {
    if (pass1 === pass2) return true;
    return false;
}

const checkIsNumeric = (value) => {
    return isNaN(value);
}
const checkIsNumericRange = (value, min = 1, max = 30) => {
    if (isNaN(value) && value >= min && value <= max) {
        return true;
    }
    return false;
}
//Input Password and Submit [6 to 20 characters which contain at least one numeric digit, one uppercase and one lowercase letter]
const CheckPassword = (value) => {
    let passw=/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/;
    // var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
    return value.match(passw);
}

function resetForm() {
   
    email.value = "";
    password.value = "";
    
}

function triggerFormRegister() {
   
    if (email.value !== "") {
        triggerEvent(email, 'input');
    }
    if (password.value !== "") {
        triggerEvent(password, 'input');
    }
    // if (confirmPassowrd.value !== "") {
    //     triggerEvent(confirmPassowrd, 'input');
    // }
    if (firstName.value !== "") {
        triggerEvent(firstName, 'input');
    }
    if (lastName.value !== "") {
        triggerEvent(lastName, 'input');
    }
   
}

function triggerFormLogin() {
   
    if (email.value !== "") {
        triggerEvent(email, 'input');
    }
    if (password.value !== "") {
        triggerEvent(password, 'input');
    }
   
}
function triggerFormfocusout() {
   
    if (email.value !== "") {
        triggerEvent(email, 'focusout');
    }
    if (password.value !== "") {
        triggerEvent(password, 'focusout');
    }
   
}