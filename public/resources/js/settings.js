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

        if ($(this).attr("name") == "save") {
            data["action"] = "save";
        }

        //Do the AJAX request
        $.ajax({
            type: "POST",
            url: rootPath + "../../interfaces/web/setVgCredentials.php",
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

$(function () {
    $(".modal-close").click(function () {
        $(this).closest(".modal-outer").remove();
    });
});

$(function () {
    $("#settings-calendar button").click(function (ev) {
        ev.preventDefault() // cancel form submission
        //Get form data
        let selectedAgenda = $("#settings-calendar select[name='calendar-agendas']").first().val();
        let data = {
            agenda: selectedAgenda,
            action: '',
        };

        if ($(this).attr("name") == "google-connect") {
            //Google connect buttons are handled differently
            return;
        }

        if (selectedAgenda === null || selectedAgenda === "") {
            setFormStatusMessage($("#settings-calendar .status-message")[0], "Please select an agenda before saving");
            return;
        }

        if ($(this).attr("name") == "calendar-save") {
            data["action"] = "save";
        }

        //Do the AJAX request
        $.ajax({
            type: "POST",
            url: rootPath + "../../interfaces/web/setCalendarSettings.php",
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
                console.log(response);

                //Update the value in the container span
                setFormStatusMessage($("#settings-calendar .status-message")[0], data["payload"]["statusmessage"]);
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



$(function () {
    $("#manual-sync").click(function (ev) {
        ev.preventDefault() // cancel form submission

        //Show the loader
        $(".dynamic-loader").addClass('loading');

        //Do the AJAX request
        $.ajax({
            type: "POST",
            url: rootPath + "../../interfaces/web/doManualSync.php",
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

                //Reload the page
                window.location.reload(false);
            },
        });
    });
});


/**
 * Calendar set-up
 */

/**
 * Google Calendar
 */


$(document).ready(function () {
    $.ajax({
        type: "POST",
        url: rootPath + "../../interfaces/web/getGoogleOauthDetails.php",
        //data: data,
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

            console.log(data); //TMP

            const client = google.accounts.oauth2.initCodeClient({
                client_id: data["payload"]["client_id"],
                scope: 'https://www.googleapis.com/auth/calendar \ https://www.googleapis.com/auth/userinfo.email',
                ux_mode: 'redirect',
                redirect_uri: data["payload"]["redirect_uri"],
                state: data["payload"]["state_guid"],
                access_type: "offline"
            });

            $("#test-google-connect").click(function () {
                client.requestCode();
            });

            $("#settings-google-connect").click(function (ev) {
                ev.preventDefault() // cancel form submission
                client.requestCode();
            });
        },
    });
});

