
let firstName=document.getElementById('account_firstName');
let lastName=document.getElementById('account_lastName');
let email=document.getElementById('account_email');
let password=document.getElementById('account_password');


// let category=document.querySelector('.select2 ');

// console.log(category);

let submit=document.getElementById('submit');

let isValid = {
  
    firstNameIsValid: firstName ? false : true,
    lastNameIsValid: lastName ? false : true,
    // confirmPassowrdIsvalid: confirmPassowrd ? false : true,
    
    emailIsValid: false,
    passwordIsValid: false,
  
  }

  const init = (event) => {
    let small = createElement("small", event.target.parentElement);
    small.classList.add('small-validation')
    const value = event.target.value;
    return {
        small,
        value
    };
}


const NameValid = (object, event) => {
    const {
      small,
      value
    } = init(event);
    if (isEmpty(value)) {
      createMessage(small, object.message);
      isValid[object.isvalid] = false;
      addStyleElement(small, event.target);
     
      return false;
    } else {
      small.innerText = "";
      removeStyleElements(event.target);
      isValid[object.isvalid] = true;
      return true;
    }
  }
  
  
  
  const emailValid = (object, event) => {
    const {
      small,
      value
    } = init(event);
    if (isEmpty(value)) {
      createMessage(small, object.message);
      isValid[object.isvalid] = false;
      addStyleElement(small, event.target);
      
      return false;
    } else if (object.messageEmaile) {
      if (!isValidEmail(event.target.value)) {
        createMessage(small, object.messageEmaile);
        isValid[object.isvalid] = false;
        addStyleElement(small, event.target);
        
        return false;
      } else {
        small.innerText = "";
        isValid[object.isvalid] = true;
        removeStyleElements(event.target);
       
        return true;
      }
    } else {
      small.innerText = "";
      isValid[object.isvalid] = true;
      removeStyleElements(event.target);
     
      return true;
    }
  }
  const passwordValid = (object, event) => {
    const {
      small,
      value
    } = init(event);
    if (isEmpty(value)) {
      createMessage(small, object.message);
      isValid[object.isvalid] = false;
      addStyleElement(small, event.target);
      
      return false;
    } else if (!chechLength(value, 6, 20)) {
      createMessage(small, object.minMessage);
      isValid[object.isvalid] = false;
      addStyleElement(small, event.target);
      
      return false;
    } else if (!CheckPassword(value)) {
      
      createMessage(small, object.passMessage);
      isValid[object.isvalid] = false;
      addStyleElement(small, event.target);
      
      return false;
    } else {
      small.innerText = "";
      isValid[object.isvalid] = true;
      removeStyleElements(event.target);
     
      return true;
    }
  }


  ////////////////////////////
  
  //////////////////////// input event
  firstName.addEventListener(
    "input",
    NameValid.bind(null, {
      message: "this first name is required",
      isvalid: 'firstNameIsValid',
      min: 1,
      max: 20
    })
  );
  lastName.addEventListener(
    "input",
    NameValid.bind(null, {
      message: "this last  name is required",
      isvalid: 'lastNameIsValid',
      min: 1,
      max: 20
    })
  );
  
  email.addEventListener(
    "input",
    emailValid.bind(null, {
      message: "this email is required",
      messageEmaile: "this is not valid email",
      isvalid: 'emailIsValid'
    })
  );
  password.addEventListener(
    "input",
    passwordValid.bind(null, {
      message: "this password is required",
      isvalid: 'passwordIsValid',
      min: 6,
      max: 20,
      minMessage: 'must have at least 1',
      maxMessage: 'must have at most 20 ',
      passMessage: 'Input Password and Submit [6 to 20 characters which contain at least one numeric digit, one uppercase and one lowercase letter]'
    })
  );


  
  triggerFormRegister();

  
  
  ///////////////////From SUBMIT
 
  isValidForm();
