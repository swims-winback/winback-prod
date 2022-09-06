$(document).ready(function (){
  
  /* when page refreshed, cancel selection */
  if (window.performance) {
    console.info("window.performance works fine on this browser");
  }
  //console.info(performance.navigation.type);
  if (performance.navigation.type == performance.navigation.TYPE_RELOAD) {
    //console.info( "This page is reloaded" );
    let selectedButton = document.querySelectorAll(".form-check-input");
    for(let button of selectedButton){
      let substr_id = button.getAttribute("id");
      let id = substr_id.substr(9);
      button.checked = false;
      let xmlhttp = new XMLHttpRequest;
      if (id != 0) {
        xmlhttp.open("GET", `/selected/${id}/${0}`)
        xmlhttp.send()
      }
    }
  } else {
    console.info( "This page is not reloaded");
  }


  /* set device active in db to show connect button and allow connection*/
  setInterval(function (){
    let deviceArray = document.querySelectorAll(".info_device")
    for (let device of deviceArray) {
      var deviceId = $(device).data("id");
      $.ajax({    
        type: "GET", 
        url: `/isactive/${deviceId}`,          
        dataType: "html",                  
        success: function(data){ 
          if ($.trim(data)==1){   
            $(device).addClass('bg-green btn-outline-green')
            var deviceSn = $(device).data("title");
            //document.getElementById(`c_sn_${deviceSn}`).classList.remove("none");
            document.getElementById(`c_sn_${deviceSn}`).style.display = "block";
          } 
          else  
          {    
            $(device).addClass('bg-orange btn-outline-orange')
            var deviceSn = $(device).data("title");
            //console.log(document.getElementById(`c_sn_${deviceSn}`));
            if (deviceSn && document.getElementById(`c_sn_${deviceSn}`)) {
              document.getElementById(`c_sn_${deviceSn}`).style.display = "none";
            }
            
          }                 
        }
      });
    }
    /*
    setTimeout(() => {
      document.location.reload();
    }, 3000);
    */
  }, 1000);


	// ######## Validate version ######## //
	//let updateButton = document.querySelectorAll(".update")
	let updateZone = document.querySelectorAll(".update-zone")
	for(let zone of updateZone) {
		zone.addEventListener("change", function() {
			id = zone.getAttribute('data-id');
			//id_substr = id.substr(7);
			version = zone.value;
			//url = `{{path('updated', {'id': ${id}, 'version': ${version}})}}`
			url = `/updated/${id}/${version}`
			document.querySelector(`#update_${id}`).href = url;
		})
	}


	// ######### Switch function ######### //
	let forcedButton = document.getElementsByName("switchbox")
	for(let button of forcedButton){
		let substr_id = button.getAttribute("data-id");
		let vButton = document.getElementById(`update_${substr_id}`)
		//vButton.addEventListener("click", function(){
    button.addEventListener("click", function(){  
			let $id = button.getAttribute("data-id");
			if (button.checked == true) {
        console.log(button.checked);
			  	let xmlhttp = new XMLHttpRequest;
			  	xmlhttp.open("GET", `/forced/${$id}/${1}`)
			  	xmlhttp.send()
			}
			else{
			  	let xmlhttp = new XMLHttpRequest;
			  	xmlhttp.open("GET", `/forced/${$id}/${0}`)
			  	xmlhttp.send()
			}
		})
	}

	// ######### Selected function ######### //
	/* select devices in db */
	// if button clicked & button checked, item selected in db
	//let selectedButton = document.querySelectorAll(".form-check-input");
	let selectedButton = document.getElementsByClassName('form-check-input');
	for(let button of selectedButton){
		let substr_id = button.getAttribute("id");
		let id = substr_id.substr(9);
		//console.log(button);
		//button.addEventListener("click", function(){
		button.onclick = function() {
			let xmlhttp = new XMLHttpRequest;
			//console.log(button);
			if (id != 0 && button.checked == true) {
				
				xmlhttp.open("GET", `/selected/${id}/${1}`)
				xmlhttp.send()
			}
			else if (id != 0 && button.checked == false) {
				xmlhttp.open("GET", `/selected/${id}/${0}`)
				xmlhttp.send()
			}
		}
	}


	/* if checkbox_0 is clicked and is checked, query all checkbox in html, change checkbox.checked's value according to value for checkbox_0, select item in db */
	let checkbox_0 = document.querySelector('#checkbox_0');
	checkbox_0.onclick = function() {
	  if (checkbox_0.checked == true) {
      var checkboxes = document.getElementsByClassName('form-check-input');
      for (var i=1; i < checkboxes.length; i++) {
        let id = checkboxes[i].getAttribute("data-id");
        checkboxes[i].checked = true;
        let xmlhttp = new XMLHttpRequest;
        xmlhttp.open("GET", `/selected/${id}/${1}`)
        xmlhttp.send()
      }
	  }
	  else {
		var checkboxes = document.getElementsByClassName('form-check-input');
		for (var i=1; i < checkboxes.length; i++) {
		  let id = checkboxes[i].getAttribute("data-id");
		  checkboxes[i].checked = false;
		  let xmlhttp = new XMLHttpRequest;
		  xmlhttp.open("GET", `/selected/${id}/${0}`)
		  xmlhttp.send()
		}
	  }
	}

});

//window.onload = () => {


  // ######### Delete function ######### //
  
  /*
  let deleteButton = document.querySelectorAll(".modal-trigger")
  for(let button of deleteButton) {
      button.addEventListener("click", function() {
          $id = button.getAttribute("data-id");
          $sn = button.getAttribute("data-title");
          //console.log($id);
          document.querySelector(".modal-footer a").href = `/admin/device/delete/${$id}`
          //document.querySelector(".modal-content").innerText = ``
      })
  }
  */


  

  // ============ Check all elements =========== //
  /*
  function check(source) {
    let checkboxes = document.getElementsByName('checkbox');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
  }
  */


  /*
  if (checkbox_0.checked == false) {
    var checkboxes = document.getElementsByClassName('form-check-input');
    //for (var checkbox of checkboxes) {
    for (var i=1; i < checkboxes.length; i++) {

      //let id = checkbox.getAttribute("data-id");
      //checkbox.checked = this.checked;
      let id = checkboxes[i].getAttribute("data-id");
      checkboxes[i].checked = false;
      let xmlhttp = new XMLHttpRequest;
      //console.log(checkboxes[i]);
      //console.log(id);
      xmlhttp.open("GET", `/admin/device/unselected/${id}`)
      xmlhttp.send()
    }
  }
  */
  
  
  // =========== check to modify version upload field ============ //

  //let checkbox_array = document.getElementsByName('checkbox_item');

  //let checkbox_0 = document.querySelector('#checkbox_0');
  //let validButton_0 = document.querySelector('#valid_0');
 

  /*
  var originalHTML = test_zone_1.innerHTML;
  checkbox_1.addEventListener('click', () => {
    checkbox_1.checked == true;
    if(checkbox_1.checked) {
      //var form = test_zone_1.dataset.form;
      
      //console.log($id);
      test_zone_1.innerHTML = `<input type="text" id="input_${$id}"/>`;
      input_1 = document.querySelector(`#input_${$id}`);
      id = $id;

      validButton.addEventListener('click', () => {
        //console.log(validButton);
        //test_zone_1.addEventListener('change', () => {
        let xmlhttp = new XMLHttpRequest;
        //console.log(input_1.value);
        $user_input = input_1.value;
        console.log(id);
        xmlhttp.open("GET", `/admin/device/update/${id}/${$user_input}`)
        xmlhttp.send()
      });
    
    }
    else {
      test_zone_1.innerHTML = originalHTML;
    }
  })
  */



  /*
  for(let button of checkbox){
      $id = button.getAttribute("data-id");
      let test_zone = document.getElementById(`test_zone_${$id}`);
      var originalHTML = test_zone.innerHTML;
      //button.addEventListener('click', function (){
        if(button.checked){
          test_zone.innerHTML = "blublu";
        }
        else {
          test_zone.innerHTML = originalHTML;
        }

      //});
  }
  */

  // ============ Switch all elements =========== //
  /*
  function toggle(source, name) {
    switchboxes = document.getElementsByName(name);
    for(var i=0, n=switchboxes.length;i<n;i++) {
        switchboxes[i].checked = source.checked;
    }
    for(let button of switchboxes){
      button.addEventListener("click", function(){
        let xmlhttp = new XMLHttpRequest;
        console.log(this.dataset.id);
        xmlhttp.open("GET", `/admin/device/forced/${this.dataset.id}`)
        xmlhttp.send()
      })
    }
  }
  */
  //checkbox_0.addEventListener('click', check(this));
  //onClick="toggle(this, 'checkbox')
//}