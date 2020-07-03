app.controller('SupplierController', function($scope, $http, API_URL) {
    // retrieve Supplier listing from API
    /*$http.get("http://192.168.1.134:8021/larajs/api/supplier")
        .success(function(response)
        {
           $scope.suppliers = response;
            //alert('ok');
        }).error(function(error,status,statusText)
        {
            //alert(statusText);
        });*/

    $http({
        url: "http://192.168.1.134:8021/larajs/api/supplierdata",
        method: "get",
    }).success(function(data, status, headers, config) {
        alert('SUCCESS');
        $scope.data = data;
    }).error(function(data, status, headers, config) {
        alert(headers);
        $scope.status = status;
    });

    // show modal Form
    $scope.toggle = function(modalstate, id) {
        $scope.modalstate = modalstate;
        switch(modalstate) {
            case 'add':
                $scope.form_title = "Add New Supplier";
                break;
            case 'edit':
                $scope.form_title = "Supplier Detail";
                $scope.id = id;
                $http.get(API_URL + 'supplier/' + id).success(function(response){
                    console.log(response);
                    $scope.supplier = response;
                });
                break;
            default:
                break;
        }
        console.log(id);
        $('#myModal').modal('show');
    }

    // save new supplier and update existing supplier
    $scope.save = function(modalstate, id) {
        var url = API_URL + "supplier";
        if (modalstate === 'edit') {
            url += "/" + id;
        }
        $http({
            method: 'POST',
            url: url,
            data: $.param($scope.supplier),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response){
            console.log(response);
            location.reload();
        }).error(function(response){
            console.log(response);
            alert('This is embarassing. An error has occured. Please check the log for details');
        });
    }

    // delete supplier record
    $scope.confirmDelete = function(id) {
        var isConfirmDelete = confirm('Are you sure you want this record?');
        if (isConfirmDelete) {
            $http({
                method: 'DELETE',
                url: API_URL + 'supplier/' + id
            }).success(function(data){
                console.log(data);
                location.reload();
            }).error(function(data){
                console.log(data);
                alert('Unable to delete');
            });
        } else {
            return false;
        }
    }
});