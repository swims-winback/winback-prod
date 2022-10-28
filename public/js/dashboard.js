//doughnut
let total_devices = document.getElementById('total_count').textContent;
let device_count_array = document.getElementsByClassName('device_count');
let count_array = [];
for (let device_count of device_count_array) {
  count_array.push(device_count.textContent);
}
let device_family_array = document.getElementsByClassName('device_family');
let family_array = [];
for (let device_family of device_family_array) {
  family_array.push(device_family.textContent);
}

var result = {};
for (var i = 0; i < family_array.length; i++) {
        result[family_array[i]] = count_array[i];
}

const ctx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [
              'Rschock',
              'Back 4',
            'Cryoback',
            'Back 2',
              'Bioback'
            ],
            datasets: [{
              label: 'Total Devices',
              data: count_array,
              backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
              ],
              borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1,
            hoverOffset: 4
            }]
        },
        options: {
          plugins: {
              title: {
                  display: true,
                  text: 'Total Devices'
              }
          }
      }
});

for (const key in result) {
  //const element = object[key];
  let substr_devices = total_devices - result[key];
  let versionZoneValue = document.getElementById(`count_version_${key}`);
  var type = 'ctx2';
  var type2 = 'myChart2';
  //this[type+"_"+key] = 1000;
  this[type+"_"+key] = document.getElementById(`myChart2_${key}`).getContext('2d');
  //this[type2+"_"+key] = new Chart(this[type+"_"+key], {
    myChart2 = new Chart(this[type+"_"+key], {
          type: 'doughnut',
          data: {
            labels: [
                'Updated',
                'Not Updated',
              ],
              datasets: [{
                label: `${key}`,
                data: [versionZoneValue.textContent, result[key]],
                backgroundColor: [
                  'rgba(54, 162, 235, 0.2)',
                  'white'
                ],
                borderColor: [
                  'rgba(54, 162, 235, 1)',
                  'grey'
              ],
              borderWidth: 1,
              hoverOffset: 4
              }]
          },
          options: {
            plugins: {
                title: {
                    display: true,
                    text: `${key}`
                }
            }
        }
  });

}

function addData(chart, label, data) {
  chart.data.labels.push(label);
  chart.data.datasets.forEach((dataset) => {
      dataset.data.push(data);
  });
  chart.update();
}

function removeData(chart) {
  chart.data.labels.pop();
  chart.data.datasets.forEach((dataset) => {
      dataset.data.pop();
  });
  chart.update();
}


//Listen for the select options
// when one option is selected, search all devices that corresponds to device family + version
// get the total number of devices
let select_options = document.getElementsByClassName('select-version');
for (let select of select_options) {
  $(document).ready(function() {
    $(select).click(() => {
      let version = select.value;
      //let deviceFamily = '1';
      let deviceFamily = $(select).data("devicefamily");
      let versionZone = document.getElementById(`count_version_${deviceFamily}`);
      $.ajax({    
        type: "GET", 
        //url: `/isactive/${deviceId}`,
        url: `/version/${deviceFamily}/${version}/`,
        dataType: "html",                  
        success: function (data) { 
          versionZone.innerHTML += data;
          console.log(data);
          console.log(deviceFamily);
          let substr_devices = total_devices - result[deviceFamily];
          let versionZoneValue = document.getElementById(`count_version_${deviceFamily}`);
          addData(myChart2, ['Updated', 'Not Updated'], [result[deviceFamily], data]);
        }
      });
    });
  });
}

