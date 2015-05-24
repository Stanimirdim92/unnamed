;(function (w, d, $, undefined) {
    'use strict';

    // Detect desktop browsers
    var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
    // Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)
    var isFirefox = typeof InstallTrigger !== 'undefined';   // Firefox 1.0+
    var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
    // At least Safari 3+: "[object HTMLElementConstructor]"
    var isChrome = !!window.chrome && !isOpera;              // Chrome 1+
    var isIE = /*@cc_on!@*/false || !!document.documentMode; // At least IE6

    // Place any jQuery/helper plugins in here.
    $(document).ready(function ($) {
        'use strict';

        /**
         * Create SEO captions/URLs for menus and content.
         */
        function fixSEOCaption (caption) {
            return caption.toLowerCase().replace(/(^\s+|[^a-zA-Z0-9 ]+|\s+$)/g,"").replace(/\s+/g, "-");
        }

        /**
         * Create DOM nodes with text, class and appends them to elementAppend
         */
        function showMessages(text, elementCreate, elementAppend, className) {
            var el = document.createElement(elementCreate);
            el.className += className;
            el.innerHTML = text;

            var frag = document.createDocumentFragment();
            frag.appendChild(el);

            $(elementAppend).append(frag).fadeIn(function () {
                setTimeout(function() {
                    $(elementCreate).fadeOut(1000);
                }, 6000);
            });
        }

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
         *  AJAX image upload
         */
        $("#imgajax").change(function (e) {
            var form = $("#content").submit();

            $.ajax({
                url: form.attr("action"),
                type: "POST",
                data: new FormData(form[0]),
                processData:false,
                contentType:  false,
                cache: false,
            }).done(function (result, request, headers) {
                var successHeaders = headers.getResponseHeader("successFiles");
                if (successHeaders !== null && successHeaders !== '') {
                    $.each(successHeaders.split(","), function(key, text) {
                        showMessages(text, "p", "header", "success");
                    });
                }

                var errorHeaders = headers.getResponseHeader("errorFiles");
                if (errorHeaders !== null && errorHeaders !== '') {
                    $.each(errorHeaders.split(","), function(key, text) {
                        showMessages(text, "p", "header", "error");
                    });
                }
            }).fail(function (result, request, headers) {
                console.log("Error:", result); //TODO must create a dialog popup
            });
            // clear the file input
            $("#imgajax").replaceWith($("#imgajax").val('').clone(true));
        });

        /*
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
        var urlSplit = window.location.href.toString().split(window.location.host)[1].split("/");
        $(".ajax-search").on("keyup", function () {
            var $search = $(".ajax-search").val();
            if ($.trim($search).length > 2) {
                $.ajax({
                    type: "GET",
                    url: "/admin/" + urlSplit[2] + "/search",
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
                                        $ul.append("<li class='table-cell'>&nbsp;</li>");
                                    } else {
                                        $ul.append("<li data-id ='"+$val["_id"]+"' class='table-cell'>"+$val[property]+"</li>");
                                    }
                                }
                            }
                            $("#results").append($ul);
                        });
                    }
                }).fail(function (a) {
                    console.log("Error:", a.responseText); //TODO must create a dialog popup
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