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
      window.location.reload();
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
        //alert("Version changed with device forced")
      }
      else {
        addDeviceModal(id, version, 0)
        //alert("Version changed, device not forced")
      }
    }
    else {
      if (forcedButton.checked == true) {
        addDeviceModal(id, "", 1)
        //alert("device forced")
      }
      else {
        addDeviceModal(id, "", 0)
        //alert("device deforced")
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
    //window.location.reload();
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
  
function getDevice(id, comment, callback) {
  $.ajax({
    type:"GET",
    cache:false,
    url: `/getDeviceId/${id}`,
    data: {comment},
    success: function (data) {
      if (callback) {
        callback(data)
      }
    }
  });
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
    getDevice(id, comment, (data) => {
      alert("Info: comment '"+comment + "' added to device "+data)
    })
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

// ######## Download Errors ######## //

function tableToCSV() {
 
  // Variable to store the final csv data
  let csv_data = [];

  // Get each row data
  //let rows = document.getElementsByTagName('tr');
  let rows = document.getElementsByClassName('tr_error');
  for (let i = 0; i < rows.length; i++) {

      // Get each column data
      let cols = rows[i].querySelectorAll('td,th');

      // Stores each csv row data
      let csvrow = [];
      for (let j = 0; j < cols.length; j++) {

          // Get the text data of each cell
          // of a row and push it to csvrow
          csvrow.push(cols[j].innerHTML);
      }

      // Combine each column value with comma
      csv_data.push(csvrow.join(","));
  }

  // Combine each row data with new line character
  csv_data = csv_data.join('\n');

  // Call this function to download csv file  
  downloadCSVFile(csv_data);

}

function downloadCSVFile(csv_data) {

  // Create CSV file object and feed
  // our csv_data into it
  CSVFile = new Blob([csv_data], {
      type: "text/csv"
  });

  // Create to temporary link to initiate
  // download process
  let temp_link = document.createElement('a');

  // Download csv file
  temp_link.download = "error.csv";
  let url = window.URL.createObjectURL(CSVFile);
  temp_link.href = url;

  // This link should not be displayed
  temp_link.style.display = "none";
  document.body.appendChild(temp_link);

  // Automatically click the link to
  // trigger download
  temp_link.click();
  document.body.removeChild(temp_link);
}


/*
function getChart(data_Iin, data_Iout, data_Vout, labels) {
  return ({
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: "Iin",
        data: data_Iin,
        backgroundColor: [
          'rgba(105, 0, 132, .2)',
        ],
        borderColor: [
          '#3A1FFF',
        ],
        borderWidth: 2,
      },
        {
        label: "Iout",
        data: data_Iout,
        backgroundColor: [
          'rgba(105, 0, 132, .2)',
        ],
        borderColor: [
          '#00FFF1',
        ],
        borderWidth: 2,
      },
      {
        label: "Vout",
        data: data_Vout,
        backgroundColor: [
          'rgba(0, 137, 132, .2)',
        ],
        borderColor: [
          '#FE8D22',
        ],
        borderWidth: 2,
        tension: 0.2
      }
      ]
    },
    options: {
      responsive: true,
      color: '#FFFFFF',
    }
  })
}

function getLabels(array_length) {
  var labels = []
  for (let index = 1; index <= array_length; index ++) {
    labels.push(index);
  }
  return labels
}

ctxl_array = document.getElementsByClassName("lineChart")
ctxl_elem_array = []

data_array = [
  [[147, 182, 13, 13, 13, 13], [164, 187, 46, 43, 43, 43], [149, 170, 88, 87, 86, 85]],
  [[6, 6, 236, 235, 229, 229], [20, 21, 232, 234, 237, 238], [42, 42, 65, 63, 59, 58]],
  [[12, 12, 12, 12], [34, 35, 35, 36], [80, 81, 81, 82]],
  [[163, 171, 167, 151], [293, 282, 290, 297], [41, 39, 41, 39]],
  [[12, 12, 12], [34, 35, 35], [80, 81, 81]],
  [[150, 137, 165], [270, 249, 292], [38, 38, 39]],
  [[125, 118], [164, 159], [153, 148]],
  [[6, 6], [17, 16], [39, 39]],
  [[152, 160, 190], [177, 188, 213], [196, 207, 213]],
  [[6, 6, 6], [19, 19, 18], [40, 40, 40]],
  [[174, 191, 167], [185, 205, 182], [220, 218, 206]],
  [[6, 6, 6], [21, 19, 20], [43, 41, 41]],
  [[112, 133], [133, 154], [180, 185]],
  [[6, 6], [20, 17], [42, 39]],
  [[49, 50, 53, 247, 254, 245, 333], [117, 117, 115, 282, 300, 288, 297], [59, 58, 57, 53, 54, 52, 76]],
  [[47, 49, 45, 6, 6, 6, 6], [121, 119, 119, 5, 5, 5, 5], [59, 59, 58, 8, 8, 8, 8]],
  [[79, 82, 69, 97], [120, 122, 115, 137], [140, 141, 136, 154]],
  [[75, 77, 77, 94], [118, 123, 120, 137], [156, 162, 145, 169]],
  [[89, 98, 56, 123], [124, 142, 98, 159], [147, 157, 137, 178]],
  [[87, 105, 52, 112], [123, 143, 89, 158], [158, 182, 135, 192]],
  [[48, 238, 262, 227, 153, 290, 314, 242, 316, 352], [114, 311, 334, 318, 224, 369, 382, 334, 418, 377], [59, 51, 54, 48, 33, 48, 49, 45, 48, 52]],
  [[38, 5, 5, 5, 6, 6, 6, 6, 6, 6], [123, 5, 5, 5, 6, 6, 6, 5, 5, 6], [60, 9, 9, 9, 9, 9, 8, 8, 8, 8]],
  [[5, 6, 6, 6, 6, 5, 6, 6, 6], [22, 5, 5, 5, 4, 21, 5, 5, 4], [42, 9, 9, 9, 9, 41, 9, 8, 8]],
  [[101, 126, 143, 136, 85, 103, 106, 105, 109], [135, 246, 273, 270, 184, 140, 219, 218, 221], [155, 32, 34, 33, 26, 154, 28, 26, 27]],
  [[165, 103, 104, 76, 84], [234, 152, 154, 153, 154], [46, 82, 76, 74, 77]],
  [[6, 60, 59, 82, 79], [5, 162, 152, 154, 158], [8, 72, 72, 77, 79]],
  [[6, 6, 102, 62, 17], [5, 5, 138, 84, 32], [9, 8, 28, 17, 11]],
  [[100, 6, 144, 6, 45], [158, 10, 162, 16, 71], [34, 4, 41, 8, 17]],
  [[5, 5, 6, 6, 6, 6, 6], [20, 21, 5, 5, 5, 5, 5], [40, 41, 8, 8, 8, 8, 8]],
  [[156, 184, 168, 215, 210, 169, 161], [187, 204, 294, 339, 344, 294, 293], [160, 186, 37, 41, 41, 36, 35]],
  [[158, 129, 127, 181, 121, 148, 47], [205, 182, 180, 265, 208, 225, 95], [142, 121, 120, 40, 31, 33, 47]],
  [[145, 102, 103, 171, 116, 147, 23], [193, 158, 159, 287, 224, 251, 84], [157, 120, 123, 38, 30, 31, 47]],
  [[330, 312, 348, 228], [309, 312, 324, 216], [81, 75, 83, 72]],
  [[0, 0, 0, 0], [0, 0, 0, 0], [0, 0, 0, 0]]

]
for (let elem of ctxl_array) {
  elem_id = elem.id
  ctxl_elem = elem.getContext('2d');
  ctxl_elem_array.push(ctxl_elem)
}

for (let index = 0; index < ctxl_elem_array.length; index++) {
  elem = ctxl_elem_array[index];
  data_Iin = data_array[index][0]
  data_Iout = data_array[index][1]
  data_Vout = data_array[index][2]
  labels = getLabels(data_Vout.length)
  console.log(labels)
  param = getChart(data_Iin, data_Iout, data_Vout, labels)
  lineChart_elem = new Chart(elem, param)
  
}

var ctxL2 = document.getElementById("lineChart2").getContext('2d');
var myLineChart2 = new Chart(ctxL2, {
  type: 'line',
  data: {
    labels: ["1", "3", "5", "7"],
    datasets: [{
      label: "Iout",
      data: [59, 80, 81, 56],
      backgroundColor: [
        'rgba(105, 0, 132, .2)',
      ],
      borderColor: [
        '#3A1FFF',
      ],
      borderWidth: 2,
    },
    {
      label: "Vout",
      data: [28, 48, 40, 19],
      backgroundColor: [
        'rgba(0, 137, 132, .2)',
      ],
      borderColor: [
        '#FE8D22',
      ],
      borderWidth: 2,
      tension: 0.2
    }
    ]
  },
  options: {
    responsive: true,
    color: '#FFFFFF',
  }
});

document.getElementById("reset-form-device").onclick = function () {
  document.getElementById("filter").reset();
  console.log("hello")
}
*/