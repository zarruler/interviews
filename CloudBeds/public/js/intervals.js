$( document ).ready(function() {
    $("#new").click(function(){
        $("#addModal").addClass("is-active");
        $(".help.is-danger").html("");
    });

    $("button.modal-close, #cancel_btn").click(function(){
        $("#addModal").removeClass("is-active");
        $("#intervalForm").trigger("reset");
        $(".help.is-danger").html("");
    });

    $("#delete-all").click(function(){
        $.ajax({
            method: "GET",
            url: "/api/interval/deleteall"
        }).done(function( data ) {
            $("#content").html(
                $("#listTemplate").render(data)
            );
        });
    });

    $("#save").click(function(){
        $.ajax({
            method: "POST",
            url: "/api/interval/add",
            data:  $("#intervalForm").serialize()
        }).done(function( data ) {
            $("#content").html(
                $("#listTemplate").render(data)
            );
            $("#addModal").removeClass("is-active");
            $("#intervalForm").trigger("reset");
            $(".help.is-danger").html("");
        }).fail(function( data ) {
            $(".help.is-danger").html("");
            let errors = data.responseJSON.errors;
            Object.keys(errors).forEach(function(key,index) {
                $("#"+key+"_message").html(errors[key]);
            });
        });
    });

    $.ajax({
        method: "GET",
        url: "/api/interval/all"
    }).done(function( data ) {
        $("#content").html(
            $("#listTemplate").render(data)
        );
    });
});