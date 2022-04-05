let app = angular.module('app', []);


//контролеры и фабрики
app.controller('myCtrl', function($scope, myFactory){
    console.log('myCtrl');
    $scope.myFactory = myFactory;
    $scope.hi = 'Привет, мир!';
    // $scope.myBook = 'Моя книга!';
});
app.controller('secondCtrl', function($scope, myFactory){
    console.log('secondCtrl');
    $scope.myFactory = myFactory;
    $scope.hi = 'Привет, мир!';
    // $scope.myBooks = 'Мои книги!';
});

app.factory('myFactory', function(){
    return {
        hi: 'Привет, мир!'
    };
});


//Директива foo
app.directive('foo', function(){
    return {
        link: function(scope, element, attrs){
            console.log('This is my derective');
            element.on('click', function(){
                if (element.text() === 'Кликни на меня!') {
                    element.text('Ещё раз!');
                }else{
                    element.text('Кликни на меня!');
                }
            });
        }
    };
});

//фильтры

app.controller('thirdCtrl', function($scope){
    $scope.hello1 = 'Привет';
    $scope.hello2 = 'Привет,';
    $scope.hello3 = 'dsfklm';
});

app.filter('worldFilter', function(){
    return function(str){
        //console.log('str', str);
        let lastChar = str.slice(-1);

        if (lastChar === ',') {
            return str + 'мир!';
        } else if(lastChar === 'т'){
            return str + ', мир!';
        } else {
            return 'Поменяй значение!';
        }
    };
});

