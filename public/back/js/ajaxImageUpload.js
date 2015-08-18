;(function (win, doc, $, undefined) {
    'use strict';

    var ajaxImageUpload = {

        /**
         * Attach event listeners
         */
        init: function () {

            $("button.upload").on("click", function () {
                $(".uploader-inline").show();
                $(".gallery-view").hide().find("figure.centered").remove();
            });

            $(".gallery").on("click", function() {
                ajaxImageUpload.showFiles();
            });

            $("button.media-modal-toggle").on("click", function () {
                $("#modal-imgupload").fadeToggle(850);
            });

            /**
             * AJAX image upload
             */
            $("#imgajax").change(function (e) {
                e.preventDefault();
                var $form = $("#content").submit();

                $.ajax({
                    url: $form.attr("action"),
                    type: "POST",
                    data: new FormData($form[0]),
                    processData:false,
                    contentType:  false,
                    cache: false,
                }).done(function (result, request, headers) {
                    var $resp = $.parseJSON(result);
                    ajaxImageUpload.setAjaxResponse($resp["successFiles"], "p", "header", "image-upload-message successFiles");
                    ajaxImageUpload.setAjaxResponse($resp["errorFiles"], "p", "header", "image-upload-message errorFiles");
                    ajaxImageUpload.showFiles();
                }).fail(function (result, request, headers) {
                    console.error("Error:", result); //TODO must create a dialog popup
                });
                // clear file input
                $("#imgajax").replaceWith($("#imgajax").val('').clone(true));
                return false;
            });
        },

        /**
         * Create DOM nodes with text, class and appends them to elementAppend
         */
        showMessages: function (text, elementCreate, elementAppend, className) {
            var el = doc.createElement(elementCreate);
            el.className += className;
            el.innerHTML = text;

            $(elementAppend).append(el).slideDown(1000, function () {
                setTimeout(function() {
                    $(elementCreate).slideUp(1000, function () {
                        $(this).fadeOut("slow", function () {
                            $(this).remove();
                         });
                    });
                }, 6000);
            });
        },

        /**
         * Show AJAX reponse
         */
        setAjaxResponse: function (response, elementCreate, elementAppend, className) {
            if (typeof response !== "undefined" && typeof response !== undefined) {
                $(elementAppend).append($("<div class='dinamicly-div-append-wrapper'></div>"));
                $.each(response, function(key, text) {
                    ajaxImageUpload.showMessages(text, elementCreate, 'div.dinamicly-div-append-wrapper', className);
                });
            }
        },

        /**
         * Gallery
         */
        showFiles: function () {
                $(".uploader-inline, .large-image").hide();
                $(".gallery-view").find("figure.centered").not(".large-image").remove();
                $(".gallery-view, .ajax-loader").show();
            $.get( "/admin/content/files", function (files) {
                $(".ajax-loader").hide();
                $(".large-image").show();
                $.each(files, function (key, file) {
                    $("div.image-grid").append("<figure class='centered'><img src='"+$.parseJSON(file["link"])+"' class='thumbnail' alt='"+$.parseJSON(file["filename"])+"' title='"+$.parseJSON(file["filename"])+"' /></figure>");
                });
                ajaxImageUpload.viewImage();
            });
        },

        viewImage: function () {
            $(".large-image").attr("src",$(".thumbnail").first().attr("src"));
            $(".thumbnail").on("click", function () {
                $(".large-image").attr("src",$(this).attr("src"));
            });
        }

    };

    $(doc).ready(function ($) {
        'use strict';

        ajaxImageUpload.init();

})(this, document, jQuery);