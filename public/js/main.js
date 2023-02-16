function getArray(className) {
  let devices_array = document.getElementsByClassName(className);
  let result_array = [];
  for (let device_count of devices_array) {
    result_array.push(device_count.textContent);
  }
  return result_array;
} 

/* Connected Devices */
count_array = getArray('device_count');
created_array = getArray('device_created');
days = getArray('days');

console.log(count_array)
console.log(days)
console.log(created_array)

var result = {};
for (var i = 0; i < days.length; i++) {
        result[days[i]] = count_array[i];
}

function getChartData(chartLabels, chartLabel, chartData, chartBorderColor, chartBackgroundColor, chartYAxisID) {
  const data = {
    labels: chartLabels,
    datasets: [
      {
        label: chartLabel,
        data: chartData,
        borderColor: chartBorderColor,
        backgroundColor: chartBackgroundColor,
        yAxisID: chartYAxisID,
      },
    ]
  };
  return data;
}

function getChartConfig(configType, configData) {
  const config = {
    type: configType,
    data: configData,
  };
  return config
}

function getChart(config, id) {
  const ctx = document.getElementById(id).getContext('2d');
  const myChart = new Chart(ctx, config);
  return myChart
}

/* Devices Connected */
dataConnected = getChartData(
  days,
  'Devices connected by day',
    count_array,
    'rgba(255, 99, 132, 1)',
    'rgba(255, 99, 132, 0.2)',
    'y'
)
configConnected = getChartConfig('line', dataConnected)
chartConnected = getChart(
  configConnected,
  'chartDeviceConnected'
)
/* Devices Created */
dataCreated = getChartData(
  days,
  'Devices created by day',
    created_array,
    'rgb(75, 192, 192)',
    'rgba(255, 99, 132, 0.2)',
    'y'
)
configCreated = getChartConfig('line', dataCreated)
chartCreated = getChart(
  configCreated,
  'chartDeviceCreated'
)