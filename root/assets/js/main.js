//JS for spinning logo

$(document).ready(function(){
    let isSpinningFast = false;
    $('#spin-logo').click(function(){
        if (!isSpinningFast) {
            $(this).addClass('spin-fast');
        } else {
            $(this).removeClass('spin-fast');
        }
        isSpinningFast = !isSpinningFast;
    });
}); 