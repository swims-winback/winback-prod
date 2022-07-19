/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    //var timeDelay = 5000;           // MILLISECONDS (5 SECONDS).
    
    setInterval(function (){
        if ((CTD == true) && (readyRq == 0)){
			readyRq = 1;
			sn = $("#screenDevice").attr("data-sn");

            $.ajax({
                type:"POST",
                cache:false,
                //url:"../src/Class/TCPClient.php",
                //url: `/admin/tcpclient/`,
                url: path,
                data:{action: 'test', sn:sn, page:page},   // multiple data sent using ajax
                success: function (data) {
                    //setTimeout(sendMsg, 5000);
                    var signalHzValue = ['Low', 'Normal', 'High'];
                    var signalPulseValue = ['Short', 'Normal', 'Long'];
					var tempData = JSON.parse(data);
					tempData = tempData[0].split('#');
					tempData.pop();
					var reqId = tempData[0];
					tempData.shift();
                    var jsonData = tempData;
					
                    var html = '<tr><th>Label</th><th>Value</th></tr>';
					html += '<tr><td>RequestId</td><td>'+reqId+'</td></tr>';
                    var status = false;
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
                            switch (key){
                                case 'Menu':
                                    if(jsonData[key] == 'Debug'){
                                        /*
                                        $("#listDevice").css('visibility', 'hidden');
                                        $("#listDevice").css('display', 'none');
                                        */
                                        $("#listDeviceSearch").css('visibility', 'hidden');
                                        $("#listDeviceSearch").css('display', 'none');
                                        
                                        $("#listDeviceTable").css('visibility', 'hidden');
                                        $("#listDeviceTable").css('display', 'none');

                                        $("#screenDevice").css('visibility', 'hidden');
                                        $("#screenDevice").css('display', 'none');
                                    }else{
                                        $("#screenDevice").css('visibility', 'visible');
                                        $("#screenDevice").css('display', 'block');                                    
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
                    console.log("infoList :".html);
                    $("#infoList").html(html);
                }
            }).done(function(){readyRq = 0;});
        }
    }, 1000);
});