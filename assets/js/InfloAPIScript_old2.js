$(document)
        .ready(function () {
			
            GetEncryptedAPIKey("974CB208-48DD-41D4-99C1-53599EB107DA");

            //Redirect to Inflo for Login
            $("a.inflologinlink")
                    .click(function () {
                        var ref_url = window.location.href;
                        $.ajax({
                            type: "POST",
                            crossDomain: true,
                            url: 'http://test.nexup.io/save_ref',
                            dataType: "json",
                            data: {ref: ref_url},
                            cache: false,
                            success: function (data) {
                            }
                        });
                        
                        
                         var infloUrl = 'https://inflo.io/Login.aspx?SkipApiLogin=1&ApiKey=' + $("#hndautoid").val() + '&RedirectUrl=http://test.nexup.io/inflo_login/';

                                var myWin = window.open(infloUrl, 'InfloLogin', 'height=700, width=600, left=300, top=100, resizable=no, scrollbars=yes, toolbar=no, menubar=no, location=no, directories=no, status=no');



                    });
			
			var mytoken = GetToken();
			
			
			
			
			//Redirect to Inflo for Login
        $(document)
            .on('click',
                ".share-btn",
                function (e) {
                    if($('.inflologinlink').length == 1){
                        e.preventDefault();
                        $('.inflologinlink').trigger('click');
                        return false;
                    }
                    $('#hidden_share_click').val('1');
                    if($(this).hasClass('noevents')){
                        return false;
                    }
                    
                var listId = $(this).attr("data-id");
				var userId = $("#hdnuserid").val();
                if (listId > 0) {
					
					var redirectUrl = window.location.href;
                    var infloShareUrl = 'https://inflo.io/ShareWithInflo/ShareWithInfloPage.aspx?SkipApiLogin=1&ApiKey=' + $("#hndautoid").val() + '&RedirectUrl=' + redirectUrl + "&UserId=" + userId+ "&ListId=" + listId;
                    var myWin = window.open(infloShareUrl,
                        'infloShareUrl',
                        'height=600, width=998, left=300, top=100, resizable=0, scrollbars=yes, toolbar=no, menubar=no, location=no, directories=no, status=no');
						
				
						
                } else {
                    alert("Invalid List");
                }
            });
			
			
        });


		
function GetInfloCredentials(apicode) {

    var objInput = {};
    objInput.url = 'https://developer.inflo.io/';
    objInput.apicode = apicode;

    $.ajax({
        type: "POST",
        crossDomain: true,
        contentType: "application/json; charset=utf-8",
        url: 'https://developer.inflo.io//api/account/GetInfloLoggedInUserDetails',
        dataType: "json",
        data: JSON.stringify(objInput),
        cache: false,
        success: function (data) {

            if (data !== null) {

                //console.log(data);
                $("#hdnuserid").val(data.data.UserId);
                $("#hdnaccesstoken").val(data.data.XAuthToken);
            }
        }
    });
}

function GetToken() {
    $.ajax({
        url: 'http://test.nexup.io/user/get_api_code',
        type: 'POST',
        data: {
            'operation': 'Get data'
        },
        success: function (res) {
            //var registerSuccess = getUrlVars("RegistrationSuccess");
            //if (registerSuccess == null || registerSuccess === "") {
            //Inflo API Code
            var infloApiCode = res; //getUrlVars("API_Code");

            if (infloApiCode != null && infloApiCode !== "" && infloApiCode !== undefined) {
                GetInfloCredentials(infloApiCode);
            }
            //}
        }
    });
}

//Get API Key
function GetEncryptedAPIKey(apikey) {
    //line added for the var that will have the result

    var objInput = {};
    objInput.Apikey = apikey;
    $.ajax({
        type: "POST",
        contentType: "application/json; charset=utf-8",
        url: 'https://developer.inflo.io/api/account/EncryptApiKey',
        dataType: "json",
        data: JSON.stringify(objInput),
        cache: false,
        success: function (data) {

            if (data !== null) {
                //line added to save ajax response in var result
                $("#hndautoid").val(data.data.ApiKey);
            }
        }
    });
}



// Read a page's GET URL variables and return them as an associative array.
function getUrlVars(param) {
    param = param.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + param + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}
