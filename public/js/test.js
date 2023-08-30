/**
 * @param {string} name 
 */
function addHello(name) {
    $.ajax({
      type:"POST",
      cache:false,
      url: `/test/hello/${name}`,
        success: function (data) {
            console.log(data);
        console.log("name added");
        console.log(name);
      }
    });
    //window.location.reload();
  }
    
    let refreshButton = document.getElementById("refresh_button");
  //let commentButtons = document.getElementsByClassName("comment_button");
  //let commentInputs = document.getElementsByClassName("comment_input");
  
  //for (let element of commentButtons) {
    //console.log(element);
refreshButton.onclick = function () {
        
      //let name = element.previousElementSibling.value;
        let name = "Lea";
      addHello(name);
    }
  //}