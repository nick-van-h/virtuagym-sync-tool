$(function () {
    $("#settings-virtuagym button").click(function (ev) {
        ev.preventDefault() // cancel form submission

        //Get form data
        let data = {
            username: $("#settings-virtuagym input[name='username']").first().val(),
            password: $("#settings-virtuagym input[name='password']").first().val(),
            action: '',
        };

        if ($(this).attr("name") == "test") {
            data["action"] = "test";
        }

        if ($(this).attr("value") == "save") {
            data["action"] = "save";
        }

        //Do the AJAX request
        $.ajax({
            type: "POST",
            url: rootPath + "../../interfaces/web/setting_vgCredentials.php",
            data: data,
            beforeSend: function () { },
            success: function (response) {
                //Parse the received data
                try {
                    var data = JSON.parse(response);
                } catch {
                    console.log("Unable to parse JSON data: ");
                    console.log(response);
                    return;
                }

                //Update the value in the container span
                setFormStatusMessage($("#settings-virtuagym .status-message")[0], data["payload"]["statusmessage"]);
            },
        });
    });
});

function setFormStatusMessage(target, message) {
    console.log('Setting message: ' + message);
    console.log(target);
    $(target).html(message);
    $(target).removeClass('status-message--hidden');
    setTimeout(function () {
        $(target).addClass('status-message--hidden');
    }, 3000);
}