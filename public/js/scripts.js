let checkbox_0 = document.querySelector('#checkbox_0');
var checkboxes = document.getElementsByClassName('device-check');
let selectedButton = document.querySelectorAll(".device-check");
let selectedZone = document.getElementById('selectedZone');
let updateZone = document.querySelectorAll(".update-zone");
let validButtons = document.querySelectorAll(".valid-buttons");
let serverButtons = document.querySelectorAll(".server-buttons");
//let forcedButton = document.getElementsByName("switchbox");
let deviceArray = document.querySelectorAll(".info_device"); //get all the info modals


  /* when page refreshed, cancel selection */
  if (window.performance) {
    console.info("window.performance works fine on this browser");
}
  
/**
 * 
 * @param {*} id device id
 * @param {*} status selected (1) or not (0)
 */
function addSelected(id, status) {
  $.ajax({
    cache:false,
    url: `/selected/${id}/${status}`,
    success: function () {
      console.log(status)
    }
    })
}
  
  //console.info(performance.navigation.type);
  
  if (performance.navigation.type == performance.navigation.TYPE_RELOAD) {
    //console.info( "This page is reloaded" );
    for(let button of selectedButton){
      let substr_id = button.getAttribute("id");
      //let id = substr_id.substr(9);
      button.checked = false;
      //if (id != 0) {
        //addSelected(id, 0)
      //}
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
  /*
	for(let zone of updateZone) {
		zone.addEventListener("change", function() {
			id = zone.getAttribute('data-id');
			version = zone.value;
			url = `/updated/${id}/${version}`
			document.querySelector(`#update_${id}`).href = url;
		})
	}
  */

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
    url: `/forced/${forced}/${id}`,
    success: function () {
      console.log("device forced")
      console.log(forced)
    }
    })
}

function addDeviceModal(id, version, forced) {
  $.ajax({
    type: "POST",
    cache: false,
    url: `/addDeviceModal/${version}/${forced}/${id}`,
    success: function () {
      console.log("device forced")
      console.log(forced)
    }
    })
}


for (let validButton of validButtons) {
  validButton.addEventListener("click", function () {
    let id = validButton.getAttribute('name');
    let zone = document.querySelector(`#update_${id}`);
    let version = zone.value;
    let forcedButton = document.querySelector(`#forced_${id}`);
    if (version != "") {
      if (forcedButton.checked == true) {
        addDeviceModal(id, version, 1)
      }
      else {
        addDeviceModal(id, version, 0)
      }
    }
    else {
      if (forcedButton.checked == true) {
        addDeviceModal(id, "", 1)
      }
      else {
        addDeviceModal(id, "", 0)
      }
    }
    /*
    if (version != "") {
      addVersionModal(version, id);
    }
    
    if (forcedButton.checked == true) {
      console.log(forcedButton.checked);
      addForce(id, 1);
    }
    else {
      console.log(forcedButton.checked);
      addForce(id, 0);
    }
    */
    window.location.reload();
  })
 }


	// ######### Selected function ######### //

	/* if checkbox_0 is clicked and is checked, query all checkbox in html, change checkbox.checked's value according to value for checkbox_0, select item in db */
  if (checkbox_0) {
    checkbox_0.onclick = function() {
      if (checkbox_0.checked == true) {
        for (var i=0; i < checkboxes.length; i++) {
          let id = checkboxes[i].getAttribute("data-id");
          checkboxes[i].checked = true;
        }
      }
      else {
        for (var i=0; i < checkboxes.length; i++) {
          let id = checkboxes[i].getAttribute("data-id");
          checkboxes[i].checked = false;
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
      async: false,
    });
}

// Delete hidden label after check form
checkbox_0.parentElement.classList.remove("form-check")

let versionButton = document.getElementById("version_button");
let versionInput = document.getElementById("version_input");
let versionDevices = document.getElementsByClassName("device-check");

// TODO check if not multiple device type checked?
versionButton.onclick = function () {
  let version = versionButton.previousElementSibling.value;
  for (let device of selectedButton) {
    if (device.checked == true) {
      let device_id = device.getAttribute("data-id");
      addVersion(version, device_id);
      console.log(device.getAttribute("data-id"));
    }
  }
  versionButton.previousElementSibling.value = "";
  window.location.reload();

}

/*** ===== SERVER IP & PORT ===== ***/
for (let validButton of serverButtons) {
  validButton.addEventListener("click", function () {
    let id = validButton.getAttribute('name');
    let serverIdButton = document.querySelector(`#server_id_${id}`);
    let zone = document.querySelector(`#server_ip_${id}`);
    let serverIp = zone.value;
    let zone2 = document.querySelector(`#server_port_${id}`);
    let serverPort = zone2.value;
    if (serverIdButton.checked == true) {
      console.log(serverIdButton.checked);
      addServerId(id, 1);
    }
    else {
      addServerId(id, 0);
    }
    if (serverIp != "") {
      addServerIp(serverIp, id);
    }
    if (serverPort != "") {
      addServerPort(serverPort, id);
    }
    window.location.reload();
  })
}
 
// ######### CHANGE Server ID ######### //
/**
 * Change server ID status to 1 or 0
 * @param {int} id - device id
 * @param {boolean} status - Server ID status - 1: change | 0: no change
 */
function addServerId(id, status) {
  $.ajax({
    type: "POST",
    cache: false,
    url: `/addServerId/${id}/${status}`,
    success: function () {
      console.log("server Id changed")
      console.log(status)
    }
    })
}

// ######### CHANGE Server IP ######### //
  /**
   * call addServerIp in DeviceController
   * @param {string} ip
   * @param {int} id
   */
function addServerIp(ip, id) {
    $.ajax({
      type:"POST",
      cache:false,
      url: `/addServerIp/${ip}/${id}`,
    });
}

// ######### CHANGE Server PORT ######### //
  /**
   * call addServerPort in DeviceController
   * @param {string} port
   * @param {int} id
   */
  function addServerPort(port, id) {
    $.ajax({
      type:"POST",
      cache:false,
      url: `/addServerPort/${port}/${id}`,
    });
}