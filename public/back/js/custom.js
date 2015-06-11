;(function (w, d, $, undefined) {
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
             * TODO add loading image
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
                    ajaxImageUpload.getAjaxResponse($resp["successFiles"], "p", "header", "image-upload-message successFiles");
                    ajaxImageUpload.getAjaxResponse($resp["errorFiles"], "p", "header", "image-upload-message errorFiles");
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
            var el = document.createElement(elementCreate);
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
        getAjaxResponse: function (response, elementCreate, elementAppend, className) {
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


    /**
     * Create SEO captions/URLs for menus and content.
     */
    var fixSEOCaption = function (caption) {
        return caption.toLowerCase().replace(/(^\s+|[^a-zA-Z0-9 ]+|\s+$)/g,"").replace(/\s+/g, "-");
    };

    $(document).ready(function ($) {
        'use strict';

        ajaxImageUpload.init();

        /**
         * Custom dialog window for delete button
         */
        $(".dialog_delete").on("click", function (e) {
            e.preventDefault();
            $("#delete_" + $(this).attr("id")).fadeIn(650);
        });

        /*
         * Custom cancel button for delete dialog. Acts as a close button
         */
        $(".cancel").on("click", function (e) {
            e.preventDefault();
            $(".dialog_hide").fadeOut(650);
        });

        /**
         * replace: this is a menu caption => this-is-a-menu-caption, trim all white space and other characters
         */
        if ($("#titleLink").val() !== undefined) {
            $("#titleLink").val(fixSEOCaption($("#seo-caption").val()));
        }
        if ($("#menulink").val() !== undefined) {
            $("#menulink").val(fixSEOCaption($("#seo-caption").val()));
        }

        $("#seo-caption").on("keyup select change", function () {
            var $seolink = fixSEOCaption($("#seo-caption").val());
            if ($("#menulink").val() !== undefined) {
                $("#titleLink").val($seolink);
            } else {
                $("#menulink").val($seolink);
            }
        });

        /**
         * AJAX search form.
         * TODO: make the for loop more flexible so it can work with all kind of data
         */
        var $urlSplit = window.location.href.toString().split(window.location.host)[1].split("/");
        $(".ajax-search").on("keyup", function () {
            var $search = $(".ajax-search").val();
            if ($.trim($search).length > 2) {
                $.ajax({
                    type: "GET",
                    url: "/admin/" + $urlSplit[2] + "/search",
                    data: {"ajaxsearch": $search},
                    dataType: "json",
                    contentType: "application/json; charset=utf-8;",
                    cache: !1,
                }).done(function (result, request, headers) {
                    if (request === "success") {
                        $("#results").empty();
                        $.each(result.ajaxsearch, function (key, value) {
                            var $ul = $("<ul class='table-row'>");
                            var $val = $.parseJSON(value);
                            for (var property in $val) {
                                if ($val.hasOwnProperty(property)) {
                                    if ($val[property] === null || $val[property] === undefined || $val[property] === '') {
                                        // $ul.append("<li class='table-cell'>&nbsp;</li>");
                                        continue;
                                    } else {
                                        $ul.append("<li data-id ='"+$val["_id"]+"' class='table-cell'>"+$val[property]+"</li>");
                                    }
                                }
                            }
                            $("#results").append($ul);
                        });
                    }
                }).fail(function (error) {
                    console.log("Error:", error.responseText); //TODO must create a dialog popup
                });
                $("#results").show();
                $("#linked").hide();
            } else {
                $("#results").hide();
                $("#linked").show();
            }
        });
    });
})(this, document, jQuery);