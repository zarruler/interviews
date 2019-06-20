$( document ).ready(function() {
    $("#new").unbind().click(function(){
        closeClearModal();

        $("#save").unbind().click(function(){
            let data = $("#intervalForm").serialize();
            submitIntervalForm("/api/interval/add", "POST", data);
        });

        $("#addModal").addClass("is-active");
    });

    $("button.modal-close, #cancel_btn").unbind().click(function(){
        closeClearModal();
    });

    $("#delete-all").unbind().click(function(){
        $.ajax({
            method: "DELETE",
            url: "/api/interval/all"
        }).done(function( data ) {
            $("#content").html(
                $("#listTemplate").render(data)
            );
        });
    });


    $.ajax({
        method: "GET",
        url: "/api/interval/all"
    }).done(function( data ) {
        renderTemplate(data);
    });
});

/**
 * rendering list of intervals basing on provided data array
 *
 * @param data
 */
function renderTemplate(data)
{
    Promise.resolve().then(function () {
        $("#content").html(
            $("#listTemplate").render(data)
        );
    }).then(function () {
        updateEvents();
    })

}

function closeClearModal()
{
    $("#addModal").removeClass("is-active");
    $("#intervalForm").trigger("reset");
    $(".help.is-danger").html("");

}

/**
 * updating edit/delete buttons' events
 */
function updateEvents(){
    $(".delete-interval").unbind().click(function () {
        let tr = $(this).closest('tr');
        let interval = tr.find("td[data-field='start_date']").html() + ' - ' + tr.find("td[data-field='end_date']").html();
        if(confirm('Are you sure want delete interval ' + interval ))
        {
            $.ajax({
                method: "DELETE",
                url: "/api/interval/" + tr.data('id')
            }).done(function( data ) {
                tr.remove();
            });
        }
    });

    $(".edit-interval").unbind().click(function () {
        closeClearModal();
        let tr = $(this).closest('tr');
        let start_date = tr.find("td[data-field='start_date']").html();
        let end_date = tr.find("td[data-field='end_date']").html();
        let price =  tr.find("td[data-field='price']").html();

        $("input[name='start_date']").val(start_date);
        $("input[name='end_date']").val(end_date);
        $("input[name='price']").val(price);
        $("input[name='id']").val(tr.data('id'));

        $("#save").unbind().click(function(){
            let data = $("#intervalForm").serialize();
            submitIntervalForm("/api/interval/edit", "PUT", data);
        });

        $("#addModal").addClass("is-active");
    })
}

function submitIntervalForm(url, method, data)
{
    $.ajax({
        method: method,
        url: url,
        data: data
    }).done(function( data ) {

        renderTemplate(data);
        closeClearModal();

    }).fail(function( data ) {

        $(".help.is-danger").html("");
        let errors = data.responseJSON.errors;
        Object.keys(errors).forEach(function(key,index) {
            $("#"+key+"_message").html(errors[key]);
        });
    });

}