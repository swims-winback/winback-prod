
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

/**
 * [send connect parameters to the TCPClient via ajax call,
 * success: ctd true, readyRq 0, socket() is called]
 * @param {string} datakey [description]
 * @param {string} sn [Device Serial Number]
 * @param {string} result [Device Id to identify elements in html]
 */
function connect(datakey, sn, result){
    $.ajax({
        type:"POST",
        cache:false,
		url: path,
		timeout: 4000, /*Miliseconds*/
		// multiple data sent using ajax
		data: { action: datakey, sn: sn },
		success : function (data) {
			CTD = true;
			readyRq = 0;
			console.log("connect success");
			//console.log(data);
			//console.log(path);
			if(data == 1){
				//$("#screenDevice").css('display', 'block');
				//$("#screenDevice").css('visibility', 'visible');

				//$("#testAcces").css('display', 'block');
				//$("#testAcces").css('visibility', 'visible');

				//$(`#testAcces_${sn}`).attr("data-sn", sn);
				//$("#q_disconnect_sn_1234").css('visibility', 'visible');
				$("#screenDevice").attr("data-sn", sn);
				console.log(sn);
				//$("#deviceSn").html(sn);
				socket(result, sn);
			}
			else
			{
				//alert("Device not available online !");
				//$("#errorDeviceConnect").html(data);
				console.log("errorDeviceConnect");
			}
		},
		error: function (xhr, status, error) {
			alert("Error Winback Connect!" + xhr.status);
		}
	});
}

/**
 * [send socket parameters to the TCPClient via ajax call,
 * success: show data in infoList in real-time]
 * @param {string} id [Desc]
 * @param {string} sn [Desc]
 */
function socket(id, sn) {
	setInterval(function (){
		if ((CTD == true) && (readyRq == 0)){
			readyRq = 1;
			$.ajax({
				type:"POST",
				cache:false,
				url: path,
				data:{action: 'test', sn:sn, page:page},   // multiple data sent using ajax
				success: function (data) {
					//sn = $("#screenDevice").attr("data-sn");
					//console.log("page :" + page);
					console.log("success socket js");
					var signalHzValue = ['Low', 'Normal', 'High'];
					var signalPulseValue = ['Short', 'Normal', 'Long'];
					console.log(data);
					var tempData = JSON.parse(data);
					tempData = tempData[0].split('#');
					tempData.pop();
					var reqId = tempData[0];
					tempData.shift();
					var jsonData = tempData;
					//Construct table with labels and their corresponding values
					var html = '<tr><th>Label</th><th>Value</th></tr>';
					html += '<tr><td>RequestId</td><td>'+reqId+'</td></tr>';
					var status = false;
					for(var key in jsonData){
						var spData = jsonData[key].split('|');
						//console.log(spData[0]);
						html += '<tr>';
						html += '<td>'+spData[0]+'</td>';
						if (typeof (jsonData[key]) !== 'undefined') {
							//console.log(jsonData[key]);
							//if(key == 0){
							if(spData[0] == 0){
								if(jsonData[key] == false){
									dataToAff = sn+' is not connected!!';
								}else{
									dataToAff = spData[1];
								}
								html += '<td>'+dataToAff+'</td>';
							}else{
								html += '<td>' + spData[1] + '</td>';
								
							}
							switch (key){
							//switch (spData[0]){
								case 'Menu':
									if (jsonData[key] == 'Debug') {
										console.log(id);
										/*
										$("#screenDevice").css('visibility', 'hidden');
										$("#screenDevice").css('display', 'none');
										*/
									} else {
										console.log(id);
										/*
										$("#screenDevice").css('visibility', 'visible');
										$("#screenDevice").css('display', 'block');  
										*/
									}
									break;
								case 'Signal Hz':
									if(jsonData[key] == 'Off'){
										$("#d_stimOff_sn_1234").prop("checked", true);
										for(var value in signalHzValue){
											$("#d_stimHz"+signalHzValue[value]+"_sn_1234").attr("disabled", "disabled");
										}
										status = false;
									}else{
										status = true;
										$("#d_stimOn_sn_1234").prop("checked", true);
										for(var value in signalHzValue){
											$("#d_stimHz"+signalHzValue[value]+"_sn_1234").removeAttr("disabled");
											if(signalHzValue[value] == jsonData[key]){
												$("#d_stimHz"+jsonData[key]+"_sn_1234").css("background-color", "blue");
											}else{
												$("#d_stimHz"+signalHzValue[value]+"_sn_1234").css("background-color", "grey");
											}
										}
									}
									break;
								case 'Signal Pulse':
										for(var value in signalPulseValue){
											if(status == false){
												$("#d_stimPulse"+signalPulseValue[value]+"_sn_1234").attr("disabled", "disabled");
											}else{
												$("#d_stimPulse"+signalPulseValue[value]+"_sn_1234").removeAttr("disabled");
												if(signalPulseValue[value] == jsonData[key]){
													$("#d_stimPulse"+jsonData[key]+"_sn_1234").css("background-color", "blue");
												}else{
													$("#d_stimPulse"+signalPulseValue[value]+"_sn_1234").css("background-color", "grey");
												}
											}
										}
									break;
								case 'Intensity':
									if(jsonData[key] == '0'){
										$("#d_tecaOff_sn_1234").prop("checked", true);
									}else{
										$("#d_tecaOn_sn_1234").prop("checked", true);                                        
										$("#d_tecaIntens"+jsonData[key]+"_sn_1234").prop("checked", true);                                        
									}
									break;
								case 'Time Min':
										timeMin = jsonData[key].toString();
										$("#timerMin").text(timeMin);
									break;
								case 'Time Sec':
										timeSec = jsonData[key].toString();
										$("#timerSec").text(timeSec);
									break;
								case 'State':
									switch (jsonData[key]){
										case 'Stop':
											$("#p_timerPlay_sn_1234").val("Start");
											$("#p_timerPlay_sn_1234").css("background-color", "grey");
											$("#timerMin").html(timeMin);
											$("#timerSec").html(timeSec);
											$("#p_timerPause_sn_1234").css("background-color", "grey");
											$("#p_timerPause_sn_1234").attr("disabled", "disabled");
											launchStatus = false;
											break
										case 'Ready':
											$("#p_timerPlay_sn_1234").val("Stop");
											$("#p_timerPlay_sn_1234").css("background-color", "blue");
											$("#p_timerPause_sn_1234").removeAttr("disabled")
											$("#p_timerPause_sn_1234").css("background-color", "grey");
											launchStatus = true;
											break;
										case 'Pause':
											$("#p_timerPause_sn_1234").removeAttr("disabled")
											$("#p_timerPlay_sn_1234").val("Start");
											$("#p_timerPause_sn_1234").css("background-color", "blue");
											var colorPause = $("#p_timerPlay_sn_1234").css("background-color");
											if(colorPause == "rgb(128, 128, 128)"){
												$("#p_timerPlay_sn_1234").css("background-color", "blue");
											}else{
												$("#p_timerPlay_sn_1234").css("background-color", "grey");
											}
											launchStatus = true;
											break;
									}
									break;
								default:
									break;
							}
						}else{
							html += '<td> - </td>';                    
						}
						html += '</tr>';
					}
					//console.log("infoList : " + html);
					console.log(id);
					$(`#infoList_${id}`).html(html);
				},
				error: function(xhr, status, error){
					alert("Error!" + xhr.status);
				},
			}).done(function(){readyRq = 0;});
		}
		/*
		setTimeout(() => {
			console.log("server is paused");
			//document.location.reload();
		}, 3000);
		*/
	}, 1000);
}

/**
 * [send disconnect parameters to the TCPClient via ajax call,
 * success: disconnect, ctd false & reload window]
 * @param {string} datakey 
 */
function disconnect(datakey){
    $.ajax({
        type:"POST",
        cache:false,
		url: path,
		data: { action: datakey },   // multiple data sent using ajax
		timeout: 4000, /*Miliseconds*/
		success: function () {
			console.log("disconnect")
			//$("#screenDevice").css('display', 'none');
			//$("#screenDevice").css('visibility', 'hidden');

			//$("#q_disconnect_sn_1234").css('visibility', 'hidden');
			//$("#infoList").html("Disconnect winback js");
			CTD = false;
			window.location.reload();
		},
		error: function () {
			console.log("error disconnect");
		}
    });
}

/**
 * [send command parameters to the TCPClient via ajax call,
 * success: ]
 * @param {string} action [cmd: connect, disconnect, buttonTouch, periodictyConnect, test, touchTest, commandTest, pageTest]
 * @param {string} sn [Serial Number]
 * @param {string} nameTouchTag [e.name, cmdTest]
 * @param {integer} value [description]
 */
function command(action, sn, nameTouchTag, value){
    $.ajax({
        type:"POST",
        cache:false,
		url: path,
        data:{action: action, sn: sn, cmd: nameTouchTag, tagTouch1:value, page:page},   // multiple data sent using ajax
		success: function (data, sn) {
			//console.log(data);
			//console.log("page: " + page);
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
            $(`#infoList_${sn}`).html(html);
        }
    });
}

function startTest(action, sn, nameTouchTag, tagValue, trackerValue){
    $.ajax({
        type:"POST",
        cache:false,
		url: path,
        data:{action: action, sn: sn, cmd: nameTouchTag, tagTouch:tagValue, trackerTouch:trackerValue, page:page},   // multiple data sent using ajax
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
			//console.log("infoList :"+html);
            $("#infoList").html(html);
        }
    });
}

let connectCommand = document.querySelectorAll(".connect_command")
for(let e of connectCommand){
	e.onclick = function() {
		console.log(e);
		idElt = e.id;
		console.log(e.id);
		console.log(typeof(sn));
		if(typeof(sn) === 'undefined'){
			//data-sn="1234"
			//if(typeof(sn) != string || !sn){
			sn = trunkId(idElt);
			console.log(sn);
		}else{
			sn = $("#screenDevice").attr("data-sn");
			//sn = $(".screenDevice").value;
			console.log(sn);
		}
		value = e.value;
		if (idElt[0] === 'd') {
			console.log('d');
			nameTouchTag = e.name;
			cmd="buttonTouch";
			command(cmd,sn,nameTouchTag, value);
		}
		/* TODO Not used, used in deviceAction but not allowed*/
		/*
		else if (idElt[0] === 't') {
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
		}
		else if (idElt[0] === 'p') {
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
		}
		*/
			/* TODO Not used? id not found */
		/*
		else if (idElt[0] === 's') {
			//nameTouchTag = e.target.name;
			nameTouchTag = e.name;
			if(nameTouchTag == 'touchTest'){
				cmd = 'touchTest';
				tagValue = $("#s_tagTest_sn_1234").val();
				trackerValue = $("#s_trackerTest_sn_1234").val();
				startTest(cmd,sn,nameTouchTag, tagValue, trackerValue);
				connect('connect', sn);
			}
		}
		*/
		
	}
}

// Connect Buttons
let connectButtons = document.getElementsByClassName("connect_button");
for(let element of connectButtons){
	element.onclick = function () {
		let sn = trunkId(element.id);
		let id = $(element).data("id");
		let result = id.substr(8);
		console.log(result);
		console.log(sn);
		connect('connect', sn, result);
	}
};

// Disconnect Buttons
let disconnectButtons = document.getElementsByClassName("disconnect_button");
for(let element of disconnectButtons){
	element.onclick = function () {
		let sn = element.name;
		disconnect('disconnect', sn);
		
	}
};

// Submit Command Buttons
let submitCommandButtons = document.getElementsByClassName("submit_command_button");
for(let element of submitCommandButtons){
	element.onclick = function () {
		let cmd = 'cmdTest';
		let value = element.previousElementSibling.value;
		element.previousElementSibling.value = "";
		let nameTouchTag = element.name;
		let sn = $(element.previousElementSibling).data("sn");
		console.log(sn);
		console.log(value);
		command(cmd, sn, nameTouchTag, value);
		//disconnect('disconnect', sn);
		connect('connect', sn);
		
	}
};

// Page Buttons
//TODO disable pagePrev if page 0
let pageButtons = document.getElementsByClassName("page_button");
for(let element of pageButtons){
	element.onclick = function () {
		nameTouchTag = element.name;
		cmd = "pageTest";
		let sn = $(element).data("sn");
		let id = $(element).data("id");
		page = parseInt($(`#pageInfoNumber_${id}`).text());
		if ((element.value == '-') && (page > 0)){
			page = page - 1;
			$(`#pageInfoNumber_${id}`).html(page.toString());
			//command(cmd, sn, nameTouchTag, page);
		}else if ((element.value == '+') && (page < 255)){
			page = page + 1;
			$(`#pageInfoNumber_${id}`).html(page.toString());
			//command(cmd, sn, nameTouchTag, page);
		}
	}
};
