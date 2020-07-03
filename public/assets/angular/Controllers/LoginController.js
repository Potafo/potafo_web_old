/*app.controller('LoginController', function($scope, $http, API_URL) {
$http({
    url: "http://192.168.1.134:8021/expodine_reports/api/expo_userexistcheck",
    method: "post",
}).success(function (data, status, headers, config) {
    alert('SUCCESS');
}).error(function (data, status, headers, config) {
    alert(headers);
});
});*/
/*

app.controller('LoginController', function($scope) {
    $scope.submitForm = function() {
        var url = 'http://192.168.1.134:8021/expodine_reports/api/expo_userexistcheck';
        if ($scope.theForm.$valid)
        {
            $http({
                method: 'POST',
                url: url,
                data: {"email": 'rere'},
            }).success(function (response) {
                alert('okkk');
            }).error(function (response) {
                alert('error');
            });
        }
        else
        {
            var email = document.getElementById('username').value;
            if(email==='')
            {
                alert('Username Required');
                $("#username").focus();
            }
        }
    };
});
*/

/*app.controller('LoginController', function($scope, $http, API_URL) {
         $scope.save = function (login) {
             var email = login.user_name;
             alert(email);
             var password = login.password;
             var url = 'http://192.168.1.134:8021/expodine_reports/api/expo_userexistcheck';
             $http({
                 method: 'POST',
                 url: url,
                 data: {"email": login.user_name,"password":login.password},
             }).success(function (response)
             {
                 alert('okkk');
                /!* if (response === 'Email Exist') {
                     alert('okkk');

                 }
                 else {
                     alert('yes');
                 }*!/
             }).error(function (response) {
                 alert('error');
             });
         }
    });*/

/*
//app.controller('LoginController', function($scope, $http, API_URL) {
    /!*    $http.get('http://192.168.1.134:8021/expodine_reports/api/mail')
     .then(function (response) {
     //                              jsonmsg =JSON.stringify(response.data);
     /!*  $scope.suppliers = response.data; *!/
     alert(response.data);

     var json_x = JSON.parse(result.data);
     alert(json_x.supplierName);
     },
     function (error) {
     alert('error');
     });*!/

*!/
   $scope.save = function (login) {

     stepsForm.prototype._validade = function() {
     // current question´s input
     var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

     var email =  document.getElementById( 'username').value;
     var password =  document.getElementById( 'password').value;
     var count =  document.getElementById( 'count').innerHTML;

     if(count === '1')
     {
     if (email === '')
     {
     this._showError('EMPTYSTR');
     return false;
     }

     if (email != '') {
     if (!emailReg.test(email))
     {
     this._showError('INVALIDEMAIL');
     return false;
     }
     }
     if(true)
     {
     //savedata(email);
     }

     }
     if(count === '2')
     {
     if (password === '') {
     this._showError('EMPTYSTR');
     return false;
     }
     }
     return true;
     }*!/
    /!* stepsForm.prototype._validade = function () {
     var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
     var email = document.getElementById('username').value;
     var password = document.getElementById('password').value;
     var count = document.getElementById('count').innerHTML;

     if (count === '1') {
     if (email === '') {
     this._showError('EMPTYSTR');
     return false;
     }
     if (email != '') {
     if (!emailReg.test(email)) {
     this._showError('INVALIDEMAIL');
     return false;
     }
     }
     if (true) {*!/
    /!*   var email = login.user_name;
     var password = login.password;
     var url = 'http://192.168.1.134:8021/expodine_reports/api/expo_emailexist';
     $http({
     method: 'POST',
     url: url,
     data: { "email" : login.user_name},
     }).success(function(response){
     if(response==='Email Exist')
     {
     alert('okkk');
     /!*  var url = 'http://192.168.1.134:8021/expodine_reports/api/expo_userexistcheck';
     $http({
     method: 'POST',
     url: url,
     data: { "email" : login.user_name,"password":login.password},
     }).success(function(response){
     alert(response);
     }).error(function(response){
     alert('error');
     });

     }
     else
     {
     alert('yes');
     }
     }).error(function(response){
     alert('error');
     });
     }
  }
     if (count === '2') {
     if (password === '') {
     this._showError('EMPTYSTR');
     return false;
     }
     }
     return true;
     }
    }

   $scope.save = function (login) {

}

});


*/
