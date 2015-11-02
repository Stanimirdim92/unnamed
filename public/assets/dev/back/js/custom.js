/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

;(function (win, doc, $, undefined) {
    'use strict';

    /**
     * Create SEO captions/URLs for menus and content.
     */
    function fixSEOCaption(caption) {
        return caption.toLowerCase().replace(/(^\s+|[^a-zA-Z0-9 ]+|\s+$)/g,"").replace(/\s+/g, "-");
    }

    function appendTranslationNode(elementAppend) {
        var $constKey =$("#newTranslationsConstName").val().toUpperCase();
        $(elementAppend).append($("<label><span>"+$constKey+"</span><input type='text' size='35' name='"+$constKey+"' placeholder='"+$constKey+"' required='required' value='"+$("#newTranslationsText").val()+"' /><button type='button' class='btn btn-sm delete translation_delete'><i class='fa fa-trash-o'></i></button></label><br>"));
    }

    $(doc).ready(function ($) {
        'use strict';

        /**
         * Custom dialog window for delete button
         */
        $("body").on("click", "button.dialog_delete", function (e) {
            e.preventDefault();
            $(".delete_" + $(this).attr("id")).fadeIn(650);
        });

        /*
         * Custom cancel button for delete dialog. Acts as a close button
         */
        $("body").on("click", ".cancel", function (e) {
            e.preventDefault();
            $(".dialog_hide").fadeOut(650);
        });

        $("body").on("click", ".add-translation", function (e) {
            e.preventDefault();
            appendTranslationNode("#translationsArray fieldset");
        });

        $("body").on("click", ".add-new-translation", function (e) {
            e.preventDefault();
            $(".toggle-translation-box").fadeToggle("slow");
        });

        $("body").on("click", ".translation_delete", function (e) {
            e.preventDefault();
            $(this).parent("label").remove();
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
            if ($("#menulink").val() !== undefined) {
                $("#menulink").val(fixSEOCaption($("#seo-caption").val()));
            } else {
                $("#titleLink").val(fixSEOCaption($("#seo-caption").val()));
            }
        });

        /**
         * AJAX search form.
         */
        var $urlSplit = win.location.href.toString().split(win.location.host)[1].split("/");
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
                    if (request === "success" && result.statusType === true) {
                        $("#results").empty();
                        $.each($.parseJSON(result.ajaxsearch), function (key, value) {
                            var $ul = $("<ul class='table-row'>");
                            for (var property in value) {
                                if (value.hasOwnProperty(property) && value[property] && property !== "id") {
                                    if (property !== "buttons") {
                                        $ul.append("<li class='table-cell'>"+value[property]+"</li>");
                                    } else {
                                        $ul.append(value["buttons"]);
                                    }
                                }
                            }
                            $("#results").append($ul);
                        });
                    } else {
                        $("#results").empty();
                        $("#results").append("<p>No matches</p>");
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
