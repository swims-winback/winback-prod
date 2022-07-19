/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var CTD = false;
var readyRq = 0;
var launchStatus = false;
var timeMin = "10";
var timeSec = "00";
var page = 0;

function modifyVersion(spanSn, inputSn){
    $("span[name='"+spanSn+"'").css('visibility', 'hidden');
    $("span[name='"+spanSn+"'").hide();
    $("#saveUpdate_"+inputSn).css('visibility', 'visible');
    $("#saveUpdate_"+inputSn).show();
    $("input[name='"+inputSn+"'").focus();
}

function saveVersion(spanSn, inputSn){
    $upValue = $("input[name='"+inputSn+"'").val();
    $("#saveUpdate_"+inputSn).css('visibility', 'hidden');
    $("#saveUpdate_"+inputSn).hide();
    $("span[name='"+spanSn+"'").text($upValue);
    $("span[name='"+spanSn+"'").css('visibility', 'visible');
    $("span[name='"+spanSn+"'").show();
}

function trunkId(idElt){
    var res = idElt.split("_sn_");
    var spanSn = 'tv'+res[1];
    var inputSn = res[1];
    
    if(res[0] === 'm'){
        modifyVersion(spanSn, inputSn);        
    }else if (res[0] === 's') {
        saveVersion(spanSn, inputSn);
    }
    
    return inputSn;
}

function connect(datakey, sn){
	//$("#errorDeviceConnect").html("function doesn't work");
	//console.log("waiting for connection");
	//var path = "{{ path('tcpclient')}}";
    $.ajax({
        type:"POST",
        cache:false,
        //url:"../src/Class/TCPClient.php",
		//url: "{{ class_directory.'/TCPClient.php'}}",
		//url: "TCPClient.php",
		//url: "../src/Class/TCPClient.php",
		//url: "{{path('class_directory', '/TCPClient.php')}}",

		//url: `/admin/tcpclient/`,
		url: path,
        //data:{action: datakey, sn:sn},   // multiple data sent using ajax
		data:{action: datakey, sn:sn},
        success: function (data) {
			//console.log("success");
			//console.log(sn);
			if(data == 1){
				//console.log("data 1");
				/*
				$("#listDevice").css('visibility', 'hidden');
				$("#listDevice").css('display', 'none');
				*/
				$("#listDeviceSearch").css('visibility', 'hidden');
				$("#listDeviceSearch").css('display', 'none');
				$("#listDeviceTable").css('visibility', 'hidden');
				$("#listDeviceTable").css('display', 'none');

				$("#screenDevice").css('display', 'block');
				$("#screenDevice").css('visibility', 'visible');
				//$("#testAcces").css('display', 'block');
				//$("#testAcces").css('visibility', 'visible');

				$("#testAcces").attr("data-sn", sn);
				$("#q_disconnect_sn_1234").css('visibility', 'visible');
				$("#screenDevice").attr("data-sn", sn);
				$("#deviceSn").html(sn);
			}
			/*
			else
			{
				//alert("Device not available online !");
				$("#errorDeviceConnect").html(data);
				console.log("errorDeviceConnect");
			}
			*/
        },
		error: function() {
            // affiche un message d'erreur
            $("#errorDeviceConnect").html("Device not available online !");
			console.log("error ajx not working");
        }
    }).done(function(){
				CTD = true;
				readyRq = 0;
				}
			);
}

function disconnect(datakey){
    $.ajax({
        type:"POST",
        cache:false,
        //url:"../src/Class/TCPClient.php",
		//url: "/TCPClient.php",
		//url: "{{ path('tcpclient')}}",
		//url: "/../../src/Class/TCPClient.php",
		//url: "{{path('class_directory', '/TCPClient.php')}}",

		//url: `/admin/tcpclient/`,
		url: path,
        data:{action: datakey},   // multiple data sent using ajax
        success: function () {
				$("#screenDevice").css('display', 'none');
				$("#screenDevice").css('visibility', 'hidden');
				//$("#testAcces").css('display', 'none');
				//$("#testAcces").css('visibility', 'hidden');
				/*
				$("#listDevice").css('visibility', 'visible');
				$("#listDevice").css('display', 'block');
				*/
				$("#listDeviceSearch").css('visibility', 'visible');
				$("#listDeviceSearch").css('display', 'block');
				$("#listDeviceTable").css('visibility', 'visible');
				$("#listDeviceTable").css('display', 'block');

				$("#q_disconnect_sn_1234").css('visibility', 'hidden');
				$("#infoList").html("");
				CTD = false;
        }
    });
}

function command(action, sn, nameTouchTag, value){
    $.ajax({
        type:"POST",
        cache:false,
        //url:"../src/Class/TCPClient.php",
		//url: "/TCPClient.php",
		//url: "{{ path('tcpclient')}}",
		//url: "/../../src/Class/TCPClient.php",

		//url: `/admin/tcpclient/`,
		url: path,
        data:{action: action,sn: sn, cmd: nameTouchTag, tagTouch1:value, page:page},   // multiple data sent using ajax
        success: function (data) {
			var tempData = JSON.parse(data);
			tempData = tempData[0].split('#');
			tempData.pop();
			var reqId = tempData[0];
			tempData.shift();
            var jsonData = tempData;
            var html = '<tr><th>Label</th><th>Value</th></tr>';
			html += '<tr><td>RequestId</td><td>'+reqId+'</td></tr>';
            for(var key in jsonData){
				var spData = jsonData[key].split('|');

                html += '<tr>';
                html += '<td>'+spData[0]+'</td>';
                if(typeof(jsonData[key]) !== 'undefined'){
					if(key == 0){
						if(jsonData[key] == false){
							dataToAff = sn+' is not connected!!';
						}else{
							dataToAff = spData[1];
						}
						html += '<td>'+dataToAff+'</td>';
					}else{
						html += '<td>'+spData[1]+'</td>';
					}
                }else{
                    html += '<td> - </td>';                    
                }
                html += '</tr>';
            }
			console.log("infoList :".html);
            $("#infoList").html(html);
        }
    });
}

function startTest(action, sn, nameTouchTag, tagValue, trackerValue){
    $.ajax({
        type:"POST",
        cache:false,
        //url:"../src/Class/TCPClient.php",
		//url: "/TCPClient.php",
		//url: "{{ path('tcpclient')}}",
		//url: "/../../src/Class/TCPClient.php",
		//url: "{{path('class_directory', '/TCPClient.php')}}",

		//url: `/admin/tcpclient/`,
		url: path,
        data:{action: action,sn: sn, cmd: nameTouchTag, tagTouch:tagValue, trackerTouch:trackerValue, page:page},   // multiple data sent using ajax
        success: function (data) {
			var tempData = JSON.parse(data);
			tempData = tempData[0].split('#');
			tempData.pop();
			var reqId = tempData[0];
			tempData.shift();
            var jsonData = tempData;
            var html = '<tr><th>Label</th><th>Value</th></tr>';
			html += '<tr><td>RequestId</td><td>'+reqId+'</td></tr>';
            for(var key in jsonData){
				var spData = jsonData[key].split('|');

                html += '<tr>';
                html += '<td>'+spData[0]+'</td>';
                if(typeof(jsonData[key]) !== 'undefined'){
					if(key == 0){
						if(jsonData[key] == false){
							dataToAff = sn+' is not connected!!';
						}else{
							dataToAff = spData[1];
						}
						html += '<td>'+dataToAff+'</td>';
					}else{
						html += '<td>'+spData[1]+'</td>';
					}
                }else{
                    html += '<td> - </td>';                    
                }
                html += '</tr>';
            }
			console.log("infoList :".html);
            $("#infoList").html(html);
        }
    });
}

//connect_command

//document.addEventListener('click',function(e){
let connectButton = document.getElementsByClassName('connect_command')
for(let e of connectButton){
	//e.onclick = function() {
	e.addEventListener("click", function(){
		console.log(e);
		//if(e.target && e.target.id){
			/*
			idElt = e.target.id;
			console.log(e.target.id);
			*/
			idElt = e.id;
			console.log(e.id);
			console.log(typeof(sn));
			if(typeof(sn) === 'undefined'){
			//data-sn="1234"
			//if(typeof(sn) != string || !sn){
				sn = trunkId(idElt);
				console.log("undefined: ".sn);
			}else{
				sn = $("#screenDevice").attr("data-sn");
				//sn = $(".screenDevice").value;
				//console.log("screenDevice: ".sn);
				console.log(sn);
				//value = e.target.value;
				value = e.value;
				console.log(idElt[0]);
				if (idElt[0] === 'd'){
					//nameTouchTag = e.target.name;
					nameTouchTag = e.name;
					cmd="buttonTouch";
					command(cmd,sn,nameTouchTag, value);
				}else if(idElt[0] === 't'){
					//nameTouchTag = e.target.name;
					nameTouchTag = e.name;
					var min = parseInt($("#timerMin").text());
					cmd="buttonTouch";
					if ((value == '-') && (min > 5)){
						min = min - 5;
						$("#timerMin").html(min.toString());
						command(cmd,sn,nameTouchTag, min);
					}else if ((value == '+') && (min < 60)){
						min = min + 5;
						$("#timerMin").html(min.toString());
						command(cmd,sn,nameTouchTag, min);
					}
				}else if(idElt[0] === 'p'){
					//nameTouchTag = e.target.name;
					nameTouchTag = e.name;
					if(value == 'Start'){
						$("#p_timerPlay_sn_1234").val("Stop");
						$("#p_timerPlay_sn_1234").css("background-color", "blue");
						launchStatus = true;
					}else if(value == 'Stop'){
						$("#p_timerPlay_sn_1234").val("Start");
						$("#p_timerPlay_sn_1234").css("background-color", "grey");
						launchStatus = false;
					}else{
						$("#p_timerPlay_sn_1234").val("Start");
						$("#p_timerPlay_sn_1234").css("background-color", "grey");
						$("#timerMin").text(timeMin);
						$("#timerSec").text(timeSec);
						launchStatus = false;
					}
					cmd="buttonTouch";
					command(cmd,sn,nameTouchTag, value);
				}else if(idElt[0] === 'i'){
					//nameTouchTag = e.target.name;
					nameTouchTag = e.name;
					page = parseInt($("#pageInfoNumber").text());
					cmd="pageTest";
					if ((value == '-') && (page > 0)){
						page = page - 1;
						$("#pageInfoNumber").html(page.toString());
						command(cmd,sn,nameTouchTag, page);
					}else if ((value == '+') && (page < 255)){
						page = page + 1;
						$("#pageInfoNumber").html(page.toString());
						command(cmd,sn,nameTouchTag, page);
					}
				}else if(idElt[0] === 's'){
					//nameTouchTag = e.target.name;
					nameTouchTag = e.name;
					if(nameTouchTag == 'touchTest'){
						cmd = 'touchTest';
						tagValue = $("#s_tagTest_sn_1234").val();
						trackerValue = $("#s_trackerTest_sn_1234").val();
						startTest(cmd,sn,nameTouchTag, tagValue, trackerValue);
						connect('connect', sn);
					}
				}else if(idElt[0] === 'u'){
					//nameTouchTag = e.target.name;
					nameTouchTag = e.name;
					if(nameTouchTag == 'cmdTest'){
						cmd = 'cmdTest';
						value = $("#u_cmdStringTest_sn_1234").val();
						command(cmd,sn,nameTouchTag, value);
						connect('connect', sn);
					}
				}else if(idElt[0] === 'q'){
					//sn = trunkId(e.target.id);
					//sn = e.target.name;
					sn = e.name;
					console.log(sn);
					disconnect('disconnect', sn);
				}else{
					if(idElt[0] === 'c'){
						//sn = trunkId(e.target.id);
						sn = trunkId(e.id);
						console.log(sn);
						connect('connect', sn);
					}
				}
			}
		//}
 	});
}