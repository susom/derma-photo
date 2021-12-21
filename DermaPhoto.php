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
        $file_upload_page   = APP_PATH_SURVEY . "index.php?pid=" . PROJECT_ID . "&__passthru=".urlencode("DataEntry/file_upload.php");
        $file_empty_page    = APP_PATH_SURVEY . "index.php?pid=" . PROJECT_ID . "&__passthru=".urlencode("DataEntry/empty.php") . '&s=' . $_GET['s'];
        $formAction         = $file_upload_page.'&id='.rawurlencode($_GET['id']).'&event_id='.$_GET['event_id'].'&instance='.$_GET['instance'].'&s='.$_GET['s'];

        $file_upload_win2 = '<form autocomplete="new-password" id="form_file_upload" action="'.$formAction.'" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="return startUpload();" >
                                <div id="f1_upload_process"></div>
                                <div id="f1_upload_form" style="">
                                    <input name="myfile" type="file" size="40">
                                    <input name="myfile_base64" type="hidden">
                                    <input name="myfile_base64_edited" type="hidden" value="0">
                                    <input name="myfile_replace" type="hidden" value="0">
                                    <button class="btn btn-primaryrc btn-fileupload" onclick="uploadFilePreProcess();return false;"><i class="fas fa-upload"></i> Upload file</button>
                                </div>
                                <input type="hidden" id="field_name" name="field_name" value="">
                                <iframe id="upload_target" name="upload_target" src="'.$file_empty_page.'" ></iframe>
                            </form>';
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
                        console.log("no dialog no close!");
                        // FU.uploadClosed();
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
                            var selected = data.selected;
                        });

                        FU.currentData = data;

                        // Does field data.key exist?
                        var uploadField = $('input[name="' + data.key + '"]');
                        console.log('uploadField for ' + data.key, uploadField);

                        if (uploadField.length && data.selected) {
                            // Found the field
                            imageMapEM.log("Found " + data.key);

                            // TRIGGER UPLOAD
                            //MODIFY BEHAVIOUR HERE
                            // console.log("what is data", data);
                            noUIfilePopUp(data.key, 0);

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

            function noUIfilePopUp(field_name, signature, replace_version){
                // filePopUp(field_name, 0);
                minPopUpBackUp(field_name, 0);
            }

            function minPopUpBackUp(field_name, signature, replace_version) {
                // Reset value of hidden field used to determine if signature was signed
                $('#f1_upload_form input[name="myfile_base64_edited"]').val('0');
                $('#f1_upload_form input[name="myfile_replace"]').val(replace_version);

                var file_upload_win = '<?php echo js_escape($file_upload_win2) ?>';

                document.getElementById('file_upload').innerHTML = file_upload_win;
                document.getElementById('field_name').value = field_name+'-linknew';

                $('#f1_upload_form').show();
            }
        </script>
        <?php
    }

}
