(function () {
    'use strict';
    
    angular.module('app.registro')
    .controller('IndexCtrl',['$scope','$http','$uibModal',IndexCtrl])
    .controller('InputCtrl',['$scope','$http','$uibModal',InputCtrl])
    .controller('InputEmail',['$scope','$http','$uibModalInstance',InputEmail])
    
    .controller('InputBusqueda',['$scope','$http','$uibModalInstance',InputBusqueda])
    .controller('InputImport',['$scope','$http',InputImport])
    .controller('InputRegistro',['$scope','$http','$uibModal',InputRegistro])
    .controller('InputModal',['$scope','$http','$uibModalInstance','field','rows_right','rows_left',InputModal])
    .controller('InputModalRegistro',['$scope','$http','$uibModalInstance','field',InputModalRegistro]);
    
    function InputBusqueda($scope,$http,$uibModalInstance)
    {
        $scope.cancel = function()
        {
            $uibModalInstance.close();
        }
    }
    function InputEmail($scope,$http,$uibModalInstance)
    {
        $scope.templates  = templates;
        $scope.readonly = false;
        $scope.form = {};
        $scope.status = false;
        $scope.show_progress = false;
        $scope.message = '';
       
        $scope.emails = emails;//['bernardo.cauich@gmail.com','ninis45@hotmail.com'];
        
        
        $scope.cancel = function()
        {
            $uibModalInstance.close();
        }
        $scope.send = function()
        {
            $scope.show_progress = true;
            var send_data = {
                emails:$scope.emails,
                template:$scope.form.template?$scope.form.template.slug:false,
                evento:evento,
                subject:$scope.form.subject,
                body:$scope.form.body
            };
            
            $http.post(SITE_URL+'admin/registros/send_email/',send_data).then(function(response){
                
                
                var result = response.data;
                
                $scope.message = result.message;
                $scope.show_progress = false;
                $scope.status = result.status;
                if(result.status)
                {
                    
                    $uibModalInstance.close();
                }
               
            });
            
        }
        $scope.$watch('form.template',function(newValue,oldValue){
            
            
            if(!newValue) return false;
            console.log(newValue);
            
            $scope.form.subject = newValue.subject;
            $scope.form.body = newValue.body;
            
            
            
        },true);
        
    }
    function IndexCtrl($scope,$http,$uibModal)
    {
        
        
        //$scope.emails = [];
        $scope.open_modal = function()
        {
            var modalInstance = $uibModal.open({
                            animation: true,
                            templateUrl: 'ModalPrepend.html',
                            controller: 'InputEmail',
                            //size: size,
                            resolve: {
                                /*emails:function()
                                {
                                    return $scope.emails;
                                }*/
                               
                                
                                
                            }
            });
        }
        $scope.open_busqueda = function()
        {
            var modalInstance = $uibModal.open({
                            animation: true,
                            templateUrl: 'ModalBusqueda.html',
                            controller: 'InputBusqueda',
                            //size: size,
                            resolve: {
                                /*emails:function()
                                {
                                    return $scope.emails;
                                }*/
                               
                                
                                
                            }
            });
        }
    }
    function InputImport($scope,$http)
    {
        
        $scope.disciplinas = disciplinas;
        $scope.disciplinas_right = disciplinas_right;
        
        
        $scope.add_items = function(item,disciplina,id_evento)
        {
           
            //var disciplina.id_evento = id_evento;
                    
            var index_right      = -1;//$scope.disciplinas_right.indexOf(disciplina);
            var send_data = {
                id_evento : item.id_evento,
                id_disciplina : item.id_disciplina,
                disciplina    : item.disciplina,
                id_centro     : item.id_centro,
                tipo          : item.tipo,
                rama          : item.rama 
            };
            $http.post(url_current,send_data).then(function(response){
                
                var result    = response.data,
                    status    = result.status,
                    exist     = false;
                
                /*$.each($scope.disciplinas_right,function(index,data){
                    
                    if(exist == false && data.nombre == disciplina.nombre)
                    {
                        
                        exist = true;
                    }    
                    
                });
                
                if(index_right == -1)
                {
                    $scope.disciplinas_right.push(disciplina);
                    
                    index_right = $scope.disciplinas_right.indexOf(disciplina);
                    $scope.disciplinas_right[index_right].participantes = [];
                   
                    
                }*/
                if(status)
                {
                    /*console.log(index_right);
                    $.each($scope.disciplinas_right,function(index,data){
                        
                        if(disciplina.nombre == data.nombre && disciplina.rama==data.rama)
                        {
                            index_right = index;
                           
                        }
                    });
                    
                    if(index_right == -1)
                    {
                        var new_disciplina = disciplina;
                        disciplina.participantes = [];
                        
                        disciplina.participantes.push(item);
                        $scope.disciplinas_right.push(disciplina);
                        
                        index_right = $scope.disciplinas_right.indexOf(disciplina);
                        console.log(disciplina);
                    }
                    else
                    {
                        $scope.disciplinas_right[index_right].participantes.push(item);
                    }*/
                     //console.log(index_right);
                    //$scope.disciplinas_right[index_right].participantes.push(item);
                    location.href = url_current;
                    //$scope.disciplinas_right[index_right].participantes.push(item);
                }
            });
        }
        
        
    }
    function InputModalRegistro($scope,$http,$uibModalInstance,field)
    {
        $scope.resources = foreigns;
        console.log(field);
        $scope.save_item = function(item)
        {
            //field.participante = item.participante;
            //field.module_id    = item.module_id;
            //field.munialum     = item.munialum;
            
            $.each(field,function(index,data){
                
                field[index] = item[index];
            });
            //field = item;
           
            //console.log(field);
            
            
            $uibModalInstance.close();
            
            
        }
        $scope.cancel = function()
        {
            $uibModalInstance.close();
        }
    }
    function InputModal($scope,$http,$uibModalInstance,field,rows_right,rows_left)
    {
        
        
        $scope.form = field? field:{};
        //$scope.form.slug = field;
        $scope.save_item = function()
        {
            rows_right.push($scope.form);
            
            $scope.form = {};
            $uibModalInstance.close();
        }
        
        $scope.cancel = function() {
                
                $uibModalInstance.close();
        }
        
        
    }
    //Form: Create/Edit
    function InputRegistro($scope,$http,$uibModal)
    {
        //$scope.selected = '';
        $scope.form = {};
        $scope.foreigns  = foreigns;
        $scope.add_part = function()
        {
             var modalInstance = $uibModal.open({
                            animation: true,
                            templateUrl: 'ModalPrepend.html',
                            controller: 'InputModalRegistro',
                            //size: size,
                            resolve: {
                                field:function()
                                {
                                    return $scope.form;
                                }
                               
                                
                                
                            }
            });
        }
        
    }
    function InputCtrl($scope,$http,$uibModal)
    {
        $scope.form = {};
        $scope.modules = modules;
        
        $scope.rows_left  = [];
        $scope.rows_right = rows_right?rows_right:[];
        
        $scope.edit_item = function(item)
        {
            var modalInstance = $uibModal.open({
                            animation: true,
                            templateUrl: 'ModalPrepend.html',
                            controller: 'InputModal',
                            //size: size,
                            resolve: {
                                field:function()
                                {
                                    return item;
                                },
                                rows_right:function()
                                {
                                    return $scope.rows_right;
                                },
                                rows_left: function () {
                                    return $scope.rows_left;
                                }
                               
                               
                                
                                
                            }
            });
        }
        $scope.add_item = function(field)
        {
            var item = {slug:false};
                item.slug  = field?field:'',
                item.grupo = field?'table':'extra';
            var modalInstance = $uibModal.open({
                            animation: true,
                            templateUrl: 'ModalPrepend.html',
                            controller: 'InputModal',
                            //size: size,
                            resolve: {
                                field:function()
                                {
                                    return item;
                                },
                                rows_right:function()
                                {
                                    return $scope.rows_right;
                                },
                                rows_left: function () {
                                    return $scope.rows_left;
                                }
                               
                               
                                
                                
                            }
            });
        }
        $scope.remove_item = function(index)
        {
            $scope.rows_right.splice(index,1);
        }
        $scope.$watch('module',function(newValue,oldValue){
            
            
            if(!newValue) return false;
            $scope.rows_left = newValue.rows;
            
            $.each($scope.modules,function(index,data){
                
                if(data.slug == newValue.slug)
                {
                    $scope.module = $scope.modules[index];
                }
            });
            //var index = $scope.modules.indexOf(newValue);
            //console.log(index);
        },true);
    }
    
})();