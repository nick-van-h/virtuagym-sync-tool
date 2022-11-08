var scriptFilename = 'functions.js';
var rootPath = ''; // Will contain the path to your file


$(document).ready(function () {
    //Set the root path of the project based on the script src path
    $('script').each(function () {
        var $script = $(this);

        if ($script.attr('src') && $script.attr('src').indexOf(scriptFilename) > -1) {
            rootPath = $script.attr('src').split(scriptFilename)[0];
            return false;
        }
    });


    console.log("Your document is ready!");
});