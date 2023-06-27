  //let category = ['sn', 'pathoType', 'zone', 'patho', 'tool'];
  var category = new Map();
  
    filterStats = document.getElementsByClassName("filterStat");
    for (let elem of filterStats) {
        elem.onchange = function () {
            //console.log(elem);
          let val = elem.value;
          
          let category_name = $(elem).data("category");
          console.log(category_name);
          //category.push($(elem).data("category"));
          //category[category_name] = val;
          category.set(category_name, val);
            console.log(category);
            console.log(val);
            $.ajax({    
                type: "GET",
                //url: `/filter/${category}/${val}/`,
                url: `/filter/${category}/`,
                dataType: "html",                  
                success: function (data) { 
                    console.log(data);
                    /*
                    versionZone.innerHTML = data;
                    notUpdatedZone.innerHTML = result[deviceFamily]-data;
                    myChartArray[deviceFamily].data.datasets[0].data[0] = result[deviceFamily]-data;
                    myChartArray[deviceFamily].data.datasets[0].data[1] = data;
                    myChartArray[deviceFamily].update();
                    */
                }
              });

        };
    }

    /*
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
    */