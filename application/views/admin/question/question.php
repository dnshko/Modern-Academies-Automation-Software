<style type="text/css">
    .loading-overlay {
        display: none;
        position: absolute;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        z-index: 2;
        background: rgba(255,255,255,0.7);
    }
    .overlay-content {
        position: absolute;
        transform: translateY(-50%);
        -webkit-transform: translateY(-50%);
        -ms-transform: translateY(-50%);
        top: 50%;
        left: 0;
        right: 0;
        text-align: center;
        color: #555;
    }
    .div_load{position: relative;}

</style>
<script src="<?php echo base_url(); ?>backend/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>backend/js/ckeditor_config.js"></script>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-bus"></i> <?php echo $this->lang->line('question'); ?></h1>
    </section>
    <section class="content">
        <div class="row">

            <div class="col-md-12">
                <div class="box box-primary" id="route">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('question') . " " . $this->lang->line('bank'); ?></h3>
                        <?php
                        if ($this->rbac->hasPrivilege('question_bank', 'can_add')) {
                            ?>
                            <button class="btn btn-primary btn-sm pull-right question-btn" data-recordid="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') . " " . $this->lang->line('question'); ?></button>
                            <?php
                        }
                        ?>

                    </div>
                    <div class="box-body">
                        <div class="mailbox-controls">
                            <div class="pull-right">
                            </div>
                        </div>
                        <div class="mailbox-messages table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('question') . " " . $this->lang->line('bank'); ?></div>
                            <!-- <textarea class="form-control question" id="question" name="question"></textarea> -->
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('subject') ?></th>
                                        <th><?php echo $this->lang->line('question') ?></th>
                                        <th><?php echo $this->lang->line('answer') ?></th>

                                        <th class="pull-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($questionList as $subject_key => $subject_value) {
                                        ?>
                                        <tr>
                                            <td class="mailbox-name"> <?php echo $subject_value->name; ?></td>
                                            <td class="mailbox-name"> <?php echo readmorelink($subject_value->question); ?></td>
                                            <td class="mailbox-name"> <?php echo findOption($questionOpt, $subject_value->correct); ?></td>
                                            <td class="pull-right">
                                                <?php if ($this->rbac->hasPrivilege('question_bank', 'can_edit')) { ?>
                                                    <button type="button" data-placement="left" class="btn btn-default btn-xs question-btn-edit" data-toggle="tooltip" id="load" data-recordid="<?php echo $subject_value->id; ?>" title="<?php echo $this->lang->line('edit'); ?>" ><i class="fa fa-pencil"></i></button>
                                                    <?php
                                                }
                                                if ($this->rbac->hasPrivilege('question_bank', 'can_delete')) {
                                                    ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/question/delete/<?php echo $subject_value->id; ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $count++;
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>
</div>


<?php

function findOption($questionOpt, $find) {

    foreach ($questionOpt as $quet_opt_key => $quet_opt_value) {
        if ($quet_opt_key == $find) {
            return $quet_opt_value;
        }
    }
    return false;
}
?>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('question') ?></h4>
            </div>
            <form action="<?php echo site_url('admin/question/add'); ?>" method="POST" id="formsubject">
                <div class="modal-body">
                    <input type="hidden" name="recordid" value="0">
                    <div class="form-group">
                        <label for="subject_id"><?php echo $this->lang->line('subject') ?></label><small class="req"> *</small>

                        <select class="form-control" name="subject_id">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php
                            foreach ($subjectlist as $subject_key => $subject_value) {
                                ?>
                                <option value="<?php echo $subject_value['id']; ?>"><?php echo $subject_value['name']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <span class="text text-danger subject_id_error"></span>
                    </div>
                    <div class="form-group">

                        <label for="question"><?php echo $this->lang->line('question') ?></label><small class="req"> *</small>

                        <button class="btn btn-primary pull-right btn-xs" type="button" id="question" data-toggle="modal" data-location="question" data-target="#myimgModal"><i class="fa fa-plus"></i><?php echo $this->lang->line('add_image'); ?></button>

                        <textarea class="form-control ckeditor" id="question_textbox" name="question"></textarea>
                        <span class="text text-danger question_error"></span>
                    </div>
                    <?php
                    foreach ($questionOpt as $question_opt_key => $question_opt_value) {
                        ?>
                        <div class="form-group">
                            <label for="<?php echo $question_opt_key; ?>"><?php echo $this->lang->line('option_' . $question_opt_value); ?><?php if ($question_opt_value != 'E') { ?><small class="req"> *</small><?php } ?></label>

                            <button class="btn btn-primary pull-right btn-xs" type="button" data-location="<?php echo $question_opt_key; ?>" id="<?php echo $question_opt_key; ?>" data-toggle="modal" data-target="#myimgModal"><i class="fa fa-plus"></i><?php echo $this->lang->line('add_image'); ?></button>

                            <textarea class="form-control ckeditor" name="<?php echo $question_opt_key; ?>" id="<?php echo $question_opt_key . "_textbox"; ?>"></textarea>
                            <span class="text text-danger <?php echo $question_opt_key; ?>_error"></span>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="form-group">
                        <label for="subject_id"><?php echo $this->lang->line('answer') ?></label><small class="req"> *</small>

                        <select class="form-control" name="correct">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php
                            foreach ($questionOpt as $question_opt_key => $question_opt_value) {
                                ?>
                                <option value="<?php echo $question_opt_key; ?>"><?php echo $question_opt_value; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <span class="text text-danger correct_error"></span>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Saving..."><?php echo $this->lang->line('save') ?></button>
                </div>
        </div>
        </form>
    </div>
</div>


<!-- Modal -->
<div id="myimgModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title imgModal-title"><?php echo $this->lang->line('images'); ?> </h4>
            </div>
            <div class="modal-body imgModal-body pupscroll">
                <div class="form-group">
                    <input type="text" name="search_box" id="search_box" class="form-control" placeholder="Search..." />
                </div>
                <div class="div_load">

                    <div class="loading-overlay">
                        <div class="overlay-content"> <?php echo $this->lang->line('loading'); ?> </div>
                    </div>
                    <label class="total displaynone"></label>
                    <div class="row" id="media_div">

                    </div>
                    <div class="row" id="pagination">

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?> </button>
                <button type="button" class="btn btn-primary add_media"><?php echo $this->lang->line('add'); ?></button>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        })

        $('#myModal').on('hidden.bs.modal', function () {

            CKupdate();
            $(this)
                    .find("input,textarea,select")
                    .val('')
                    .end()
                    .find("input[type=checkbox], input[type=radio]")
                    .prop("checked", "")
                    .end();
        });
        $(document).on('click', '.question-btn', function () {
            var recordid = $(this).data('recordid');
            $('input[name=recordid]').val(recordid);
            $('#myModal').modal('show');

        });

        $(document).on('click', '.question-btn-edit', function () {
            var $this = $(this);
            var recordid = $this.data('recordid');
            $('input[name=recordid]').val(recordid);
            $.ajax({
                type: 'POST',
                url: baseurl + "admin/question/getQuestionByID",
                data: {'recordid': recordid},
                dataType: 'JSON',
                beforeSend: function () {
                    $this.button('loading');
                },
                success: function (data) {

                    if (data.status) {
                        $('select[name=subject_id]').val(data.result.subject_id);
                        $('select[name=correct]').val(data.result.correct);
                        CKEDITOR.instances['question_textbox'].setData(data.result.question);
                        CKEDITOR.instances['opt_a_textbox'].setData(data.result.opt_a);
                        CKEDITOR.instances['opt_b_textbox'].setData(data.result.opt_b);
                        CKEDITOR.instances['opt_c_textbox'].setData(data.result.opt_c);
                        CKEDITOR.instances['opt_d_textbox'].setData(data.result.opt_d);
                        CKEDITOR.instances['opt_e_textbox'].setData(data.result.opt_e);
                        $('#myModal').modal('show');
                    }
                    $this.button('reset');
                },
                error: function (xhr) { // if error occured
                    alert("Error occured.please try again");
                    $this.button('reset');
                },
                complete: function () {
                    $this.button('reset');
                }
            });

        });



    });

    $("form#formsubject").submit(function (e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.
        // $("span[id$='_error']").html("");
        var form = $(this);
        var url = form.attr('action');
        var submit_button = form.find(':submit');
        var post_params = form.serialize();

        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            dataType: "JSON", // serializes the form's elements.
            beforeSend: function () {
                $("[class$='_error']").html("");
                submit_button.button('loading');
            },
            success: function (data)
            {

                if (!data.status) {
                    $.each(data.error, function (index, value) {
                        var errorDiv = '.' + index + '_error';
                        $(errorDiv).html(value);
                    });
                } else {
                    location.reload();
                }
            },
            error: function (xhr) { // if error occured
                submit_button.button('reset');
                alert("Error occured.please try again");

            },
            complete: function () {
                submit_button.button('reset');
            }
        });


    });

</script>


<script>

    $(".ckeditor").each(function (_, ckeditor) {

        CKEDITOR.replace(ckeditor, {
            toolbar: 'Ques',
            customConfig: baseurl + '/backend/js/ckeditor_config.js'
        });
    });


    $(document).ready(function () {
        var target_textbox = "";
        $(document).on('click', '#question,#opt_a,#opt_b,#opt_c,#opt_d,#opt_e', function () {
            getImages(1);
        });
    });

    function getImages(page, query = "") {
        $.ajax({
            type: "POST",
            url: baseurl + 'admin/question/getimages',
            data: {page: page, query: query},
            dataType: "JSON", // serializes the form's elements.
            beforeSend: function () {
                $('.loading-overlay').css("display", "block");
            },
            success: function (data)
            {

                $('label.total').html("").html("<?php echo $this->lang->line('total_record'); ?>: " + data.count).css("display", "block");

                $('.imgModal-body #media_div').html("").html(data.page);
                $('.imgModal-body #pagination').html("").html(data.pagination);
                $('.loading-overlay').css("display", "none");
            },
            error: function (xhr) { // if error occured

                alert("Error occured.please try again");
                $('.loading-overlay').css("display", "none");
            },
            complete: function () {
                $('.loading-overlay').css("display", "none");
            }
        });
    }


    $(document).on('click', '.img_div_modal', function (event) {
        $('.img_div_modal div.fadeoverlay').removeClass('active');
        $(this).closest('.img_div_modal').find('.fadeoverlay').addClass('active');

    });

    $(document).on('click', '.add_media', function (event) {

        var content_html = $('div#media_div').find('.fadeoverlay.active').find('img').data('img');
        var is_image = $('div#media_div').find('.fadeoverlay.active').find('img').data('is_image');
        var content_name = $('div#media_div').find('.fadeoverlay.active').find('img').data('content_name');
        var content_type = $('div#media_div').find('.fadeoverlay.active').find('img').data('content_type');
        var vid_url = $('div#media_div').find('.fadeoverlay.active').find('img').data('vid_url');
        var content = "";


        if (typeof content_html !== "undefined") {
            if (is_image === 1) {
                content = '<img src="' + content_html + '">';
            }
            InsertHTML(content);
            $('#myimgModal').modal('hide');
        }

    });

    function InsertHTML(content_html) {
        var aaa = target_textbox + "_textbox";
        // Get the editor instance that we want to interact with.
        var editor = CKEDITOR.instances[aaa];
        console.log(editor);


        // Check the active editing mode.
        if (editor.mode == 'wysiwyg')
        {
            editor.insertHtml(content_html);
        } else
            alert('You must be in WYSIWYG mode!');
    }
    $('#myimgModal').on('shown.bs.modal', function (event) {
        button = $(event.relatedTarget);
        target_textbox = button.data('location');
        console.log(target_textbox);
    })

    $('.modal').on("hidden.bs.modal", function (e) { //fire on closing modal box

        if ($('.modal:visible').length) { // check whether parent modal is opend after child modal close
            $('body').addClass('modal-open'); // if open mean length is 1 then add a bootstrap css class to body of the page
        }
    });

    function CKupdate() {
        for (instance in CKEDITOR.instances) {
            // CKEDITOR.instances[instance].updateElement();
            CKEDITOR.instances[instance].setData('');
        }
    }

    $(document).on('keyup', '#search_box', function (event) {
        var query = $('#search_box').val();
        getImages(1, query);

    });

    $(document).on('click', '.page-link', function () {
        var page = $(this).data('page_number');
        var query = $('#search_box').val();
        getImages(page, query);
    });


</script>


