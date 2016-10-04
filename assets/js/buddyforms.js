function bf_form_errors(){

    jQuery('input').removeClass('error');

    var errors = jQuery('.bf-alert-wrap ul li span');
    jQuery.each(errors, function (i, error) {
        var field_id = jQuery(error).attr('data-field-id');
        console.log(field_id);
        if(field_id === 'user_pass'){
            jQuery( '#' + field_id + '2').addClass('error');
        }
        jQuery( '#' + field_id).addClass('error');
    });
}

jQuery(document).ready(function () {

    var bf_submission_modal_content = jQuery( ".buddyforms-posts-content" );
    var bf_submission_modal = '';

    jQuery(document).on("click", '.bf-submission-modal', function (evt) {

        //console.log(evt);

        bf_submission_modal = jQuery( "#bf-submission-modal_" + jQuery(this).attr('data-id') );

        jQuery('.buddyforms-posts-container').html(bf_submission_modal);

        jQuery( "#bf-submission-modal_" + jQuery(this).attr('data-id') + " :input").attr("disabled", true);
        jQuery( "#bf-submission-modal_" + jQuery(this).attr('data-id')).show();
        return false;
    });


    jQuery(document).on("click", '.bf-close-submissions-modal', function (evt) {
        bf_submission_modal_content.find( '.bf_posts_' + jQuery(this).attr('data-id')).prepend(bf_submission_modal);
        jQuery('.buddyforms-posts-container').html(bf_submission_modal_content);
        jQuery( "#bf-submission-modal_" + jQuery(this).attr('data-id')).hide();
        return false;
    });

    jQuery(document).on("click", '.bf-alert-close', function (){
        jQuery('.bf-alert-wrap').remove();
    });

    bf_form_errors();

    jQuery( '.bf-garlic' ).garlic();

    jQuery(".bf-select2").select2({
        placeholder: "Select an option"
    });

    jQuery('.bf_datetime').datetimepicker({
        controlType: 'select',
        timeFormat: 'hh:mm tt'
    });

    var bf_status = jQuery('select[name=status]').val();

    if (bf_status == 'future') {
        jQuery('.bf_datetime_wrap').show();
    } else {
        jQuery('.bf_datetime_wrap').hide();
    }

    jQuery('select[name=status]').change(function () {
        var bf_status = jQuery(this).val();
        if (bf_status == 'future') {
            jQuery('.bf_datetime_wrap').show();
        } else {
            jQuery('.bf_datetime_wrap').hide();
        }
    });

    var buddyforms_form_content_val = jQuery('#buddyforms_form_content_val').html();
    jQuery('#buddyforms_form_content').html(buddyforms_form_content_val);

    //var clkBtn = "";
    //jQuery(document).on("click", '.bf-submit', function (evt) {
    //    clkBtn = evt.target.name;
    //});

    //    var submit_type = clkBtn;
    //    var form_name = event.target.id;
    //    var form_slug = form_name.split("buddyforms_form_")[1];
    //
    //    if (!jQuery('#' + form_name).valid()) {
    //        alert('Please check all errors before submitting the form!')
    //        return false;
    //    }
    //
    //    jQuery('#' + form_name + ' #submitted').val(submit_type);
    //
    //    if (jQuery('#' + form_name + ' input[name="ajax"]').val() != 'off') {
    //
    //        event.preventDefault();
    //
    //        var FormData = jQuery('#' + form_name).serialize();
    //
    //        jQuery.ajax({
    //            type: 'POST',
    //            dataType: "json",
    //            url: ajaxurl,
    //            data: {"action": "buddyforms_ajax_process_edit_post", "data": FormData},
    //            beforeSend: function () {
    //                jQuery('.the_buddyforms_form_' + form_slug + ' .form_wrapper .bf_modal').show();
    //            },
    //            success: function (data) {
    //
    //                // console.log(data);
    //
    //                jQuery('.the_buddyforms_form_' + form_slug + ' .form_wrapper .bf_modal').hide();
    //
    //                jQuery.each(data, function (i, val) {
    //                    switch (i) {
    //                        case 'form_notice':
    //                            jQuery('#form_message_' + form_slug).html(val);
    //                            break;
    //                        case 'form_remove':
    //                            jQuery('.the_buddyforms_form_' + form_slug + ' .form_wrapper').remove();
    //                            break;
    //                        case 'form_actions':
    //                            jQuery('.the_buddyforms_form_' + form_slug + ' .form-actions').html(val);
    //                            break;
    //                        default:
    //                            jQuery('input[name="' + i + '"]').val(val);
    //                    }
    //                    jQuery('#recaptcha_reload').trigger('click');
    //
    //                });
    //                jQuery('#' + form_name).valid();
    //            },
    //            error: function (request, status, error) {
    //                jQuery('.the_buddyforms_form_' + form_slug + ' .form_wrapper .bf_modal').hide();
    //                alert(request.responseText);
    //            }
    //        });
    //
    //        return false;
    //    }
    //    return true;
    //});

    jQuery(document).on("click", '.bf_delete_post', function (event) {
        var post_id = jQuery(this).attr('id');

        if (confirm('Delete Permanently')) {
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {"action": "buddyforms_ajax_delete_post", "post_id": post_id},
                success: function (data) {
                    if (isNaN(data)) {
                        alert(data);
                    } else {
                        var id = "#bf_post_li_";
                        var li = id + data;
                        li = li.replace(/\s+/g, '');
                        jQuery(li).remove();
                    }
                },
                error: function (request, status, error) {
                    alert(request.responseText);
                }
            });
        } else {
            return false;
        }
        return false;
    });

});
