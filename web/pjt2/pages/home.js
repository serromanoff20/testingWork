let ngRoute = angular.module('app', ['ngRoute']);
// ng-route
ngRoute.config(function($routeProvider){
    $routeProvider.when(
        '/', {
            template: '<h1>Домашняя страница</h1>'
        }
    )
    .when(
        '/posts', {
            templateUrl: 'posts.html'
        }
    )
});