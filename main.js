$(document).ready(function () {

    $(".delete").click(function () {

        let name = $(this).data('name');
        let id = $(this).data('id');

        $("#modal-dialog-header").html(name);
        $("#delete-form-id").val(id);

        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false
        });

    });

    $(".delete_student").click(function () {

        let name_student = $(this).data('name');
        let id_student = $(this).data('id');

        $("#modal-dialog-header-student").html(name_student);
        $("#delete-form-id-1").val(id_student);

        $('#myModal_student').modal({
            backdrop: 'static',
            keyboard: false
        });

    });

    $(".delete-comment").click(function () {

        let name_comment = $(this).data('name');
        let username = $(this).data('id');

        $("#modal-dialog-header-2").html(name_comment + ' comment');
        $("#delete-form-id-2").val(username);

        $('#myModal_comment').modal({
            backdrop: 'static',
            keyboard: false
        });

    });

});
