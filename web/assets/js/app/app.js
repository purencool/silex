$(document).ready(function() {
console.log('We are in the app');
});
angular.module('myApp', []).config(function($interpolateProvider){
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
});
