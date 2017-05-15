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
                            url: 'http://34.206.184.180/save_ref',
                            dataType: "json",
                            data: {ref: ref_url},
                            cache: false,
                            success: function (data) {
                            }
                        });
                        
                        
                         var infloUrl = 'https://demo.inflo.io/Login.aspx?SkipApiLogin=1&ApiKey=' + $("#hndautoid").val() + '&RedirectUrl=http://34.206.184.180/inflo_login/';

                                var myWin = window.open(infloUrl, 'InfloLogin', 'height=700, width=600, left=300, top=100, resizable=no, scrollbars=yes, toolbar=no, menubar=no, location=no, directories=no, status=no');



                    });

            //Inflo API Code
            var infloApiCode = getUrlVars()["API_Code"];

            if (infloApiCode !== null && infloApiCode != undefined) {
                GetInfloCredentials(infloApiCode);
            }
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

                console.log(data);
                $("#hdnuserid").val(data.data.UserId);
                $("#hdnaccesstoken").val(data.data.XAuthToken);
            }
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
function getUrlVars() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}
