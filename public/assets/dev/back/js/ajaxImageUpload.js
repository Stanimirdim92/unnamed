/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.12
 * @link       TBA
 */

;(function (win, doc, $, undefined) {
    /**
     * use strict doesn't play nice with IIS/.NET
     */
    'use strict';

    var request,
        ajaxImageUpload = {

        /**
         * Attach event listeners
         */
        init: function () {
            $("button.upload").on("click", function (event) {
                event.preventDefault();
                $(".uploader-inline").show();
                $(".gallery-view").hide().find("figure.centered").remove();
            });

            $(".gallery").on("click", function (event) {
                event.preventDefault();
                ajaxImageUpload.showFiles();
            });

            $("button.modal-toggle").on("click", function (event) {
                event.preventDefault();
                $("#modal-imgupload").fadeToggle(850);
            });

            ajaxImageUpload.abourtXHR(request);

            $("#imgajax").on("change", function (event) {
                event.preventDefault();
                $("#content").submit();

                /**
                 * Clear file input
                 */
                $("#imgajax").replaceWith($("#imgajax").val('').clone(true));

            });

            /**
             *Listen for submit event and prevent the request from refreshing the page
             */
            $("#content").on("submit", function (event) {
                event.preventDefault();
                request = $.ajax({
                    url: $(this).attr("action"),
                    type: "POST",
                    data: new FormData($(this)[0]),
                    processData: false,
                    contentType: false,
                    cache: false,
                });

                /**
                 * Callback for success response
                 */
                request.done(function (result, request, headers) {
                    ajaxImageUpload.showFiles();
                    ajaxImageUpload.setAjaxResponse($.parseJSON(result), "p", "header");
                });

                /**
                 * Callback for error response
                 */
                request.fail(function (error, textStatus, errorThrown) {
                    console.error(textStatus, errorThrown); //TODO must create a dialog popup
                });
            });
        },

        /**
         * Create DOM nodes with text, class and appends them to elementAppend
         */
        showMessages: function (text, elementCreate, elementAppend, className) {
            var el = doc.createElement(elementCreate);
            el.className += className;
            el.innerHTML = text;

            $(elementAppend).append(el).slideDown(1000, function (event) {
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
        setAjaxResponse: function (response, elementCreate, elementAppend) {
            if (typeof response !== "undefined" && typeof response !== undefined) {
                $(elementAppend).append($("<div class='dinamicly-div-append-wrapper'></div>"));
                $.each(response, function (className, text) {
                    if (text.length > 1) {
                        $.each(text, function (i, t) {
                            ajaxImageUpload.showMessages(t, elementCreate, 'div.dinamicly-div-append-wrapper', "image-upload-message " + className);
                        });
                    } else {
                        ajaxImageUpload.showMessages(text, elementCreate, 'div.dinamicly-div-append-wrapper', "image-upload-message " + className);
                    }
                });
            }
        },

        /**
         * Gallery view
         */
        showFiles: function () {
            $(".large-image").attr("src", "/assets/prod/front/img/default.png");
            $(".uploader-inline, .large-image").hide();
            $(".gallery-view").find("figure.centered").not(".large-image").remove();
            $(".gallery-view, .ajax-loader").show();

            ajaxImageUpload.abourtXHR(request);

            request = $.get("/admin/content/files", function (files) {
                $(".ajax-loader").hide();
                $(".large-image").show();
                $.each(files["files"], function (key, imgFile) {
                    $("div.image-grid").append("<figure class='centered'><i class='fa fa-times deleteimg'></i><img aria-checked='false' aria-label='"+imgFile["filename"]+"' src='"+imgFile["filelink"]+"' class='thumbnail' alt='"+imgFile["filename"]+"' title='"+imgFile["filename"]+"' /></figure>");
                });
                ajaxImageUpload.viewImage();
                ajaxImageUpload.deleteImage();
            });
        },

        /**
         * The big image on the right, next to thumbnails
         */
        viewImage: function () {
            $(".thumbnail").on("click", function (event) {
                event.preventDefault();
                $(".thumbnail").removeClass('image-border').attr("aria-checked", false);
                $(this).addClass('image-border').attr("aria-checked", true);
                $(".large-image").attr("src", $(this).attr("src"));
            });
            $(".large-image").attr("src", $(".thumbnail").first().attr("src"));
        },

        /**
         * Send a request to the server, where the script will check to see if the image exists
         * and if it does it will be deleted
         */
        deleteImage: function () {
            ajaxImageUpload.abourtXHR(request);

            $(".deleteimg").on("click", function (event) {
                request = $.post("/admin/content/deleteimage", {"img": $(this).next("img").attr("src")}, function () {
                    ajaxImageUpload.showFiles();
                });
            });
        },

        /**
         * Abort every previous AJAX request if new is made.
         * The method will abort on both client and server sides.
         */
        abourtXHR: function (xhr) {
            if (xhr && xhr.readyState !== 4) {
                xhr.abort();
                xhr = null;
            }
        }
    };

    $(doc).ready(function ($) {
        'use strict';
        ajaxImageUpload.init();
    });
})(this, document, jQuery);
