// Add Record
function addRecord() {
    // get values
    var first_name = $("#first_name").val();
    //var last_name = $("#last_name").val();
    var numero = $("#numero").val();

    // Add record
    $.post("includes/agenda/ajax/addRecord.php", {
        first_name: first_name,
        //last_name: last_name,
        numero: numero
    }, function (data, status) {
        // close the popup
        $("#add_new_record_modal").modal("hide");

        // read records again
        readRecords();

        // clear fields from the popup
        $("#first_name").val("");
        //$("#last_name").val("");
        $("#numero").val("");
    });
}

// READ records
function readRecords() {
    $.get("includes/agenda/ajax/readRecords.php", {}, function (data, status) {
        $(".records_content").html(data);
    });
}


function DeleteUser(id) {
    var conf = confirm("Are you sure, do you really want to delete User?");
    if (conf == true) {
        $.post("includes/agenda/ajax/deleteUser.php", {
                id: id
            },
            function (data, status) {
                // reload Users by using readRecords();
                readRecords();
            }
        );
    }
}

function GetUserDetails(id) {
    // Add User ID to the hidden field for furture usage
    $("#hidden_user_id").val(id);
    $.post("includes/agenda/ajax/readUserDetails.php", {
            id: id
        },
        function (data, status) {
            // PARSE json data
            var user = JSON.parse(data);
            // Assing existing values to the modal popup fields
            $("#update_first_name").val(user.first_name);
            //$("#update_last_name").val(user.last_name);
            $("#update_numero").val(user.numero);
        }
    );
    // Open modal popup
    $("#update_user_modal").modal("show");
}

function UpdateUserDetails() {
    // get values
    var first_name = $("#update_first_name").val();
  //  var last_name = $("#update_last_name").val();
    var numero = $("#update_numero").val();

    // get hidden field value
    var id = $("#hidden_user_id").val();

    // Update the details by requesting to the server using ajax
    $.post("includes/agenda/ajax/updateUserDetails.php", {
            id: id,
            first_name: first_name,
            //last_name: last_name,
            numero: numero
        },
        function (data, status) {
            // hide modal popup
            $("#update_user_modal").modal("hide");
            // reload Users by using readRecords();
            readRecords();
        }
    );
}

$(document).ready(function () {
    // READ recods on page load
    readRecords(); // calling function
});
