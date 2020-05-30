
let title=document.getElementById('news_title');
let content=document.getElementById('news_content');
 let category=document.getElementById('news_categories');

let submit=document.getElementById('submit');

let isValid = {
    titleIsValid: false,
    contentIsValid: false,
    categoryIsValid: false,

  }

  const init = (event) => {
    let small = createElement("small", event.target.parentElement);
    const value = event.target.value;
    return {
        small,
        value
    };
}

const initForSelect = (event) => {
    let small = createElement("small", document.getElementById('news_categories').parentElement);
    const value = event.target.value;
   
    return {
        small,
        value
    };
}


const inputValid = (object, event) => {
    const {
      small,
      value
    } = init(event);
    if (isEmpty(value)) {
    //  createMessage(small, object.message);
      isValid[object.isvalid] = false;
      addStyleElement(small, event.target);
     
      return false;
    } else {
        console.log(event);
        
      small.innerText = "";
      removeStyleElements(event.target);
      isValid[object.isvalid] = true;
      return true;
    }
  }

  const selectValid=(object, event) => {

    const {
        small,
        value
      } = initForSelect(event);
      if (!value) {
      //  createMessage(small, object.message);
        isValid[object.isvalid] = false;
        addStyleElement(small, event.target);
       
        return false;
      } else {
          console.log(event);
          
        small.innerText = "";
        removeStyleElements(event.target);
        isValid[object.isvalid] = true;
          console.log(isValid);
          isValidForm()
        return true;
      }
    
  }

  title.addEventListener(
    "input",
    inputValid.bind(null, {
      message: "this title  is required",
      isvalid: 'titleIsValid',
      min: 1,
      max: 20
    })
  );

 
 
  var $eventSelect = $("#news_categories");
  $eventSelect.select2();
  $eventSelect.on("change", selectValid.bind(null, {
    message: "this category  is required",
    isvalid: 'categoryIsValid'
  }));
  
  triggerForm()
  isValidForm();