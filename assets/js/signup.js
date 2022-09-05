$(document).ready(function(){

    $("#user_save").click(function(e){
        e.preventDefault();
        console.log(validateEmail($("#user_emailAddress").val()));
        if($("#user_emailAddress").val().toLowerCase() == $("#mail_confirm").val().toLowerCase()) {

        } else {
            console.log($("#same-email-alert"));
            $('#same-email-alert').css("display",'block');
        }
    });
    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }


/*    $(function () {
        $('[data-toggle="popover"]').popover();
    })
    $('#user_emailAddress').popover();*/
});




