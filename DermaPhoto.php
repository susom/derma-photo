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
        $this->emDebug("Hello Newman");
        ?>
        <script>
            $(document).ready(function() {
                // console.log('hi');

                //Add javascript here...
                //	    $("a.fileuploadlink").text('Take photo');
                // Finish loading ImageMap

                setTimeout(function() {
                    // console.log('300');
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
                                // cbx.click().trigger('onclick');
                                var p = $(cbx).parent();
                                console.log('p', p);
                                p.trigger('click');

                                // imageMapEM.updateAreaList(imageMapEM, FU.currentData);
                            }

                        }
                        else {
                            // Make sure checkbox is NOT checked
                            if (checked) {
                                console.log('no image but checked - uncheck');

                                $('area[data-key="' + key + '"]').click();


                                // cbx.click().trigger('onclick');
                                // var p =  $(cbx).parent();
                                // console.log('p',p);
                                // p.trigger('onclick');
                                //                    p.triggerHandler('click');
                                // imageMapEM.updateAreaList(imageMapEM, FU.currentData);
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
                        console.log("trigger button");
                        $('#f1_upload_form .btn-fileupload').trigger('click');
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
                            var checked = $(this).is(":checked");
                            //imageMapEM.log ('is checked: ' + checked);
                            var selected = data.selected;
                            //imageMapEM.log ('is selected: ' + selected);
                            if (checked !== selected) {

                                //commenting this out as broken after upgrade to 10.5.1
                                //$(this).click().trigger('onclick');
                                //$(this).blur();
                            }
                        });

                        FU.currentData = data;

                        // Does field data.key exist?
                        var uploadField = $('input[name="' + data.key + '"]');
                        console.log('uploadField for ' + data.key, uploadField);
                        if (uploadField.length && data.selected) {
                            // Found the field
                            imageMapEM.log("Found " + data.key);

                            // TRIGGER UPLOAD
                            filePopUp(data.key, 0);
                            // PRESS FIND FILE BUTTON AUTOMATICALLY
                            $('input[name="myfile"]').trigger('click');
                            //HIDE the select file button
                            $('input[name="myfile"]').hide();
                            //hide the uplaode button
                            $('#f1_upload_form .btn-fileupload').hide();


                            // return;
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
