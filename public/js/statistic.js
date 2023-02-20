backgroundArray = [
  'rgba(255, 99, 132, 0.2)',
  'rgba(255, 159, 64, 0.2)',
  'rgba(255, 205, 86, 0.2)',
  'rgba(75, 192, 192, 0.2)',
  'rgba(54, 162, 235, 0.2)',
  'rgba(153, 102, 255, 0.2)',
  'rgba(201, 203, 207, 0.2)'
]

borderArray = [
  'rgb(255, 99, 132)',
  'rgb(255, 159, 64)',
  'rgb(255, 205, 86)',
  'rgb(75, 192, 192)',
  'rgb(54, 162, 235)',
  'rgb(153, 102, 255)',
  'rgb(201, 203, 207)'
]

function getArray(className) {
  let devices_array = document.getElementsByClassName(className);
  let result_array = [];
  for (let device_count of devices_array) {
    result_array.push(device_count.textContent);
  }
  return result_array;
} 

let sn_count_array = getArray('sn_count')
let sn_list_array = getArray('sn_list')
console.log(sn_count_array)
/**
 * Nombre de traitements par Types de Patho
 */
let pathoType_count_array = getArray('pathoType_count')
let pathoType_list_array = getArray('pathoType_list')
/**
 * Nombre de traitements par Zones
 */
let zone_count_array = getArray('zone_count')
let zone_list_array = getArray('zone_list')
/**
 * Nombre de traitements par Patho
 */
let patho_count_array = getArray('patho_count')
let patho_list_array = getArray('patho_list')
 /**
 * Nombre de traitements par Accessoires
 */
let tool_count_array = getArray('tool_count')
let tool_list_array = getArray('tool_list')


function getChartData(chartLabels, chartData, backgroundArray, borderArray) {
  const data = {
    labels: chartLabels, //sn_list
    datasets: [{
      label: false,
      data: chartData, //sn_count
      backgroundColor: backgroundArray,
      borderColor: borderArray,
      borderWidth: 1
    }]
  }
  return data;
}

function getChartOption() {
  options = {
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
  return options
}

function getChartConfig(configType, configData, configOption) {
  const config = {
    type: configType,
    data: configData,
    option: configOption,
  };
  return config
}

function getChart(config, id) {
  const ctx = document.getElementById(id);
  const myChart = new Chart(ctx, config);
  return myChart;
}
/* Chart Sn */
dataSn = getChartData(
  sn_list_array,
  sn_count_array,
  backgroundArray,
  borderArray
)
option = getChartOption()
configSn = getChartConfig(
  'bar',
  dataSn,
  option
)
chartSn = getChart(
  configSn,
  'chartSn'
)
/* Chart Patho Type */
dataPathoType = getChartData(
  pathoType_list_array,
  pathoType_count_array,
  backgroundArray,
  borderArray
)
option = getChartOption()
configPathoType = getChartConfig(
  'bar',
  dataPathoType,
  option
)
chartPathoType = getChart(
  configPathoType,
  'chartPathoType'
)
/* Chart Zone */
dataZone = getChartData(
  zone_list_array,
  zone_count_array,
  backgroundArray,
  borderArray
)
option = getChartOption()
configZone = getChartConfig(
  'bar',
  dataZone,
  option
)
chartZone = getChart(
  configZone,
  'chartZone'
)
/* Chart Patho */
dataPatho = getChartData(
  patho_list_array,
  patho_count_array,
  backgroundArray,
  borderArray
)
option = getChartOption()
configPatho = getChartConfig(
  'bar',
  dataPatho,
  option
)
chartPatho = getChart(
  configPatho,
  'chartPatho'
)
/* Chart Tool */
dataTool = getChartData(
  tool_list_array,
  tool_count_array,
  backgroundArray,
  borderArray
)
option = getChartOption()
configTool = getChartConfig(
  'bar',
  dataTool,
  option
)
chartTool = getChart(
  configTool,
  'chartTool'
)

//Listen for the filter options
// when one option is selected, search all treatments that correspond to filters

$(document).ready(function () {
  filterSn = document.getElementById("filterSn");
  filterSn.onchange = function() {
    let val = filterSn.value;
    console.log(val)
  }
});
  