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
          labels:
          family_array,
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
    responsive: true,
    plugins: {
            /*
              title: {
                  display: true,
                  text: 'Total Devices'
              }
            */
          }
  },
  plugins: [{
    id: 'text',
    beforeDraw: function(chart, a, b) {
      var width = chart.width,
        height = chart.height,
        ctx = chart.ctx;
      ctx.restore();
      var fontSize = (height / 114).toFixed(2);
      ctx.font = fontSize + "em sans-serif";
      ctx.textBaseline = "middle";

      var text = total_devices,
        textX = Math.round((width - ctx.measureText(text).width) / 2),
        textY = (height / 3)+(height / 3);
      ctx.fillText(text, textX, textY);
      ctx.save();
    }
  }]
  
});


myChartArray = []
for (const key in result) {
  let substr_devices = total_devices - result[key];
  let versionZoneValue = document.getElementById(`count_version_${key}`);
  var type = 'ctx2';
  var type2 = 'myChart2';
  this[type+"_"+key] = document.getElementById(`myChart2_${key}`).getContext('2d');
    myChartArray[key] = new Chart(this[type+"_"+key], {
          type: 'doughnut',
          data: {
            labels: ['Not Updated', 'Updated'],
              datasets: [{
                label: `${key}`,
                data: [result[key], versionZoneValue.textContent],
                backgroundColor: [
                  'white',
                  'rgba(54, 162, 235, 0.2)'
                  
                ],
                borderColor: [
                  'grey',
                  'rgba(54, 162, 235, 1)',
              ],
              borderWidth: 1,
              hoverOffset: 4
              }]
            },
          
      options: {
        responsive: true,
        plugins: {
              /*
                title: {
                    display: true,
                    text: `${key}`
                }
              */
            }
          },
          
          plugins: [{
            id: 'text',
            beforeDraw: function(chart, a, b) {
              var width = chart.width,
                height = chart.height,
                ctx = chart.ctx;
              
              ctx.restore();
              var fontSize = (height / 200).toFixed(2);
              ctx.font = fontSize + "em sans-serif";
              ctx.textBaseline = "middle";
        
              var text = versionZoneValue.textContent,
                textX = Math.round((width - ctx.measureText(text).width) / 2),
                textY = (height / 3)+(height / 3);
              
              ctx.fillText(text, textX, textY);
              ctx.save();
            }
          }]
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

$(document).ready(function() {
  for (let fam in family_array) {
    console.log(family_array[fam]);
    $(`#dashSelect_${family_array[fam]}`).on('change', () => {
      let version = $( `#dashSelect_${family_array[fam]} option:selected` ).val();
        let deviceFamily = $( `#dashSelect_${family_array[fam]} option:selected` ).data("devicefamily");
      let versionZone = document.getElementById(`count_version_${deviceFamily}`);
      let notUpdatedZone = document.getElementById(`count_not_${deviceFamily}`);
        $.ajax({    
          type: "GET",
          url: `/version/${deviceFamily}/${version}/`,
          dataType: "html",                  
          success: function (data) { 
            versionZone.innerHTML = data;
            notUpdatedZone.innerHTML = result[deviceFamily]-data;
            myChartArray[deviceFamily].data.datasets[0].data[0] = result[deviceFamily]-data;
            myChartArray[deviceFamily].data.datasets[0].data[1] = data;
            myChartArray[deviceFamily].update();
          }
        });
      });

  }
});

