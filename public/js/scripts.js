let checkbox_0 = document.querySelector('#checkbox_0');
var checkboxes = document.getElementsByClassName('device-check');
let selectedButton = document.querySelectorAll(".device-check");
let selectedZone = document.getElementById('selectedZone');
let updateZone = document.querySelectorAll(".update-zone");
//let forcedButton = document.getElementsByName("switchbox");
let deviceArray = document.querySelectorAll(".info_device"); //get all the info modals


  /* when page refreshed, cancel selection */
  if (window.performance) {
    console.info("window.performance works fine on this browser");
  }
  //console.info(performance.navigation.type);
  if (performance.navigation.type == performance.navigation.TYPE_RELOAD) {
    //console.info( "This page is reloaded" );
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
    if (checkbox_0) {
      checkbox_0.checked = false;
    }
  } else {
    console.info( "This page is not reloaded");
  }


  /* set device active in db to show connect button and allow connection*/
  /*
  setInterval(function (){
    for (let device of deviceArray) {
      var deviceId = $(device).data("id");
      var deviceElem = document.getElementById(`info_device_${deviceId}`);
      var request = $.ajax({    
        type: "GET", 
        url: `/isactive/${deviceId}`,          
        dataType: "html",                  
        success: function(data){ 
          if (data == 1){  
            //$(device).addClass('bg-green btn-outline-green')
            var deviceSn = $(device).data("title");
            //print(deviceSn);
            if (deviceSn && document.getElementById(`c_sn_${deviceSn}`)) {
              document.getElementById(`c_sn_${deviceSn}`).style.display = "block";
              //$(deviceElem).removeClass('bg-orange').addClass('bg-green');
              $(deviceElem).addClass('bg-green');
            }

          } 
          else  
          {   
            var deviceSn = $(device).data("title");
            if (deviceSn && document.getElementById(`c_sn_${deviceSn}`)) {
              document.getElementById(`c_sn_${deviceSn}`).style.display = "none";
            }
            //$(deviceElem).removeClass('bg-green').addClass('bg-orange');
            //$(deviceElem).addClass('bg-orange');
            //$(deviceElem).removeClass('bg-green')
          }                 
        }
      });

    }
    */

    /*
    let downloadArray = document.querySelectorAll(".progress-bar")
    for (let download of downloadArray) {
      //var downloadValue = $(download).data("value");
      $.ajax({    
        type: "GET", 
        url: `/download/${deviceId}`,          
        dataType: "html",                  
        success: function(data){ 
          if (data != 0) {
            $(download).width(data+"%");
            $(download).html(data+"%");
          }
          if (data == 100) {
            $(download).addClass('bg-green')
          }               
        }
      });
    }
    */

    /*
    setTimeout(() => {
      document.location.reload();
    }, 3000);
    */
  //}, 3000);


	// ######## Validate version ######## //
  //TODO Update in modal
	for(let zone of updateZone) {
		zone.addEventListener("change", function() {
			id = zone.getAttribute('data-id');
			version = zone.value;
			url = `/updated/${id}/${version}`
			document.querySelector(`#update_${id}`).href = url;
		})
	}
  
// ######### CHANGE FORCED ######### //
/**
 * Change force device status to 1 or 0
 * @param {int} id - device id
 * @param {boolean} forced - device forced status
 */
function addForce(id, forced) {
  $.ajax({
    type: "POST",
    cache: false,
    url: `/forced/${id}/${forced}`,
    success: function () {
      console.log("device forced")
      console.log(forced)
    }
    })
}
  
  let forcedButton = document.getElementsByName("switchbox");
	for(let button of forcedButton){
    button.addEventListener("click", function(){  
			let id = button.getAttribute("data-id");
			if (button.checked == true) {
        console.log(button.checked);
        addForce(id, 1);
			}
      else {
        addForce(id, 0);
			}
		})
	}
  
	// ######### Selected function ######### //
	/* select devices in db */
	// if button clicked & button checked, item selected in db

	for(let button of selectedButton){
		button.onclick = function() {
      let substr_id = button.getAttribute("id");
      let id = substr_id.substr(9);
      
			if (id != 0 && button.checked == true) {
				//console.log(button.checked);
        let xmlhttp = new XMLHttpRequest;
				xmlhttp.open("GET", `/selected/${id}/${1}`)
				xmlhttp.send()
			}
			else if (id != 0 && button.checked == false) {
        let xmlhttp = new XMLHttpRequest;
				xmlhttp.open("GET", `/selected/${id}/${0}`)
				xmlhttp.send()
			}
      
		}
	}


	/* if checkbox_0 is clicked and is checked, query all checkbox in html, change checkbox.checked's value according to value for checkbox_0, select item in db */
  if (checkbox_0) {
    checkbox_0.onclick = function() {
      if (checkbox_0.checked == true) {
        for (var i=0; i < checkboxes.length; i++) {
          let id = checkboxes[i].getAttribute("data-id");
          checkboxes[i].checked = true;
          
          let xmlhttp = new XMLHttpRequest;
          xmlhttp.open("GET", `/selected/${id}/${1}`)
          xmlhttp.send()
          
        }
      }
      else {
        for (var i=0; i < checkboxes.length; i++) {
          let id = checkboxes[i].getAttribute("data-id");
          checkboxes[i].checked = false;
          
          let xmlhttp = new XMLHttpRequest;
          xmlhttp.open("GET", `/selected/${id}/${0}`)
          xmlhttp.send()
          
        }
      }
    }
  }
  

/**
 * Call addComment in DeviceController
 * @param {int} id - device id
 * @param {string} comment 
 */
function addComment(id, comment) {
  $.ajax({
    type:"POST",
    cache:false,
    url: `/addComment/${id}/${comment}`,
    success: function () {
      console.log("comment added");
      console.log(comment);
    }
  });
  window.location.reload();
}
  
let commentButtons = document.getElementsByClassName("comment_button");
let commentInputs = document.getElementsByClassName("comment_input");

for (let element of commentButtons) {
  //console.log(element);
  element.onclick = function () {
    let id = $(element).data("id");
    let comment = element.previousElementSibling.value;
    if (!comment.replace(/\s/g, '').length) {
      console.log('string only contains whitespace (ie. spaces, tabs or line breaks)');
      comment = null
    }
    if (comment == "") {
      comment = null
    }
    addComment(id, comment);
  }
}

/* ===== VERSION - new functionnality ===== */
  /**
   * call addDeviceVersion in DeviceController
   * @param {string} version 
   * @param {int} id - device version
   */
  function addVersion(version, id) {
    $.ajax({
      type:"POST",
      cache:false,
      url: `/addDeviceVersion/${version}/${id}`,
      success: function () {
        console.log("version added");
        console.log(version);
      }
    });
    //window.location.reload();
}

// Delete hidden label after check form
checkbox_0.parentElement.classList.remove("form-check")
document.getElementById('versionUpload').parentElement.classList.remove("mb-3")

let versionButton = document.getElementById("version_button");
let versionInput = document.getElementById("version_input");
let versionDevices = document.getElementsByClassName("device-check");

// TODO check if not multiple device type checked?
versionButton.onclick = function () {
  let version = versionButton.previousElementSibling.value;
  console.log(versionDevices.length);
  for (let device of versionDevices) {
    if (device.checked == true) {
      let device_id = device.getAttribute("data-id");
      addVersion(version, device_id);
      console.log(device.getAttribute("data-id"));
    }
  }
  console.log(version);
  window.location.reload();
  versionButton.previousElementSibling.value = "";
  //window.location.reload();
}