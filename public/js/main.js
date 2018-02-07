$( document ).ready(function() {
    $('#parseBtn').click(function (e) {
        e.preventDefault();

        $.ajax({
            url: '/parser',
            type: 'GET',
            beforeSend : function () {
                $('.loading').show();
            },
            success: function (result) {

                if ( result ) {

                    $('.loading').hide();
                    alert('Parsing error!')

                } else {
                    $('.loading').hide();
                }

            },
            errors: function () {

                $('.loading').hide();
                alert('Parsing error! 1');
            }
        });
    });
});