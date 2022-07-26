
 // ######### Connect function ######### //
 /*
 $(document).ready(function (){
  let deviceArray = document.querySelectorAll(".info_device")
  for (let device of deviceArray) {
    //var deviceId = $('#info_device').data("id");
    var deviceId = $(device).data("id");
    //var infoButton = 
    $( device ).load( `/admin/device/`);
  }
});
*/

 $(document).ready(function (){
    
  setInterval(function (){
    let deviceArray = document.querySelectorAll(".info_device")
    for (let device of deviceArray) {
      //var deviceId = $('#info_device').data("id");
      var deviceId = $(device).data("id");
      
      //var infoButton = 
      $.ajax({    
        type: "GET",
        //url: `/admin/device/isactive/${deviceId}`, 
        url: `/admin/device/isactive/${deviceId}`,          
        dataType: "html",                  
        success: function(data){ 
          if ($.trim(data)==1){   
            //$("#test").html(data); 
            //$(device).css("background-color", "green");
            $(device).addClass('bg-green btn-outline-green')
            var deviceSn = $(device).data("title");
            console.log(deviceSn);
            //$(`#c_sn_${deviceSn}`).attr('hidden', false);
            document.getElementById(`c_sn_${deviceSn}`).style.display = "block";
            
            //console.log(url);
          } 
          else  
          {    
            //$(device).css("background-color", "green");
            $(device).addClass('bg-orange btn-outline-orange')
            var deviceSn = $(device).data("title");
            //$(`#c_sn_${deviceSn}`).attr('hidden', true);
            //console.log(`#c_sn_${deviceSn}`);
            document.getElementById(`c_sn_${deviceSn}`).style.display = "none";
            //document.getElementById(`c_sn_${deviceSn}`).style.display = "none";
            //$("#c_sn_".deviceSn).html('ok');
            
          }                 
        }
      });
    }
  }, 1000);
});


window.onload = () => {



  // ######## Validate version ######## //
  let updateButton = document.querySelectorAll(".update")
  let updateZone = document.querySelectorAll(".update-zone")
  for(let zone of updateZone) {
    zone.addEventListener("change", function() {
      id = zone.getAttribute('data-id');
      //id_substr = id.substr(7);
      version = zone.value;
      
      //url = `{{path('updated', {'id': ${id}, 'version': ${version}})}}`
      url = `/admin/device/updated/${id}/${version}`
      document.querySelector(`#update_${id}`).href = url;
    })
  }


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

  // ######### Switch function ######### //
  let forcedButton = document.getElementsByName("switchbox")
  let validButton = document.getElementsByName("validate")
  for(let button of forcedButton){
    for (let vButton of validButton) {
        vButton.addEventListener("click", function(){
        $id = button.getAttribute("data-id");
        let xmlhttp = new XMLHttpRequest;
        //console.log($id);
        xmlhttp.open("GET", `/admin/device/forced/${$id}`)
        xmlhttp.send()
    })
    }

  }

// ============ Sort Table by column =========== //

/*
function sortTable(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("deviceTable");
    switching = true;
    // Set the sorting direction to ascending:
    dir = "asc";
    while (switching) {
      // Start by saying: no switching is done:
      switching = false;
      rows = table.rows;
      for (i = 1; i < (rows.length - 1); i++) {
        // Start by saying there should be no switching:
        shouldSwitch = false;
        x = rows[i].getElementsByTagName("TD")[n];
        y = rows[i + 1].getElementsByTagName("TD")[n];
        if (dir == "asc") {
          if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
            // If so, mark as a switch and break the loop:
            shouldSwitch = true;

            break;
          }
        } else if (dir == "desc") {
          if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
            // If so, mark as a switch and break the loop:
            shouldSwitch = true;

            break;
          }
        }
      }
      if (shouldSwitch) {
        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
        switching = true;
        // Each time a switch is done, increase this count by 1:
        switchcount ++;
      } else {
        if (switchcount == 0 && dir == "asc") {
          dir = "desc";
          switching = true;
        }
      }
    }
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

  
  document.getElementById('checkbox_0').onclick = function() {
    var checkboxes = document.getElementsByClassName('form-check-input');
    //for (var checkbox of checkboxes) {
    for (var i=1; i < checkboxes.length; i++) {

      //let id = checkbox.getAttribute("data-id");
      //checkbox.checked = this.checked;
      let id = checkboxes[i].getAttribute("data-id");
      checkboxes[i].checked = this.checked;
      let xmlhttp = new XMLHttpRequest;
      console.log(checkboxes[i]);
      console.log(id);
      xmlhttp.open("GET", `/admin/device/selected/${id}`)
      xmlhttp.send()
    }
  }
  
  
  // ######### Selected function ######### //
  
  let selectedButton = document.querySelectorAll(".form-check-input");
  let saveButton = document.querySelector("#form_Save");
  for(let button of selectedButton){
  //for (var i=1; i < selectedButton.length; i++) {
      //saveButton.addEventListener("click", function(){
        button.addEventListener("click", function(){
          //let id = button.getAttribute("data-id");
          let substr_id = button.getAttribute("id");
          let id = substr_id.substr(9);
          //button.checked == true;
          let xmlhttp = new XMLHttpRequest;
          //if (id != 0 && button.checked) {
          if (id != 0) {
            console.log(id);
            console.log(button.checked);
            xmlhttp.open("GET", `/admin/device/selected/${id}`)
            xmlhttp.send()
          }

      })
  }
  
  // =========== check to modify version upload field ============ //

  let checkbox_array = document.getElementsByName('checkbox_item');

  let checkbox_0 = document.querySelector('#checkbox_0');
  let validButton_0 = document.querySelector('#valid_0');
 

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

  //checkbox_0.addEventListener('click', check(this));
  //onClick="toggle(this, 'checkbox')
}