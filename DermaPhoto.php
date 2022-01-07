<?php
namespace Stanford\DermaPhoto;

require_once "emLoggerTrait.php";

class DermaPhoto extends \ExternalModules\AbstractExternalModule {

    use emLoggerTrait;

    public function __construct() {
		parent::__construct();
		// Other code to run when object is instantiated
	}

	public function redcap_module_system_enable( $version ) {

	}


	public function redcap_module_project_enable( $version, $project_id ) {

	}


	public function redcap_module_save_configuration( $project_id ) {

	}

    public function redcap_survey_page_top($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance) {

        ?>
        <script>
            $(document).ready(function() {
                setTimeout(function() {
                    FU.init();
                }, 300);
            });

            var FU = {
                currentData: {},

                uploadClosed: function() {
                    console.log("Just closed!", FU.currentData);

                    // IS UPLOAD FIELD EMPTY?
                    var key = FU.currentData.key;

                    var uploadField = $('input[name="' + key + '"]');
                    if (uploadField.length) {
                        // Found the field
                        imageMapEM.log("Found " + key);

                        var cbx = $('input[type=checkbox][code="' + key + '"]');
                        var checked = cbx.is(':checked');

                        if (uploadField.val()) {
                            // We have a file - make sure it is still checked
                            if (!checked) {
                                console.log('image but not checked - check');
                                var p = $(cbx).parent();
                                console.log('p', p);
                                p.trigger('click');
                            }
                        }
                        else {
                            // Make sure checkbox is NOT checked
                            if (checked) {
                                console.log('no image but checked - uncheck');
                                $('area[data-key="' + key + '"]').click();
                            }

                        }
                    }
                    else {
                        imageMapEM.log("Cannot find field in modal close for " + key);
                    }

                },

                init: function() {
                    // Capture close event
                    $("#file_upload").on("dialogclose", function(event, ui) {
                        FU.uploadClosed();
                    });

                    //as soon as the photo has been uploaded, click the Upload button
                    $('body').on("change", 'input[name = "myfile"]', function() {
                        console.log("auto trigger upload button");
                        $('#f1_upload_form .btn-fileupload').trigger('click');

                        //set flag that it is uploading
                        window.uploadflag = true;
                    });


                    // debug to unhide checkbox
                    //$('div.choicevert').css({"display":"block"});

                    imageMapEM.updateAreaList = function(image, data) {
                        var field_name = $(image).attr('field');
                        var tr = $('tr[sq_id=' + field_name + ']');

                        imageMapEM.log("DATA:", data);

                        // DEFAULT CHECKBOX BEHAVIOR
                        $('input[type=checkbox][code="' + data.key + '"]', tr).each(function() {
                            imageMapEM.log('Found checkbox ' + data.key);
                            var checked     = $(this).is(":checked");
                            var selected    = data.selected;
                        });

                        FU.currentData = data;

                        // Does field data.key exist?
                        var uploadField = $('input[name="' + data.key + '"]');
                        // console.log('uploadField for ' + data.key, uploadField);

                        if (uploadField.length && data.selected) {
                            // Found the field
                            imageMapEM.log("Found " + data.key);

                            // TRIGGER UPLOAD
                            //open up default REDCAP upload dialog UI
                            filePopUp(data.key, 0);

                            //set uploading flag to false for every new modal
                            window.uploadflag = false;

                            //on input[myfile] click , native finder ui opens and entire document loses focus
                            //when finder dialog is closed (due to canceling or file selection) focus returns to document, act if canceled.
                            $('input[name="myfile"]').on("click", function(){
                                document.body.onfocus = function(){
                                    setTimeout(function(){
                                        if(!window.uploadflag){
                                            //dehighlight area
                                            $("area[data-key='"+data.key+"']").mapster('deselect');
                                            //auto trigger close button
                                            $("button.ui-dialog-titlebar-close").trigger("click");
                                        }
                                    }, 1000);
                                }
                            });

                            // PRESS FIND FILE BUTTON AUTOMATICALLY
                            $('input[name="myfile"]').trigger('click');

                            //HIDE the select file button
                            $('input[name="myfile"]').hide();
                            //hide the uplaode button
                            $('#f1_upload_form .btn-fileupload').hide();
                        }
                        else {
                            imageMapEM.log("Cannot find field for " + data.key);
                        }
                    };

                }
            }
        </script>
        <?php
    }

}
