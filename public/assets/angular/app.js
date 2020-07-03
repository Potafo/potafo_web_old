
var app = angular.module('SafaApp',[])
    .constant('API_URL','http://192.168.1.134:8021/inventory_new/api/')
    .run(function ($rootScope) {
        $rootScope.mySetting = 42;
   });
